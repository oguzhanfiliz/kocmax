<?php

namespace Database\Factories;

use App\Models\PricingRule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PricingRule>
 */
class PricingRuleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'slug' => $this->faker->slug(),
            'description' => $this->faker->sentence(),
            'type' => $this->faker->randomElement(['quantity_discount', 'amount_discount', 'customer_tier', 'seasonal']),
            'conditions' => json_encode([
                'min_quantity' => $this->faker->numberBetween(1, 100),
                'min_amount' => $this->faker->randomFloat(2, 100, 1000)
            ]),
            'actions' => json_encode([
                'discount_type' => $this->faker->randomElement(['percentage', 'fixed_amount']),
                'discount_value' => $this->faker->randomFloat(2, 5, 50)
            ]),
            'priority' => $this->faker->numberBetween(1, 100),
            'is_active' => true,
            'starts_at' => now(),
            'ends_at' => now()->addMonths(3),
            'usage_limit' => $this->faker->optional()->numberBetween(10, 1000),
            'usage_count' => 0
        ];
    }

    /**
     * Indicate that the rule is for quantity discounts.
     */
    public function quantityDiscount()
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'quantity_discount',
            'conditions' => json_encode([
                'min_quantity' => $this->faker->numberBetween(10, 100)
            ]),
            'actions' => json_encode([
                'discount_type' => 'percentage',
                'discount_value' => $this->faker->randomFloat(2, 5, 20)
            ])
        ]);
    }

    /**
     * Indicate that the rule is for amount discounts.
     */
    public function amountDiscount()
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'amount_discount',
            'conditions' => json_encode([
                'min_amount' => $this->faker->randomFloat(2, 500, 2000)
            ]),
            'actions' => json_encode([
                'discount_type' => 'fixed_amount',
                'discount_value' => $this->faker->randomFloat(2, 50, 200)
            ])
        ]);
    }

    /**
     * Indicate that the rule is inactive.
     */
    public function inactive()
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the rule has expired.
     */
    public function expired()
    {
        return $this->state(fn (array $attributes) => [
            'starts_at' => now()->subMonths(2),
            'ends_at' => now()->subMonth(),
        ]);
    }
}