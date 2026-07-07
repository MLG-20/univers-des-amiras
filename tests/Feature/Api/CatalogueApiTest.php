<?php

namespace Tests\Feature\Api;

use App\Models\Catalogue\Category;
use App\Models\Catalogue\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogueApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_categories_are_publicly_listed(): void
    {
        Category::factory()->create(['name' => 'Visible', 'is_active' => true]);
        Category::factory()->create(['name' => 'Cachée', 'is_active' => false]);

        $response = $this->getJson('/api/v1/categories');

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['name' => 'Visible']);
    }

    public function test_inactive_products_are_not_listed_or_shown(): void
    {
        $active = Product::factory()->create(['is_active' => true]);
        $inactive = Product::factory()->create(['is_active' => false]);

        $this->getJson('/api/v1/products')
            ->assertOk()
            ->assertJsonMissing(['id' => $inactive->id])
            ->assertJsonFragment(['id' => $active->id]);

        $this->getJson("/api/v1/products/{$inactive->id}")->assertNotFound();
        $this->getJson("/api/v1/products/{$active->id}")->assertOk();
    }

    public function test_soft_deleted_products_are_not_reachable_via_the_api(): void
    {
        $product = Product::factory()->create();
        $product->delete();

        $this->getJson("/api/v1/products/{$product->id}")->assertNotFound();
    }

    public function test_a_client_can_obtain_an_api_token_and_use_it(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);

        $response = $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'password',
            'device_name' => 'phpunit',
        ]);

        $response->assertOk()->assertJsonStructure(['token']);

        $token = $response->json('token');

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/user')
            ->assertOk()
            ->assertJsonFragment(['id' => $user->id]);
    }

    public function test_api_login_fails_with_a_generic_message_and_is_rate_limited(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);

        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/v1/login', [
                'email' => $user->email,
                'password' => 'wrong-password',
                'device_name' => 'phpunit',
            ])->assertStatus(422);
        }

        $response = $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'password',
            'device_name' => 'phpunit',
        ]);

        $response->assertStatus(422);
    }
}
