<?php

namespace App\Models\Content;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['author_name', 'location', 'rating', 'comment', 'is_published', 'position'])]
class Review extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'rating' => 'integer',
        ];
    }

    /** Avis modérés (publiés) uniquement — pour l'affichage public. */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }
}
