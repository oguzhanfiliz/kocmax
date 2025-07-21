<?php

declare(strict_types=1);

namespace App\Services\Pricing;

use App\Enums\Pricing\CustomerType;
use App\Models\User;

class CustomerTypeDetector
{
    public function detect(?User $customer = null): CustomerType
    {
        if (!$customer) {
            return CustomerType::GUEST;
        }

        // Check user roles to determine customer type
        if ($customer->hasRole('dealer')) {
            return CustomerType::B2B;
        }

        if ($customer->hasRole('wholesale')) {
            return CustomerType::WHOLESALE;
        }

        if ($customer->hasRole('retail')) {
            return CustomerType::RETAIL;
        }

        // Check if user has dealer-related fields
        if ($customer->is_dealer ?? false) {
            return CustomerType::B2B;
        }

        // Check company information
        if (!empty($customer->company_name) || !empty($customer->tax_number)) {
            return CustomerType::B2B;
        }

        // Default to B2C for registered users without specific business indicators
        return CustomerType::B2C;
    }

    public function isB2BCustomer(?User $customer = null): bool
    {
        return $this->detect($customer)->isB2B();
    }

    public function isB2CCustomer(?User $customer = null): bool
    {
        return $this->detect($customer)->isB2C();
    }

    public function canAccessDealerPrices(?User $customer = null): bool
    {
        return $this->detect($customer)->canAccessDealerPrices();
    }

    public function getCustomerTier(?User $customer = null): string
    {
        $customerType = $this->detect($customer);
        
        if (!$customer) {
            return 'guest';
        }

        // Determine customer tier based on order history and type
        $totalOrders = $customer->orders()->completed()->count();
        $totalSpent = $customer->orders()->completed()->sum('total_amount');

        if ($customerType->isB2B()) {
            return match(true) {
                $totalSpent >= 500000 => 'b2b_vip',
                $totalSpent >= 250000 => 'b2b_premium',
                $totalSpent >= 100000 => 'b2b_gold',
                $totalSpent >= 50000 => 'b2b_silver',
                default => 'b2b_standard'
            };
        }

        return match(true) {
            $totalOrders >= 50 => 'b2c_vip',
            $totalOrders >= 25 => 'b2c_gold',
            $totalOrders >= 10 => 'b2c_silver',
            $totalOrders >= 5 => 'b2c_bronze',
            default => 'b2c_standard'
        };
    }
}