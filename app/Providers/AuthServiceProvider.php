<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        \App\Models\Campaign::class => \App\Policies\CampaignPolicy::class,
        \App\Models\Cart::class => \App\Policies\CartPolicy::class,
        \App\Models\Category::class => \App\Policies\CategoryPolicy::class,
        \App\Models\Currency::class => \App\Policies\CurrencyPolicy::class,
        \App\Models\CustomerPricingTier::class => \App\Policies\CustomerPricingTierPolicy::class,
        \App\Models\DealerApplication::class => \App\Policies\DealerApplicationPolicy::class,
        \App\Models\DiscountCoupon::class => \App\Policies\DiscountCouponPolicy::class,
        \App\Models\Order::class => \App\Policies\OrderPolicy::class,
        \App\Models\PriceHistory::class => \App\Policies\PriceHistoryPolicy::class,
        \App\Models\PricingRule::class => \App\Policies\PricingRulePolicy::class,
        \App\Models\Product::class => \App\Policies\ProductPolicy::class,
        \App\Models\SkuConfiguration::class => \App\Policies\SkuConfigurationPolicy::class,
        \App\Models\User::class => \App\Policies\UserPolicy::class,
        \App\Models\VariantType::class => \App\Policies\VariantTypePolicy::class,
        \Spatie\Permission\Models\Role::class => \App\Policies\RolePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
