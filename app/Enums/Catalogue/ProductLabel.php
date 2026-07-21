<?php

namespace App\Enums\Catalogue;

/**
 * Signaux commerciaux de la maquette Aissatou Store (rapport d'identité p.13).
 *
 * Le rapport est explicite : « Les labels sont réservés aux informations qui
 * modifient réellement la décision. Ils ne deviennent jamais une décoration
 * permanente. » D'où une liste fermée et volontairement courte — pas un champ
 * libre où l'on finirait par écrire « Promo », « Top », « Coup de cœur »…
 */
enum ProductLabel: string
{
    case Selected = 'selected';
    case LimitedEdition = 'limited_edition';
    case New = 'new';

    public function label(): string
    {
        return match ($this) {
            self::Selected => 'Sélectionné',
            self::LimitedEdition => 'Édition limitée',
            self::New => 'Nouveauté',
        };
    }

    /**
     * Couleur de fond du badge. Chaque label garde la sienne d'un écran à
     * l'autre : c'est ce qui permet de le reconnaître sans le lire.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::Selected => 'bg-brand-accent text-brand-surface',
            self::LimitedEdition => 'bg-brand-signature text-brand-surface',
            self::New => 'bg-brand-ink text-brand-surface',
        };
    }

    /** @return array<string, string> Pour les listes déroulantes de l'admin. */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->all();
    }
}
