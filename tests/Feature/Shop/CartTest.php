<?php

namespace Tests\Feature\Shop;

use App\Exceptions\CartException;
use App\Models\Cart\Cart;
use App\Models\Catalogue\Product;
use App\Models\Catalogue\ProductVariant;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    private string $guestSessionId;

    /**
     * Le client de test ne renvoie pas automatiquement les cookies entre deux
     * appels séparés, contrairement à un navigateur — il faut donc forcer
     * explicitement le même id de session sur chaque appel pour simuler un
     * seul visiteur qui navigue sur plusieurs pages. withCookie() suffit : le
     * client de test chiffre lui-même la valeur avant l'envoi (le préfixe/
     * chiffrement ne doit pas être fait à la main, sinon double chiffrement).
     */
    private function fixedGuestSessionId(): string
    {
        return $this->guestSessionId ??= Str::random(40);
    }

    private function asGuestSession(): static
    {
        return $this->withCookie(config('session.cookie'), $this->fixedGuestSessionId());
    }

    public function test_adding_a_product_that_requires_a_variant_without_selecting_one_fails(): void
    {
        $product = Product::factory()->create();
        ProductVariant::factory()->for($product)->create();

        $response = $this->post(route('shop.cart.store'), [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $response->assertSessionHasErrors('variant_id');
        $this->assertDatabaseCount('cart_items', 0);
    }

    public function test_adding_a_product_without_variants_works_without_selecting_one(): void
    {
        $product = Product::factory()->create();

        $response = $this->post(route('shop.cart.store'), [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $response->assertRedirect(route('shop.cart'));
        $this->assertDatabaseHas('cart_items', ['product_id' => $product->id, 'quantity' => 2]);
    }

    public function test_a_guest_cart_persists_across_requests_in_the_same_session(): void
    {
        $product = Product::factory()->create();

        $this->asGuestSession()->post(route('shop.cart.store'), ['product_id' => $product->id, 'quantity' => 1]);

        $response = $this->asGuestSession()->get(route('shop.cart'));

        $response->assertOk();
        $response->assertSee($product->name);
    }

    public function test_adding_the_same_product_twice_increments_the_quantity_instead_of_duplicating_the_line(): void
    {
        $product = Product::factory()->create();

        $this->asGuestSession()->post(route('shop.cart.store'), ['product_id' => $product->id, 'quantity' => 1]);
        $this->asGuestSession()->post(route('shop.cart.store'), ['product_id' => $product->id, 'quantity' => 2]);

        $this->assertDatabaseCount('cart_items', 1);
        $this->assertDatabaseHas('cart_items', ['product_id' => $product->id, 'quantity' => 3]);
    }

    public function test_adding_more_than_the_available_stock_is_rejected(): void
    {
        $product = Product::factory()->create();
        $variant = ProductVariant::factory()->for($product)->create(['stock' => 2]);

        $response = $this->post(route('shop.cart.store'), [
            'product_id' => $product->id,
            'variant_id' => $variant->id,
            'quantity' => 5,
        ]);

        $response->assertSessionHasErrors('variant_id');
        $this->assertDatabaseCount('cart_items', 0);
    }

    public function test_two_independent_guest_sessions_never_see_each_others_cart(): void
    {
        $service = app(CartService::class);
        $product = Product::factory()->create();

        $cartA = $service->cartForSession('session-a');
        $cartB = $service->cartForSession('session-b');

        $service->addItem($cartA, $product, null, 1);

        $this->assertNotSame($cartA->id, $cartB->id);
        $this->assertCount(1, $cartA->fresh()->items);
        $this->assertCount(0, $cartB->fresh()->items);
    }

    public function test_a_visitor_cannot_modify_another_visitors_cart_item_by_guessing_its_id(): void
    {
        $service = app(CartService::class);
        $product = Product::factory()->create();

        $otherCart = $service->cartForSession('someone-elses-session');
        $otherItem = $service->addItem($otherCart, $product, null, 1);

        // Un autre visiteur (session neuve dans ce test) tente de la supprimer via l'URL.
        $response = $this->delete(route('shop.cart.destroy', $otherItem));

        $response->assertForbidden();
        $this->assertDatabaseHas('cart_items', ['id' => $otherItem->id]);
    }

    public function test_guest_cart_is_merged_into_the_account_at_login(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $product = Product::factory()->create();

        // Ajout au panier invité d'abord, dans la même session « navigateur » que la connexion ci-dessous.
        $this->asGuestSession()->post(route('shop.cart.store'), ['product_id' => $product->id, 'quantity' => 1]);

        $this->asGuestSession()->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $userCart = Cart::where('user_id', $user->id)->firstOrFail();
        $this->assertCount(1, $userCart->items);
        $this->assertSame(0, Cart::whereNull('user_id')->count());
    }

    public function test_merging_a_guest_cart_caps_the_merged_quantity_at_available_stock(): void
    {
        $service = app(CartService::class);
        $user = User::factory()->create();
        $product = Product::factory()->create();
        $variant = ProductVariant::factory()->for($product)->create(['stock' => 3]);

        $guestCart = $service->cartForSession('guest-session');
        $service->addItem($guestCart, $product, $variant, 2);

        $userCart = Cart::create(['user_id' => $user->id]);
        $service->addItem($userCart, $product, $variant, 2);

        $merged = $service->mergeGuestCartIntoUser('guest-session', $user);

        $this->assertSame(3, $merged->items->first()->quantity);
    }

    public function test_removing_an_item_deletes_it_from_the_cart(): void
    {
        $service = app(CartService::class);
        $product = Product::factory()->create();
        $cart = $service->cartForSession('remove-session');
        $item = $service->addItem($cart, $product, null, 1);

        $service->removeItem($item);

        $this->assertDatabaseMissing('cart_items', ['id' => $item->id]);
    }

    public function test_setting_quantity_to_zero_removes_the_item(): void
    {
        $service = app(CartService::class);
        $product = Product::factory()->create();
        $cart = $service->cartForSession('zero-session');
        $item = $service->addItem($cart, $product, null, 1);

        $service->updateQuantity($item, 0);

        $this->assertDatabaseMissing('cart_items', ['id' => $item->id]);
    }

    public function test_cart_total_reflects_current_price_not_a_stored_snapshot(): void
    {
        $service = app(CartService::class);
        $product = Product::factory()->create(['price' => 1000]);
        $cart = $service->cartForSession('price-session');
        $service->addItem($cart, $product, null, 2);

        $product->update(['price' => 1500]);

        $this->assertSame(3000.0, $cart->fresh('items.product')->total());
    }

    public function test_adding_an_inactive_product_is_rejected(): void
    {
        $this->expectException(CartException::class);

        $service = app(CartService::class);
        $product = Product::factory()->create(['is_active' => false]);
        $cart = $service->cartForSession('inactive-session');

        $service->addItem($cart, $product, null, 1);
    }
}
