<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\Catalogue\CategoryResource\Pages\CreateCategory;
use App\Models\Admin;
use App\Models\Catalogue\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

    public function test_category_hierarchy_relationship_resolves_children(): void
    {
        $parent = Category::factory()->create();
        $child = Category::factory()->create(['parent_id' => $parent->id]);

        $this->assertTrue($parent->children->contains($child));
        $this->assertEquals($parent->id, $child->parent->id);
    }
}
