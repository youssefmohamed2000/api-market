<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => fake()->word(),
            'category_id' => fake()->numberBetween(1, 30),
            'description' => fake()->paragraph(),
            'price' => fake()->numberBetween(20, 1000),
            'quantity' => fake()->numberBetween(5, 20),
            'status' => fake()->numberBetween(0, 1),
            'image' => 'digital_' . fake()->numberBetween(1, 22) . '.jpg',
            'seller_id' => User::all()->random()->id
        ];
    }
}
