<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\Catalogue\ProductResource\Pages\CreateProduct;
use App\Models\Admin;
use App\Models\Catalogue\Category;
use App\Models\Catalogue\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ProductResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_product_with_variants_and_an_image_end_to_end(): void
    {
        Storage::fake('public');
        $this->actingAs(Admin::factory()->create(), 'admin');

        $category = Category::factory()->create();

        Livewire::test(CreateProduct::class)
            ->fillForm([
                'name' => 'Voile brodé',
                'slug' => 'voile-brode',
                'category_id' => $category->id,
                'price' => 15000,
                'is_active' => true,
                'images' => [
                    ['path' => [UploadedFile::fake()->image('voile.jpg')], 'is_primary' => true, 'position' => 0],
                ],
                'variants' => [
                    ['sku' => 'VOI-001-M', 'attributes' => ['taille' => 'M'], 'stock' => 10, 'is_active' => true],
                ],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $product = Product::where('slug', 'voile-brode')->firstOrFail();

        $this->assertSame($category->id, $product->category_id);
        $this->assertCount(1, $product->variants);
        $this->assertSame('VOI-001-M', $product->variants->first()->sku);
        $this->assertCount(1, $product->images);
        Storage::disk('public')->assertExists($product->images->first()->path);
    }

    public function test_uploading_a_non_image_file_disguised_as_a_product_image_is_rejected(): void
    {
        Storage::fake('public');
        $this->actingAs(Admin::factory()->create(), 'admin');

        $category = Category::factory()->create();

        Livewire::test(CreateProduct::class)
            ->fillForm([
                'name' => 'Produit malveillant',
                'slug' => 'produit-malveillant',
                'category_id' => $category->id,
                'price' => 1000,
                'images' => [
                    ['path' => [UploadedFile::fake()->create('script.php', 10, 'application/x-php')]],
                ],
            ])
            ->call('create')
            ->assertHasFormErrors();

        $this->assertDatabaseMissing('products', ['slug' => 'produit-malveillant']);
    }

    public function test_deactivating_a_product_removes_it_from_the_active_catalogue_without_deleting_it(): void
    {
        $product = Product::factory()->create(['is_active' => true]);

        $product->update(['is_active' => false]);

        $this->assertFalse(Product::active()->whereKey($product->id)->exists());
        $this->assertDatabaseHas('products', ['id' => $product->id, 'is_active' => false]);
    }

    public function test_soft_deleting_a_product_keeps_the_row_in_the_database(): void
    {
        $this->actingAs(Admin::factory()->create(), 'admin');

        $product = Product::factory()->create();

        $product->delete();

        $this->assertSoftDeleted($product);
        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }
}
