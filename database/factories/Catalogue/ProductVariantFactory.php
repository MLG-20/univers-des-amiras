<?php

namespace Database\Factories\Catalogue;

use App\Models\Catalogue\Product;
use App\Models\Catalogue\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductVariant>
 */
class ProductVariantFactory extends Factory
{
    protected $model = ProductVariant::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'sku' => strtoupper(fake()->unique()->bothify('VAR-####-????')),
            'attributes' => [
                'couleur' => fake()->safeColorName(),
                'taille' => fake()->randomElement(['S', 'M', 'L', 'XL']),
            ],
            'price_override' => null,
            'stock' => fake()->numberBetween(0, 100),
            'is_active' => true,
        ];
    }
}
