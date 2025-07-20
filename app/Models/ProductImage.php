<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'image',
        'is_primary',
        'sort_order',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->sort_order)) {
                $model->sort_order = static::where('product_id', $model->product_id)->max('sort_order') + 1;
            }
        });
    }

    public function moveUp()
    {
        $previousImage = static::where('product_id', $this->product_id)
            ->where('sort_order', '<', $this->sort_order)
            ->orderBy('sort_order', 'desc')
            ->first();

        if ($previousImage) {
            $temp = $this->sort_order;
            $this->sort_order = $previousImage->sort_order;
            $previousImage->sort_order = $temp;
            
            $this->save();
            $previousImage->save();
        }
    }

    public function moveDown()
    {
        $nextImage = static::where('product_id', $this->product_id)
            ->where('sort_order', '>', $this->sort_order)
            ->orderBy('sort_order', 'asc')
            ->first();

        if ($nextImage) {
            $temp = $this->sort_order;
            $this->sort_order = $nextImage->sort_order;
            $nextImage->sort_order = $temp;
            
            $this->save();
            $nextImage->save();
        }
    }
}
