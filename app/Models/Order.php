<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\OrderStatusHistory;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_number',
        'user_id',
        'cart_id',
        'customer_type',
        'status',
        'payment_status',
        'payment_method',
        'payment_transaction_id',
        'subtotal',
        'tax_amount',
        'shipping_amount',
        'discount_amount',
        'total_amount',
        'currency_code',
        'coupon_code',
        'notes',
        // Shipping Address
        'shipping_name',
        'shipping_email',
        'shipping_phone',
        'shipping_address',
        'shipping_city',
        'shipping_state',
        'shipping_zip',
        'shipping_country',
        // Billing Address
        'billing_name',
        'billing_email',
        'billing_phone',
        'billing_address',
        'billing_city',
        'billing_state',
        'billing_zip',
        'billing_country',
        'billing_tax_number',
        'billing_tax_office',
        // Tracking
        'tracking_number',
        'shipping_carrier',
        'shipped_at',
        'delivered_at',
        'paid_at',
        'cancelled_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'paid_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = self::generateOrderNumber();
            }
        });
    }

    public static function generateOrderNumber()
    {
        $prefix = 'ORD';
        $date = now()->format('Ymd');
        $random = strtoupper(substr(md5(uniqid()), 0, 6));
        
        return "{$prefix}-{$date}-{$random}";
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->orderBy('created_at', 'desc');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_code', 'code');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeShipped($query)
    {
        return $query->where('status', 'shipped');
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    // Completed order scope (used by pricing/loyalty calculations)
    public function scopeCompleted($query)
    {
        // Consider an order completed when it is delivered
        return $query->where('status', 'delivered');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopeB2B($query)
    {
        return $query->where('customer_type', 'B2B');
    }

    public function scopeB2C($query)
    {
        return $query->where('customer_type', 'B2C');
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isProcessing()
    {
        return $this->status === 'processing';
    }

    public function isShipped()
    {
        return $this->status === 'shipped';
    }

    public function isDelivered()
    {
        return $this->status === 'delivered';
    }

    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    public function isPaid()
    {
        return $this->payment_status === 'paid';
    }

    public function canBeCancelled()
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    public function markAsShipped($trackingNumber = null, $carrier = null)
    {
        $this->update([
            'status' => 'shipped',
            'tracking_number' => $trackingNumber,
            'shipping_carrier' => $carrier,
            'shipped_at' => now(),
        ]);
    }

    public function markAsDelivered()
    {
        $this->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);
    }

    public function cancel()
    {
        if ($this->canBeCancelled()) {
            $this->update([
                'status' => 'cancelled',
                'cancelled_at' => now()
            ]);
            
            // Restore product stock
            foreach ($this->items as $item) {
                $item->product->increment('stock', $item->quantity);
                
                if ($item->product_variant_id) {
                    $item->productVariant->increment('stock', $item->quantity);
                }
            }
        }
    }

    /**
     * Siparişi ödendi olarak işaretle
     */
    public function markAsPaid(string $transactionId = null): void
    {
        $this->update([
            'payment_status' => 'paid',
            'payment_transaction_id' => $transactionId,
            'paid_at' => now(),
            'status' => 'processing' // Ödeme sonrası işleme al
        ]);
    }

    /**
     * Ödeme başarısız olarak işaretle
     */
    public function markAsPaymentFailed(): void
    {
        $this->update([
            'payment_status' => 'failed',
            'status' => 'pending'
        ]);
    }

    /**
     * KDV detaylarını döndür
     */
    public function getTaxBreakdown(): array
    {
        if (!$this->relationLoaded('items')) {
            return [];
        }

        $taxBreakdown = [];
        $totalTaxAmount = 0;

        foreach ($this->items as $item) {
            $taxRate = (float) ($item->tax_rate ?? 0);
            $taxAmount = (float) ($item->tax_amount ?? 0);
            
            if ($taxRate > 0) {
                $key = "KDV %{$taxRate}";
                
                if (!isset($taxBreakdown[$key])) {
                    $taxBreakdown[$key] = [
                        'tax_rate' => $taxRate,
                        'tax_rate_label' => "%{$taxRate}",
                        'tax_amount' => 0,
                        'base_amount' => 0,
                        'items_count' => 0
                    ];
                }
                
                $taxBreakdown[$key]['tax_amount'] += $taxAmount;
                $taxBreakdown[$key]['base_amount'] += ($item->total - $taxAmount);
                $taxBreakdown[$key]['items_count'] += $item->quantity;
                $totalTaxAmount += $taxAmount;
            }
        }

        // KDV detaylarını düzenle
        $formattedBreakdown = [];
        foreach ($taxBreakdown as $key => $breakdown) {
            $formattedBreakdown[] = [
                'tax_rate' => $breakdown['tax_rate'],
                'tax_rate_label' => $breakdown['tax_rate_label'],
                'base_amount' => round($breakdown['base_amount'], 2),
                'tax_amount' => round($breakdown['tax_amount'], 2),
                'total_amount' => round($breakdown['base_amount'] + $breakdown['tax_amount'], 2),
                'items_count' => $breakdown['items_count']
            ];
        }

        return [
            'breakdown' => $formattedBreakdown,
            'total_tax_amount' => round($totalTaxAmount, 2),
            'total_base_amount' => round($this->subtotal - $totalTaxAmount, 2),
            'total_amount_incl_tax' => round($this->total_amount, 2)
        ];
    }
}
