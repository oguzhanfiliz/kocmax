<?php

declare(strict_types=1);

namespace App\Services\Pricing;

use App\Enums\Pricing\CustomerType;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class CustomerTypeDetector
{
    public function detect(?User $customer = null, array $context = []): CustomerType
    {
        if (!$customer) {
            return CustomerType::GUEST;
        }

        // If no context, try to get from cache
        if (empty($context)) {
            $cacheKey = "customer_type_{$customer->id}";
            $cached = Cache::get($cacheKey);
            if ($cached) {
                return CustomerType::from($cached);
            }
        }

        $customerType = $this->doDetect($customer, $context);

        // Cache result if no context was provided
        if (empty($context)) {
            Cache::put("customer_type_{$customer->id}", $customerType->value, 3600); // 1 hour
        }

        return $customerType;
    }

    private function doDetect(User $customer, array $context = []): CustomerType
    {
        // Check for customer type override first
        if (!empty($customer->customer_type_override)) {
            return CustomerType::from($customer->customer_type_override);
        }

        // Check context for forced type
        if (isset($context['force_type'])) {
            return CustomerType::from($context['force_type']);
        }

        // Check context for B2B behavioral indicators
        if (isset($context['order_quantity']) && $context['order_quantity'] >= 100) {
            return CustomerType::B2B;
        }

        if (isset($context['order_frequency']) && $context['order_frequency'] === 'high') {
            return CustomerType::B2B;
        }

        // Check for high volume wholesale qualification
        if (($customer->lifetime_value ?? 0) >= 50000) {
            return CustomerType::WHOLESALE;
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
        if ($customer->is_approved_dealer ?? false) {
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

    public function isWholesaleCustomer(?User $customer = null): bool
    {
        return $this->detect($customer) === CustomerType::WHOLESALE;
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