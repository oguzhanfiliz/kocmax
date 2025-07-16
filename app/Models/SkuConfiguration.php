<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkuConfiguration extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'pattern',
        'separator',
        'number_length',
        'last_number',
        'is_default',
    ];
}
