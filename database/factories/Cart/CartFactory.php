<?php

namespace Database\Factories\Cart;

use App\Models\Cart\Cart;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Cart>
 */
class CartFactory extends Factory
{
    protected $model = Cart::class;

    public function definition(): array
    {
        return [
            'user_id' => null,
            'session_id' => fake()->unique()->uuid(),
        ];
    }
}
