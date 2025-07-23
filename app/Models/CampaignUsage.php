<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'user_id',
        'order_id',
        'cart_items', // JSON: Hangi ürünler için kullanıldı
        'reward_items', // JSON: Hangi hediyeler verildi
        'discount_amount',
        'session_id', // Guest kullanıcılar için
        'ip_address',
        'user_agent',
        'metadata'
    ];

    protected $casts = [
        'cart_items' => 'array',
        'reward_items' => 'array',
        'discount_amount' => 'decimal:2',
        'metadata' => 'array'
    ];

    // Relations
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}