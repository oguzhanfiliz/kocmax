<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DealerApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_name',
        'tax_number',
        'trade_registry_document_path',
        'tax_plate_document_path',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
