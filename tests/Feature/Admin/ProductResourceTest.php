<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\Catalogue\ProductResource\Pages\CreateProduct;
use App\Filament\Resources\Catalogue\ProductResource\Pages\EditProduct;
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

    public function test_editing_a_product_without_touching_its_image_keeps_it(): void
    {
        // Point critique du Repeater d'images : sur la page Edit, le champ de
        // chaque image démarre vide (aperçu au-dessus). Enregistrer sans y
        // toucher NE DOIT PAS effacer ni recréer l'image — comme le hero.
        Storage::fake('public');
        $this->actingAs(Admin::factory()->create(), 'admin');

        $product = Product::factory()->create(['name' => 'Voile brodé']);
        $image = $product->images()->create([
            'path' => 'products/existante.webp',
            'is_primary' => true,
            'position' => 0,
        ]);

        Livewire::test(EditProduct::class, ['record' => $product->getRouteKey()])
            ->fillForm(['name' => 'Voile brodé modifié'])
            ->call('save')
            ->assertHasNoFormErrors();

        $product->refresh();
        $this->assertSame('Voile brodé modifié', $product->name);
        $this->assertCount(1, $product->images);
        // Même enregistrement (pas supprimé/recréé) et chemin conservé :
        $this->assertSame($image->id, $product->images->first()->id);
        $this->assertSame('products/existante.webp', $product->images->first()->path);
    }

    public function test_uploading_a_new_file_replaces_an_existing_product_image(): void
    {
        Storage::fake('public');
        $this->actingAs(Admin::factory()->create(), 'admin');

        $product = Product::factory()->create();
        $product->images()->create([
            'path' => 'products/ancienne.webp',
            'is_primary' => true,
            'position' => 0,
        ]);

        Livewire::test(EditProduct::class, ['record' => $product->getRouteKey()])
            ->fillForm([
                'images' => [
                    ['path' => [UploadedFile::fake()->image('nouvelle.jpg', 1400, 1400)], 'is_primary' => true, 'position' => 0],
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $product->refresh();
        $this->assertCount(1, $product->images);
        $newPath = $product->images->first()->path;
        $this->assertNotSame('products/ancienne.webp', $newPath);
        $this->assertStringStartsWith('products/', (string) $newPath);
        Storage::disk('public')->assertExists($newPath);
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
