<?php

namespace Database\Factories\Catalogue;

use App\Models\Catalogue\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => Str::title($name),
            'slug' => Str::slug($name),
            'description' => fake()->optional()->sentence(),
            'parent_id' => null,
            'position' => fake()->numberBetween(0, 20),
            'is_active' => true,
        ];
    }
}
