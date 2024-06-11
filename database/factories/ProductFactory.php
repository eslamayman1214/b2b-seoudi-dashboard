<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'item_code' => $this->faker->unique()->text(10),
            'sku' => $this->faker->unique()->text(10),
            'price' => $this->faker->randomFloat(2, 1, 1000),
            'stock' => $this->faker->randomFloat(2, 0, 1000),
        ];
    }
}