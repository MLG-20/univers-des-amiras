<?php

namespace Database\Seeders;

use App\Models\Catalogue\Category;
use App\Models\Catalogue\Product;
use App\Models\Catalogue\ProductVariant;
use Illuminate\Database\Seeder;

class CatalogueSeeder extends Seeder
{
    public function run(): void
    {
        $voiles = Category::factory()->create(['name' => 'Voiles & Hijabs', 'slug' => 'voiles-hijabs']);
        $parfums = Category::factory()->create(['name' => 'Parfums', 'slug' => 'parfums']);
        $sacs = Category::factory()->create(['name' => 'Sacs à main', 'slug' => 'sacs-a-main']);

        Category::factory()->create([
            'name' => 'Voiles en soie',
            'slug' => 'voiles-en-soie',
            'parent_id' => $voiles->id,
        ]);

        foreach ([$voiles, $parfums, $sacs] as $category) {
            Product::factory()
                ->count(3)
                ->for($category)
                ->has(ProductVariant::factory()->count(2), 'variants')
                ->create();
        }
    }
}
