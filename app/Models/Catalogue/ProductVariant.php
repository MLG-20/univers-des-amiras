<?php

namespace App\Models\Catalogue;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['product_id', 'sku', 'attributes', 'price_override', 'stock', 'is_active'])]
class ProductVariant extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'attributes' => 'array',
            'price_override' => 'decimal:2',
            'stock' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
