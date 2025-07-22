<?php

namespace Tests\Unit\Pricing;

use App\Services\Pricing\CustomerTypeDetector;
use App\Enums\Pricing\CustomerType;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CustomerTypeDetectorTest extends TestCase
{
    use RefreshDatabase;

    private CustomerTypeDetector $detector;

    protected function setUp(): void
    {
        parent::setUp();
        $this->detector = new CustomerTypeDetector();
        
        // Create necessary roles for tests
        \Spatie\Permission\Models\Role::create(['name' => 'dealer']);
        \Spatie\Permission\Models\Role::create(['name' => 'wholesale']);
        \Spatie\Permission\Models\Role::create(['name' => 'retail']);
    }

    public function test_detects_guest_user_when_no_user_provided()
    {
        $customerType = $this->detector->detect(null);
        
        $this->assertEquals(CustomerType::GUEST, $customerType);
    }

    public function test_detects_b2b_for_approved_dealer()
    {
        $user = User::factory()->create([
            'is_approved_dealer' => true,
            'company_name' => 'Test Company Ltd.',
        ]);
        
        $customerType = $this->detector->detect($user);
        
        $this->assertEquals(CustomerType::B2B, $customerType);
    }

    public function test_detects_b2b_for_user_with_dealer_role()
    {
        $user = User::factory()->create([
            'is_approved_dealer' => false,
        ]);
        
        // Simulate user having dealer role
        $user->assignRole('dealer');
        
        $customerType = $this->detector->detect($user);
        
        $this->assertEquals(CustomerType::B2B, $customerType);
    }

    public function test_detects_b2b_for_user_with_company_name()
    {
        $user = User::factory()->create([
            'is_approved_dealer' => false,
            'company_name' => 'Business Corp',
        ]);
        
        $customerType = $this->detector->detect($user);
        
        $this->assertEquals(CustomerType::B2B, $customerType);
    }

    public function test_detects_b2b_for_user_with_tax_number()
    {
        $user = User::factory()->create([
            'is_approved_dealer' => false,
            'company_name' => null,
            'tax_number' => '1234567890',
        ]);
        
        $customerType = $this->detector->detect($user);
        
        $this->assertEquals(CustomerType::B2B, $customerType);
    }

    public function test_uses_customer_type_override_when_set()
    {
        $user = User::factory()->create([
            'is_approved_dealer' => true,
            'customer_type_override' => CustomerType::WHOLESALE->value,
        ]);
        
        $customerType = $this->detector->detect($user);
        
        $this->assertEquals(CustomerType::WHOLESALE, $customerType);
    }

    public function test_detects_b2c_for_regular_individual_user()
    {
        $user = User::factory()->create([
            'is_approved_dealer' => false,
            'company_name' => null,
            'tax_number' => null,
            'customer_type_override' => null,
        ]);
        
        $customerType = $this->detector->detect($user);
        
        $this->assertEquals(CustomerType::B2C, $customerType);
    }

    public function test_detects_wholesale_for_high_volume_user()
    {
        $user = User::factory()->create([
            'is_approved_dealer' => false,
            'lifetime_value' => 50000, // High lifetime value
        ]);
        
        $customerType = $this->detector->detect($user);
        
        // Should detect as wholesale due to high volume
        $this->assertEquals(CustomerType::WHOLESALE, $customerType);
    }

    public function test_customer_type_detection_with_context()
    {
        $user = User::factory()->create([
            'is_approved_dealer' => false,
        ]);
        
        // Test with context suggesting B2B behavior
        $context = [
            'order_quantity' => 500,
            'order_frequency' => 'high',
        ];
        
        $customerType = $this->detector->detect($user, $context);
        
        $this->assertEquals(CustomerType::B2B, $customerType);
    }

    public function test_caches_customer_type_detection()
    {
        $user = User::factory()->create([
            'is_approved_dealer' => true,
        ]);
        
        // First call
        $customerType1 = $this->detector->detect($user);
        
        // Second call should use cache
        $customerType2 = $this->detector->detect($user);
        
        $this->assertEquals($customerType1, $customerType2);
        $this->assertEquals(CustomerType::B2B, $customerType1);
    }

    public function test_can_check_if_user_qualifies_for_b2b_pricing()
    {
        $b2bUser = User::factory()->create([
            'is_approved_dealer' => true,
        ]);
        
        $b2cUser = User::factory()->create([
            'is_approved_dealer' => false,
            'company_name' => null,
        ]);
        
        $this->assertTrue($this->detector->isB2BCustomer($b2bUser));
        $this->assertFalse($this->detector->isB2BCustomer($b2cUser));
    }

    public function test_can_check_if_user_qualifies_for_wholesale_pricing()
    {
        $wholesaleUser = User::factory()->create([
            'lifetime_value' => 100000,
        ]);
        
        $regularUser = User::factory()->create([
            'lifetime_value' => 1000,
        ]);
        
        $this->assertTrue($this->detector->isWholesaleCustomer($wholesaleUser));
        $this->assertFalse($this->detector->isWholesaleCustomer($regularUser));
    }
}