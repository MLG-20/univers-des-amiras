<?php

namespace Database\Factories\Catalogue;

use App\Models\Catalogue\Product;
use App\Models\Catalogue\ProductImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductImage>
 */
class ProductImageFactory extends Factory
{
    protected $model = ProductImage::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'path' => 'products/'.fake()->uuid().'.jpg',
            'is_primary' => false,
            'position' => 0,
        ];
    }
}
