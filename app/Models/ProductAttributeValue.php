<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductAttributeValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'attribute_id',
        'value_text',
        'value_number',
        'value_json',
    ];

    protected $casts = [
        'value_number' => 'decimal:4',
        'value_json'   => 'array',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class);
    }

    /**
     * Get a human-readable representation of this attribute's value.
     */
    public function getFormattedValue(): string
    {
        if ($this->value_json && is_array($this->value_json)) {
            return implode(', ', $this->value_json);
        }

        if ($this->value_number !== null) {
            return (string) round((float) $this->value_number, 2);
        }

        if ($this->attribute && $this->attribute->type === 'boolean') {
            return $this->value_text === '1' || $this->value_text === 'true' ? 'Yes' : 'No';
        }

        return (string) ($this->value_text ?? '');
    }
}

