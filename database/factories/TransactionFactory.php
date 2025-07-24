<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'date' => now(),
            'source' => fake()->name,
            'description' => fake()->sentence(),
            'value' => fake()->randomFloat(2, 0, 1000),
            'is_recurred' => fake()->boolean,
            'is_receipt' => true
        ];
    }
}
