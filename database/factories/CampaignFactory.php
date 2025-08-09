<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Campaign>
 */
class CampaignFactory extends Factory
{
    protected $model = Campaign::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = ['buy_x_get_y_free', 'bundle_discount', 'cross_sell', 'percentage_discount'];
        $customerTypes = ['b2b', 'b2c', 'guest'];
        
        return [
            'name' => $this->faker->sentence(3),
            'slug' => $this->faker->slug(),
            'description' => $this->faker->sentence(8),
            'type' => $this->faker->randomElement($types),
            'status' => 'active',
            'rules' => [
                'discount_percentage' => $this->faker->numberBetween(10, 50),
                'min_quantity' => $this->faker->numberBetween(1, 5)
            ],
            'rewards' => [
                'type' => 'percentage',
                'value' => $this->faker->numberBetween(10, 30)
            ],
            'conditions' => [
                'min_cart_amount' => $this->faker->numberBetween(100, 1000)
            ],
            'priority' => $this->faker->numberBetween(1, 10),
            'is_active' => true,
            'is_stackable' => $this->faker->boolean(30),
            'starts_at' => now()->subDays($this->faker->numberBetween(0, 10)),
            'ends_at' => now()->addDays($this->faker->numberBetween(10, 60)),
            'usage_limit' => $this->faker->numberBetween(100, 2000),
            'usage_count' => $this->faker->numberBetween(0, 200),
            'usage_limit_per_customer' => $this->faker->numberBetween(1, 10),
            'minimum_cart_amount' => $this->faker->randomFloat(2, 50, 500),
            'customer_types' => $this->faker->randomElements($customerTypes, $this->faker->numberBetween(1, 3)),
            'created_by' => User::factory(),
            'updated_by' => User::factory(),
        ];
    }

    /**
     * Active campaign state
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
            'starts_at' => now()->subDays(1),
            'ends_at' => now()->addDays(30),
        ]);
    }

    /**
     * Upcoming campaign state
     */
    public function upcoming(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
            'starts_at' => now()->addDays(5),
            'ends_at' => now()->addDays(35),
        ]);
    }

    /**
     * Expired campaign state
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
            'starts_at' => now()->subDays(40),
            'ends_at' => now()->subDays(10),
        ]);
    }

    /**
     * B2B only campaign state
     */
    public function b2bOnly(): static
    {
        return $this->state(fn (array $attributes) => [
            'customer_types' => ['b2b'],
            'minimum_cart_amount' => 1000.00,
        ]);
    }

    /**
     * B2C only campaign state
     */
    public function b2cOnly(): static
    {
        return $this->state(fn (array $attributes) => [
            'customer_types' => ['b2c', 'guest'],
            'minimum_cart_amount' => 200.00,
        ]);
    }
}
