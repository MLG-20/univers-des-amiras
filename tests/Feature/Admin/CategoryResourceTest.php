<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\Catalogue\CategoryResource\Pages\CreateCategory;
use App\Filament\Resources\Catalogue\CategoryResource\Pages\EditCategory;
use App\Models\Admin;
use App\Models\Catalogue\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class CategoryResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_root_category(): void
    {
        $this->actingAs(Admin::factory()->create(), 'admin');

        Livewire::test(CreateCategory::class)
            ->fillForm([
                'name' => 'Voiles & Hijabs',
                'slug' => 'voiles-hijabs',
                'is_active' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('categories', [
            'name' => 'Voiles & Hijabs',
            'slug' => 'voiles-hijabs',
            'parent_id' => null,
        ]);
    }

    public function test_admin_can_create_a_child_category_under_a_parent(): void
    {
        $this->actingAs(Admin::factory()->create(), 'admin');

        $parent = Category::factory()->create(['name' => 'Voiles & Hijabs']);

        Livewire::test(CreateCategory::class)
            ->fillForm([
                'name' => 'Voiles en soie',
                'slug' => 'voiles-en-soie',
                'parent_id' => $parent->id,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('categories', [
            'name' => 'Voiles en soie',
            'parent_id' => $parent->id,
        ]);
    }

    public function test_editing_a_category_without_touching_the_image_keeps_it(): void
    {
        // Le point critique : sur la page Edit, le champ image démarre vide
        // (aperçu au-dessus). Enregistrer sans y toucher NE DOIT PAS effacer
        // l'image existante — même comportement que le hero et les produits.
        Storage::fake('public');
        $this->actingAs(Admin::factory()->create(), 'admin');

        $category = Category::factory()->create([
            'name' => 'Voiles',
            'image_path' => 'categories/existante.webp',
        ]);

        Livewire::test(EditCategory::class, ['record' => $category->getRouteKey()])
            ->fillForm(['name' => 'Voiles modifiés'])
            ->call('save')
            ->assertHasNoFormErrors();

        $category->refresh();
        $this->assertSame('Voiles modifiés', $category->name);
        $this->assertSame('categories/existante.webp', $category->image_path);
    }

    public function test_uploading_a_new_image_replaces_the_category_image(): void
    {
        Storage::fake('public');
        $this->actingAs(Admin::factory()->create(), 'admin');

        $category = Category::factory()->create([
            'image_path' => 'categories/ancienne.webp',
        ]);

        Livewire::test(EditCategory::class, ['record' => $category->getRouteKey()])
            ->fillForm([
                'image_path' => [UploadedFile::fake()->image('nouvelle.jpg', 1000, 1250)],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $category->refresh();
        $this->assertNotSame('categories/ancienne.webp', $category->image_path);
        $this->assertStringStartsWith('categories/', (string) $category->image_path);
        Storage::disk('public')->assertExists($category->image_path);
    }

    public function test_category_hierarchy_relationship_resolves_children(): void
    {
        $parent = Category::factory()->create();
        $child = Category::factory()->create(['parent_id' => $parent->id]);

        $this->assertTrue($parent->children->contains($child));
        $this->assertEquals($parent->id, $child->parent->id);
    }
}
