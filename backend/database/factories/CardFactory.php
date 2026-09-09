<?php

namespace Database\Factories;

use App\Models\Card;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Card>
 */
class CardFactory extends Factory
{
    public function definition(): array
    {
        return [
            'card_number' => $this->faker->unique()->numerify('####################'),
            'pin' => $this->faker->numerify('####'),
            'activation_date' => now(),
            'expiration_date' => now()->addYear(),
            'balance' => $this->faker->randomFloat(2, 10, 1000),
        ];
    }
}
