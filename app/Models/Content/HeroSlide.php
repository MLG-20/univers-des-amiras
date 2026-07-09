<?php

namespace App\Models\Content;

use App\Services\ImageVariantGenerator;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

#[Fillable(['image_path', 'image_position', 'title', 'subtitle', 'cta_label', 'cta_url', 'position', 'is_active'])]
class HeroSlide extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function sizedUrl(string $size): ?string
    {
        if (! $this->image_path) {
            return null;
        }

        $disk = Storage::disk('public');
        $sizedPath = app(ImageVariantGenerator::class)->sizedPath($this->image_path, $size);

        return $disk->exists($sizedPath) ? $disk->url($sizedPath) : $disk->url($this->image_path);
    }

    /**
     * Valeur CSS `background-position` correspondant au réglage admin — les
     * photos sont souvent des portraits (sujet décentré) alors que le hero
     * est un bandeau large ; ce réglage évite que le recadrage automatique
     * ne coupe le sujet.
     */
    public function cssBackgroundPosition(): string
    {
        return match ($this->image_position) {
            'left' => 'left center',
            'right' => 'right center',
            default => 'center',
        };
    }
}
