<?php

namespace Tests\Feature\Shop;

use App\Models\Catalogue\Category;
use App\Models\Catalogue\Product;
use App\Models\Catalogue\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogueFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_search_term_matches_product_name(): void
    {
        $match = Product::factory()->create(['name' => 'Voile en soie doré']);
        $other = Product::factory()->create(['name' => 'Sac à main bordeaux']);

        $response = $this->get('/catalogue?q=soie');

        $response->assertOk();
        $response->assertSee($match->name);
        $response->assertDontSee($other->name);
    }

    public function test_the_search_term_matches_category_name(): void
    {
        $category = Category::factory()->create(['name' => 'Parfums']);
        $match = Product::factory()->for($category)->create(['name' => 'Essence florale']);
        $other = Product::factory()->create(['name' => 'Collant noir']);

        $response = $this->get('/catalogue?q=parfum');

        $response->assertOk();
        $response->assertSee($match->name);
        $response->assertDontSee($other->name);
    }

    public function test_a_like_wildcard_in_the_search_term_is_treated_as_literal_text(): void
    {
        Product::factory()->create(['name' => 'Voile classique']);

        $response = $this->get('/catalogue?'.http_build_query(['q' => '%_']));

        $response->assertOk();
        $response->assertSee('Aucun produit ne correspond à ces critères.');
    }

    public function test_the_category_filter_only_returns_products_from_that_category(): void
    {
        $category = Category::factory()->create();
        $other = Category::factory()->create();

        $match = Product::factory()->for($category)->create(['name' => 'Produit A']);
        $excluded = Product::factory()->for($other)->create(['name' => 'Produit B']);

        $response = $this->get('/catalogue?category_id='.$category->id);

        $response->assertOk();
        $response->assertSee($match->name);
        $response->assertDontSee($excluded->name);
    }

    public function test_an_inactive_category_id_is_silently_ignored_as_a_filter(): void
    {
        $inactiveCategory = Category::factory()->create(['is_active' => false]);
        $visible = Product::factory()->create(['name' => 'Produit visible']);

        $response = $this->get('/catalogue?category_id='.$inactiveCategory->id);

        $response->assertOk();
        $response->assertSee($visible->name);
    }

    public function test_the_price_range_filter_excludes_products_outside_the_bounds(): void
    {
        $cheap = Product::factory()->create(['name' => 'Produit pas cher', 'price' => 1000]);
        $mid = Product::factory()->create(['name' => 'Produit dans la fourchette', 'price' => 5000]);
        $expensive = Product::factory()->create(['name' => 'Produit cher', 'price' => 20000]);

        $response = $this->get('/catalogue?min_price=2000&max_price=10000');

        $response->assertOk();
        $response->assertDontSee($cheap->name);
        $response->assertSee($mid->name);
        $response->assertDontSee($expensive->name);
    }

    public function test_an_invalid_price_range_fails_validation(): void
    {
        $response = $this->get('/catalogue?min_price=100&max_price=10');

        $response->assertSessionHasErrors('max_price');
    }

    public function test_the_in_stock_filter_excludes_products_with_no_stock_left(): void
    {
        $inStock = Product::factory()->create(['name' => 'Produit disponible']);
        ProductVariant::factory()->for($inStock)->create(['stock' => 5, 'is_active' => true]);

        $outOfStock = Product::factory()->create(['name' => 'Produit épuisé']);
        ProductVariant::factory()->for($outOfStock)->create(['stock' => 0, 'is_active' => true]);

        $response = $this->get('/catalogue?in_stock=1');

        $response->assertOk();
        $response->assertSee($inStock->name);
        $response->assertDontSee($outOfStock->name);
    }

    public function test_filters_combine_on_the_category_page(): void
    {
        $category = Category::factory()->create();
        $match = Product::factory()->for($category)->create(['name' => 'Voile léger', 'price' => 5000]);
        $wrongPrice = Product::factory()->for($category)->create(['name' => 'Voile luxe', 'price' => 50000]);

        $response = $this->get('/categories/'.$category->slug.'?q=voile&max_price=10000');

        $response->assertOk();
        $response->assertSee($match->name);
        $response->assertDontSee($wrongPrice->name);
    }

    public function test_a_category_id_in_the_query_string_cannot_escape_the_category_page_scope(): void
    {
        $category = Category::factory()->create();
        $otherCategory = Category::factory()->create();

        $inCategory = Product::factory()->for($category)->create(['name' => 'Produit dans la catégorie']);
        $inOther = Product::factory()->for($otherCategory)->create(['name' => 'Produit hors catégorie']);

        $response = $this->get('/categories/'.$category->slug.'?category_id='.$otherCategory->id);

        $response->assertOk();
        $response->assertSee($inCategory->name);
        $response->assertDontSee($inOther->name);
    }

    public function test_an_ajax_request_returns_only_the_product_grid_partial(): void
    {
        Product::factory()->create(['name' => 'Produit du fragment']);

        $response = $this->get('/catalogue', ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertOk();
        $response->assertSee('Produit du fragment');
        $response->assertDontSee('<html', false);
    }
}
