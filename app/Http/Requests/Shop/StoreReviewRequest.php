<?php

namespace App\Http\Requests\Shop;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'author_name' => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'rating' => ['nullable', 'integer', 'between:1,5'],
            'comment' => ['required', 'string', 'max:600'],
        ];
    }

    /**
     * En cas d'erreur de validation, revenir directement au formulaire d'avis
     * (ancre #avis) plutôt qu'en haut de la page d'accueil.
     */
    protected function getRedirectUrl(): string
    {
        return url()->previous().'#avis';
    }
}
