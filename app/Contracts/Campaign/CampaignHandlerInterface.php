<?php

declare(strict_types=1);

namespace App\Contracts\Campaign;

use App\Models\Campaign;
use App\Models\User;
use App\ValueObjects\Campaign\CampaignResult;
use App\ValueObjects\Campaign\CartContext;

interface CampaignHandlerInterface
{
    /**
     * Kampanyanın uygulanabilir olup olmadığını kontrol eder
     */
    public function canApply(Campaign $campaign, CartContext $context, ?User $user = null): bool;

    /**
     * Kampanyayı uygular ve sonucu döner
     */
    public function apply(Campaign $campaign, CartContext $context, ?User $user = null): CampaignResult;

    /**
     * Bu handler'ın desteklediği kampanya tipini döner
     */
    public function getSupportedType(): string;

    /**
     * Kampanya öncelik seviyesini döner (yüksek numara = yüksek öncelik)
     */
    public function getPriority(): int;
}