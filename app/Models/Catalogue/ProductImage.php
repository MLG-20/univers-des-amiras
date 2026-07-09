<?php

namespace App\Models\Catalogue;

use App\Services\ImageVariantGenerator;
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

    /**
     * URL d'une variante redimensionnée (voir ImageVariantGenerator::SIZES).
     * Retombe sur l'image d'origine si la variante n'a pas encore été
     * générée (image uploadée avant l'ajout de cette fonctionnalité, ou
     * génération ayant échoué) — jamais d'image cassée côté client.
     */
    public function sizedUrl(string $size): string
    {
        $disk = Storage::disk('public');
        $sizedPath = app(ImageVariantGenerator::class)->sizedPath($this->path, $size);

        return $disk->exists($sizedPath) ? $disk->url($sizedPath) : $this->url();
    }
}
