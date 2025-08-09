<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\DiscountCoupon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DiscountCoupon>
 */
class DiscountCouponFactory extends Factory
{
    protected $model = DiscountCoupon::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = ['percentage', 'fixed'];
        $type = $this->faker->randomElement($types);
        
        return [
            'code' => $this->generateUniqueCode(),
            'type' => $type,
            'value' => $type === 'percentage' 
                ? $this->faker->numberBetween(5, 50) 
                : $this->faker->numberBetween(10, 500),
            'min_order_amount' => $this->faker->optional(0.7)->randomFloat(2, 50, 1000),
            'usage_limit' => $this->faker->optional(0.8)->numberBetween(10, 1000),
            'used_count' => $this->faker->numberBetween(0, 50),
            'expires_at' => $this->faker->optional(0.8)->dateTimeBetween('now', '+6 months'),
            'is_active' => true,
        ];
    }

    /**
     * Generate unique coupon code
     */
    private function generateUniqueCode(): string
    {
        do {
            $code = strtoupper($this->faker->lexify('????') . $this->faker->numerify('##'));
        } while (DiscountCoupon::where('code', $code)->exists());
        
        return $code;
    }

    /**
     * Active coupon state
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
            'expires_at' => $this->faker->dateTimeBetween('+1 week', '+3 months'),
        ]);
    }

    /**
     * Expired coupon state
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
            'expires_at' => $this->faker->dateTimeBetween('-2 months', '-1 day'),
        ]);
    }

    /**
     * Used up coupon state
     */
    public function usedUp(): static
    {
        return $this->state(function (array $attributes) {
            $usageLimit = $this->faker->numberBetween(10, 100);
            return [
                'usage_limit' => $usageLimit,
                'used_count' => $usageLimit,
            ];
        });
    }

    /**
     * High value coupon state
     */
    public function highValue(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'percentage',
            'value' => $this->faker->numberBetween(25, 50),
            'min_order_amount' => $this->faker->numberBetween(500, 2000),
        ]);
    }

    /**
     * Free shipping coupon state
     */
    public function freeShipping(): static
    {
        return $this->state(fn (array $attributes) => [
            'code' => 'FREESHIP' . $this->faker->numberBetween(10, 99),
            'type' => 'fixed',
            'value' => 50.00, // Kargo bedeli
            'min_order_amount' => 200.00,
        ]);
    }

    /**
     * Welcome coupon state
     */
    public function welcome(): static
    {
        return $this->state(fn (array $attributes) => [
            'code' => 'WELCOME' . $this->faker->numberBetween(10, 99),
            'type' => 'percentage',
            'value' => 10,
            'min_order_amount' => 100.00,
            'usage_limit' => 1,
            'used_count' => 0,
        ]);
    }

    /**
     * Summer campaign coupon state
     */
    public function summer(): static
    {
        return $this->state(fn (array $attributes) => [
            'code' => 'SUMMER' . $this->faker->numberBetween(10, 99),
            'type' => 'percentage',
            'value' => $this->faker->numberBetween(15, 30),
            'min_order_amount' => $this->faker->numberBetween(300, 800),
            'expires_at' => now()->addMonths(3),
        ]);
    }
}
