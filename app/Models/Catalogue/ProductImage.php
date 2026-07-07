<?php

namespace App\Models\Catalogue;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

#[Fillable(['product_id', 'path', 'is_primary', 'position'])]
class ProductImage extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function url(): string
    {
        return Storage::disk('public')->url($this->path);
    }
}
