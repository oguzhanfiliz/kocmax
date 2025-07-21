<?php

namespace Tests\Performance\Pricing;

use App\Models\User;
use App\Models\ProductVariant;
use App\Models\CustomerPricingTier;
use App\Models\PricingRule;
use App\Services\PricingService;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PricingPerformanceTest extends TestCase
{
    use RefreshDatabase;

    private PricingService $pricingService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pricingService = app(PricingService::class);
    }

    public function test_single_price_calculation_performance()
    {
        // Setup data
        $user = User::factory()->create(['is_approved_dealer' => true]);
        $variant = ProductVariant::factory()->create(['price' => 100.00]);

        PricingRule::factory(3)->create([
            'conditions' => ['customer_type' => 'b2b'],
            'actions' => ['discount_percentage' => 5],
            'is_active' => true
        ]);

        // Measure single calculation
        $startTime = microtime(true);
        $result = $this->pricingService->calculatePrice($variant, 1, $user);
        $endTime = microtime(true);

        $executionTime = ($endTime - $startTime) * 1000; // Convert to milliseconds

        // Single calculation should complete under 50ms
        $this->assertLessThan(50, $executionTime, 
            "Single price calculation took {$executionTime}ms, expected < 50ms"
        );
        $this->assertNotNull($result);
    }

    public function test_bulk_price_calculation_performance()
    {
        // Setup multiple users and variants
        $users = User::factory(10)->create(['is_approved_dealer' => true]);
        $variants = ProductVariant::factory(20)->create(['price' => 100.00]);

        // Create pricing rules
        PricingRule::factory(5)->create([
            'conditions' => ['customer_type' => 'b2b'],
            'actions' => ['discount_percentage' => 10],
            'is_active' => true
        ]);

        $startTime = microtime(true);
        $calculationCount = 0;

        // Perform bulk calculations
        foreach ($users as $user) {
            foreach ($variants->take(5) as $variant) { // 10 users × 5 variants = 50 calculations
                $this->pricingService->calculatePrice($variant, rand(1, 10), $user);
                $calculationCount++;
            }
        }

        $endTime = microtime(true);
        $totalTime = ($endTime - $startTime) * 1000;
        $avgTimePerCalculation = $totalTime / $calculationCount;

        // Bulk calculations should average under 30ms per calculation
        $this->assertLessThan(30, $avgTimePerCalculation,
            "Average calculation time was {$avgTimePerCalculation}ms, expected < 30ms"
        );
        
        // Total time for 50 calculations should be under 2 seconds
        $this->assertLessThan(2000, $totalTime,
            "Total bulk calculation time was {$totalTime}ms, expected < 2000ms"
        );

        $this->assertEquals(50, $calculationCount);
    }

    public function test_database_query_optimization()
    {
        $user = User::factory()->create(['is_approved_dealer' => true]);
        $variant = ProductVariant::factory()->create(['price' => 100.00]);

        // Create multiple pricing rules to test query efficiency
        PricingRule::factory(10)->create([
            'conditions' => ['customer_type' => 'b2b'],
            'actions' => ['discount_percentage' => 5],
            'is_active' => true
        ]);

        // Enable query logging
        DB::enableQueryLog();

        $this->pricingService->calculatePrice($variant, 5, $user);

        $queries = DB::getQueryLog();
        $queryCount = count($queries);

        // Should not exceed reasonable number of queries
        $this->assertLessThan(15, $queryCount,
            "Price calculation generated {$queryCount} queries, expected < 15"
        );

        // Check for N+1 queries
        $selectQueries = array_filter($queries, function($query) {
            return stripos($query['query'], 'select') === 0;
        });

        $this->assertLessThan(10, count($selectQueries),
            "Too many SELECT queries detected, possible N+1 problem"
        );

        DB::disableQueryLog();
    }

    public function test_caching_performance_improvement()
    {
        $user = User::factory()->create(['is_approved_dealer' => true]);
        $variant = ProductVariant::factory()->create(['price' => 100.00]);

        // Clear any existing cache
        Cache::flush();

        // First calculation (cache miss)
        $startTime = microtime(true);
        $result1 = $this->pricingService->calculatePrice($variant, 1, $user);
        $firstCallTime = (microtime(true) - $startTime) * 1000;

        // Second calculation (cache hit)
        $startTime = microtime(true);
        $result2 = $this->pricingService->calculatePrice($variant, 1, $user);
        $secondCallTime = (microtime(true) - $startTime) * 1000;

        // Cached call should be significantly faster
        $this->assertLessThan($firstCallTime * 0.5, $secondCallTime,
            "Cached calculation ({$secondCallTime}ms) should be < 50% of first call ({$firstCallTime}ms)"
        );

        // Results should be identical
        $this->assertEquals($result1->finalPrice->amount, $result2->finalPrice->amount);
    }

    public function test_memory_usage_optimization()
    {
        $initialMemory = memory_get_usage();

        // Create test data
        $users = User::factory(20)->create(['is_approved_dealer' => true]);
        $variants = ProductVariant::factory(30)->create(['price' => 100.00]);

        PricingRule::factory(15)->create([
            'conditions' => ['customer_type' => 'b2b'],
            'actions' => ['discount_percentage' => 8],
            'is_active' => true
        ]);

        // Perform calculations
        foreach ($users as $user) {
            foreach ($variants->take(3) as $variant) {
                $this->pricingService->calculatePrice($variant, rand(1, 5), $user);
            }
        }

        $peakMemory = memory_get_peak_usage();
        $memoryIncrease = $peakMemory - $initialMemory;
        $memoryIncreaseMB = $memoryIncrease / 1024 / 1024;

        // Memory increase should be reasonable (< 50MB for this test)
        $this->assertLessThan(50, $memoryIncreaseMB,
            "Memory usage increased by {$memoryIncreaseMB}MB, expected < 50MB"
        );
    }

    public function test_concurrent_pricing_calculations()
    {
        // Simulate concurrent users
        $users = User::factory(5)->create(['is_approved_dealer' => true]);
        $variant = ProductVariant::factory()->create(['price' => 100.00]);

        PricingRule::factory(3)->create([
            'conditions' => ['customer_type' => 'b2b'],
            'actions' => ['discount_percentage' => 10],
            'is_active' => true
        ]);

        $startTime = microtime(true);
        $results = [];

        // Simulate concurrent requests
        foreach ($users as $user) {
            $results[] = $this->pricingService->calculatePrice($variant, rand(1, 10), $user);
        }

        $endTime = microtime(true);
        $totalTime = ($endTime - $startTime) * 1000;

        // All concurrent calculations should complete quickly
        $this->assertLessThan(200, $totalTime,
            "Concurrent calculations took {$totalTime}ms, expected < 200ms"
        );

        // All results should be valid
        foreach ($results as $result) {
            $this->assertNotNull($result);
            $this->assertGreaterThan(0, $result->finalPrice->amount);
        }
    }

    public function test_large_dataset_performance()
    {
        // Create large dataset
        $users = User::factory(50)->create(['is_approved_dealer' => true]);
        $variants = ProductVariant::factory(100)->create(['price' => 100.00]);

        // Create many pricing rules
        PricingRule::factory(25)->create([
            'conditions' => ['customer_type' => 'b2b'],
            'actions' => ['discount_percentage' => rand(5, 15)],
            'is_active' => true
        ]);

        $startTime = microtime(true);

        // Perform sample calculations
        $sampleUsers = $users->random(10);
        $sampleVariants = $variants->random(10);

        foreach ($sampleUsers as $user) {
            foreach ($sampleVariants->take(2) as $variant) {
                $this->pricingService->calculatePrice($variant, rand(1, 5), $user);
            }
        }

        $endTime = microtime(true);
        $totalTime = ($endTime - $startTime) * 1000;

        // 20 calculations with large dataset should complete under 1 second
        $this->assertLessThan(1000, $totalTime,
            "Large dataset calculations took {$totalTime}ms, expected < 1000ms"
        );
    }

    public function test_pricing_rule_complexity_performance()
    {
        $user = User::factory()->create(['is_approved_dealer' => true]);
        $variant = ProductVariant::factory()->create(['price' => 100.00]);

        // Create complex pricing rules with various conditions
        $complexRules = [
            [
                'name' => 'Complex Rule 1',
                'conditions' => [
                    'customer_type' => 'b2b',
                    'min_quantity' => 5,
                    'min_order_amount' => 300,
                    'day_of_week' => 'monday'
                ],
                'actions' => ['discount_percentage' => 12]
            ],
            [
                'name' => 'Complex Rule 2',
                'conditions' => [
                    'customer_type' => 'b2b',
                    'user_tier' => 'premium',
                    'season' => 'winter',
                    'product_category' => 'safety'
                ],
                'actions' => ['discount_percentage' => 8]
            ],
            [
                'name' => 'Complex Rule 3',
                'conditions' => [
                    'customer_type' => 'b2b',
                    'min_lifetime_value' => 10000,
                    'min_monthly_orders' => 3
                ],
                'actions' => ['discount_amount' => 25]
            ]
        ];

        foreach ($complexRules as $ruleData) {
            PricingRule::factory()->create([
                'name' => $ruleData['name'],
                'conditions' => $ruleData['conditions'],
                'actions' => $ruleData['actions'],
                'is_active' => true
            ]);
        }

        $startTime = microtime(true);

        // Test with various contexts
        $contexts = [
            ['day_of_week' => 'monday'],
            ['season' => 'winter', 'product_category' => 'safety'],
            ['min_lifetime_value' => 15000, 'min_monthly_orders' => 5]
        ];

        foreach ($contexts as $context) {
            $this->pricingService->calculatePrice($variant, 6, $user, $context);
        }

        $endTime = microtime(true);
        $complexRuleTime = ($endTime - $startTime) * 1000;

        // Complex rule evaluation should complete under 150ms
        $this->assertLessThan(150, $complexRuleTime,
            "Complex rule evaluation took {$complexRuleTime}ms, expected < 150ms"
        );
    }

    public function test_pricing_calculation_scalability()
    {
        // Test scalability with increasing load
        $loadSizes = [10, 50, 100, 200];
        $timings = [];

        foreach ($loadSizes as $size) {
            $users = User::factory($size)->create(['is_approved_dealer' => true]);
            $variants = ProductVariant::factory(min($size, 20))->create(['price' => 100.00]);

            $startTime = microtime(true);

            // Sample calculations for each load size
            $sampleSize = min($size, 10);
            foreach ($users->take($sampleSize) as $user) {
                foreach ($variants->take(2) as $variant) {
                    $this->pricingService->calculatePrice($variant, rand(1, 3), $user);
                }
            }

            $endTime = microtime(true);
            $timings[$size] = ($endTime - $startTime) * 1000;

            // Clean up for next iteration
            User::query()->delete();
            ProductVariant::query()->delete();
        }

        // Check that performance doesn't degrade exponentially
        $scalabilityRatio = $timings[200] / $timings[10];
        
        $this->assertLessThan(10, $scalabilityRatio,
            "Performance degraded by factor of {$scalabilityRatio}, expected < 10x"
        );

        // Log performance metrics
        foreach ($timings as $size => $time) {
            echo "\nLoad size {$size}: {$time}ms";
        }
    }
}