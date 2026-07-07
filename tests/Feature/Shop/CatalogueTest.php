<?php

namespace Tests\Feature\Shop;

use App\Models\Catalogue\Category;
use App\Models\Catalogue\Product;
use App\Models\Catalogue\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogueTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_homepage_lists_active_products_only(): void
    {
        $visible = Product::factory()->create(['name' => 'Voile visible', 'is_active' => true]);
        $hidden = Product::factory()->create(['name' => 'Voile caché', 'is_active' => false]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee($visible->name);
        $response->assertDontSee($hidden->name);
    }

    public function test_the_catalogue_page_paginates_products(): void
    {
        Product::factory()->count(15)->create(['is_active' => true]);

        $response = $this->get('/catalogue');

        $response->assertOk();
        $response->assertViewHas('products', fn ($products) => $products->count() === 12 && $products->total() === 15);
    }

    public function test_an_inactive_product_returns_a_404(): void
    {
        $product = Product::factory()->create(['is_active' => false]);

        $this->get("/produits/{$product->slug}")->assertNotFound();
    }

    public function test_a_soft_deleted_product_returns_a_404(): void
    {
        $product = Product::factory()->create();
        $product->delete();

        $this->get("/produits/{$product->slug}")->assertNotFound();
    }

    public function test_the_product_page_displays_active_variants_with_their_price(): void
    {
        $product = Product::factory()->create(['price' => 10000]);
        $variant = ProductVariant::factory()->for($product)->create([
            'attributes' => ['taille' => 'M', 'couleur' => 'Noir'],
            'price_override' => null,
            'stock' => 5,
            'is_active' => true,
        ]);
        $inactiveVariant = ProductVariant::factory()->for($product)->create(['is_active' => false]);

        $response = $this->get("/produits/{$product->slug}");

        $response->assertOk();
        $response->assertSee('Taille: M');
        $response->assertDontSee($inactiveVariant->sku);
    }

    public function test_the_category_page_only_lists_products_from_that_category(): void
    {
        $category = Category::factory()->create(['is_active' => true]);
        $otherCategory = Category::factory()->create(['is_active' => true]);

        $inCategory = Product::factory()->for($category)->create(['name' => 'Produit A', 'is_active' => true]);
        $inOther = Product::factory()->for($otherCategory)->create(['name' => 'Produit B', 'is_active' => true]);

        $response = $this->get("/categories/{$category->slug}");

        $response->assertOk();
        $response->assertSee($inCategory->name);
        $response->assertDontSee($inOther->name);
    }

    public function test_an_inactive_category_returns_a_404(): void
    {
        $category = Category::factory()->create(['is_active' => false]);

        $this->get("/categories/{$category->slug}")->assertNotFound();
    }

    public function test_the_category_page_lists_its_active_child_categories(): void
    {
        $parent = Category::factory()->create(['is_active' => true]);
        $child = Category::factory()->create(['parent_id' => $parent->id, 'is_active' => true]);

        $response = $this->get("/categories/{$parent->slug}");

        $response->assertOk();
        $response->assertSee($child->name);
    }
}
