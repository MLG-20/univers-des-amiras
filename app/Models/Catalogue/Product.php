<?php

namespace App\Models\Catalogue;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable(['category_id', 'name', 'slug', 'sku', 'description', 'price', 'is_active'])]
class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Product $product): void {
            if (blank($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('position');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    // Les deux méthodes ci-dessous lisent des relations déjà chargées (jamais
    // de requête) — l'appelant doit donc précharger images/variantes en amont
    // pour éviter le problème N+1 sur les listings.
    public function primaryImage(): ?ProductImage
    {
        return $this->images->firstWhere('is_primary', true) ?? $this->images->first();
    }

    public function isInStock(): bool
    {
        // Aucune variante = produit sans suivi de stock (le schéma n'enregistre
        // le stock qu'au niveau variante) — considéré comme toujours disponible.
        if ($this->variants->isEmpty()) {
            return true;
        }

        return $this->variants->contains(fn (ProductVariant $variant) => $variant->is_active && $variant->stock > 0);
    }
}
