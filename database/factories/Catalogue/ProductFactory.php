<?php

namespace Database\Factories\Catalogue;

use App\Models\Catalogue\Category;
use App\Models\Catalogue\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'category_id' => Category::factory(),
            'name' => Str::title($name),
            'slug' => Str::slug($name),
            'sku' => strtoupper(fake()->unique()->bothify('SKU-####-????')),
            'description' => fake()->paragraph(),
            'price' => fake()->randomFloat(2, 1000, 50000),
            'is_active' => true,
        ];
    }
}
