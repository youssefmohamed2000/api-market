<?php

namespace Database\Factories;

use App\Models\Buyer;
use App\Models\Seller;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $seller = Seller::has('products')->get()->random();
        $buyer = Buyer::all()->except($seller->id)->random()->id;
        return [
            'quantity' => fake()->numberBetween(2, 5),
            'buyer_id' => $buyer,
            'product_id' => $seller->products->random()->id
        ];
    }
}
