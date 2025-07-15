<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttributeType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'component',
        'config',
    ];

    protected $casts = [
        'config' => 'array',
    ];

    /**
     * Attribute types constants
     */
    const TYPE_TEXT = 'text';
    const TYPE_SELECT = 'select';
    const TYPE_CHECKBOX = 'checkbox';
    const TYPE_RADIO = 'radio';
    const TYPE_COLOR = 'color';
    const TYPE_NUMBER = 'number';
    const TYPE_DATE = 'date';

    /**
     * Get all attributes of this type
     */
    public function productAttributes(): HasMany
    {
        return $this->hasMany(ProductAttribute::class);
    }

    /**
     * Get Filament component for this type
     */
    public function getFilamentComponent(): string
    {
        return match($this->name) {
            self::TYPE_TEXT => 'TextInput',
            self::TYPE_SELECT => 'Select',
            self::TYPE_CHECKBOX => 'CheckboxList',
            self::TYPE_RADIO => 'Radio',
            self::TYPE_COLOR => 'ColorPicker',
            self::TYPE_NUMBER => 'TextInput',
            self::TYPE_DATE => 'DatePicker',
            default => 'TextInput',
        };
    }
}
