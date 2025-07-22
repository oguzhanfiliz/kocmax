<?php

namespace Database\Factories;

use App\Models\CustomerPricingTier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CustomerPricingTier>
 */
class CustomerPricingTierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(['Bronze', 'Silver', 'Gold', 'Platinum', 'Diamond']),
            'description' => $this->faker->sentence(),
            'discount_percentage' => $this->faker->randomFloat(2, 5, 25),
            'minimum_order_amount' => $this->faker->randomFloat(2, 0, 1000),
            'credit_limit' => $this->faker->randomFloat(2, 1000, 50000),
            'payment_terms_days' => $this->faker->randomElement([15, 30, 45, 60]),
            'is_active' => true,
            'sort_order' => $this->faker->numberBetween(1, 10)
        ];
    }

    /**
     * Indicate that the tier is for B2B customers.
     */
    public function b2b()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'B2B ' . $attributes['name'],
            'minimum_order_amount' => $this->faker->randomFloat(2, 500, 2000),
            'credit_limit' => $this->faker->randomFloat(2, 5000, 100000),
        ]);
    }

    /**
     * Indicate that the tier is for B2C customers.
     */
    public function b2c()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'B2C ' . $attributes['name'],
            'minimum_order_amount' => 0,
            'credit_limit' => 0,
            'payment_terms_days' => 0,
        ]);
    }

    /**
     * Indicate that the tier is inactive.
     */
    public function inactive()
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}