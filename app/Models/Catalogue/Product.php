<?php

namespace App\Models\Catalogue;

use App\Enums\Catalogue\ProductLabel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable(['category_id', 'name', 'slug', 'sku', 'description', 'price', 'label', 'is_active'])]
class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'label' => ProductLabel::class,
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

    /**
     * Recherche/filtres combinables du catalogue public (section 2.1 du cahier
     * des charges). Toutes les valeurs sont déjà validées et nettoyées en amont
     * par FilterProductsRequest — ce scope ne fait qu'assembler la requête.
     *
     * @param  array{q: ?string, category_id: ?int, min_price: ?float, max_price: ?float, in_stock: bool}  $filters
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        if (filled($filters['q'] ?? null)) {
            // Échappement des caractères spéciaux LIKE (%, _, \) pour qu'ils soient
            // traités comme du texte littéral et non comme des jokers de motif.
            $term = '%'.addcslashes($filters['q'], '%_\\').'%';

            $query->where(function (Builder $query) use ($term): void {
                $query->where('name', 'like', $term)
                    ->orWhereHas('category', fn (Builder $query) => $query->where('name', 'like', $term));
            });
        }

        if (filled($filters['category_id'] ?? null)) {
            $query->where('category_id', $filters['category_id']);
        }

        if (filled($filters['min_price'] ?? null)) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if (filled($filters['max_price'] ?? null)) {
            $query->where('price', '<=', $filters['max_price']);
        }

        if ($filters['in_stock'] ?? false) {
            $query->where(function (Builder $query): void {
                $query->doesntHave('variants')
                    ->orWhereHas('variants', fn (Builder $query) => $query->active()->where('stock', '>', 0));
            });
        }

        return $query;
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
