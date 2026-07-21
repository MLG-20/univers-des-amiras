<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Reprend la slide d'ouverture du hero, restée à l'ancienne marque.
 *
 * Le renommage vers Aissatou Store n'avait touché que les valeurs par défaut du
 * code ; les lignes déjà saisies en base, elles, n'ont jamais bougé. La slide 1
 * affichait donc encore « L'élégance voilée, pensée pour vous » et son bouton
 * pointait sur /categories/voiles-hijabs — un LIEN MORT depuis que la catégorie
 * a été renommée en « hijabs » (CategoryTreeSeeder).
 *
 * Le texte de remplacement n'est pas inventé : c'est mot pour mot la promesse
 * de la maquette de la page d'accueil (rapport d'identité p.10).
 *
 * Portée volontairement étroite : seules les slides portant encore le texte
 * exact de l'ancienne marque sont reprises, et rien n'est supprimé. Une slide
 * que la cliente aura réécrite entre-temps n'est pas touchée, et la migration
 * peut être rejouée sans effet (idempotente).
 *
 * Les photographies ne sont pas retirées : arbitrage de la cliente du
 * 2026-07-21, le hero reste photographique malgré l'annotation 04 de la p.10
 * qui décrit un fond abstrait inspiré du pli.
 */
return new class extends Migration
{
    private const ANCIEN_TITRE = "L'élégance voilée, pensée pour vous";

    private const NOUVEAU = [
        'title' => "L'élégance dans la pudeur",
        'subtitle' => 'Une sélection de hijabs, foulards, accessoires et parfums pensée pour accompagner chaque silhouette avec distinction.',
        'cta_label' => 'Découvrir la collection',
        'cta_url' => '/catalogue',
    ];

    public function up(): void
    {
        DB::table('hero_slides')
            ->where('title', self::ANCIEN_TITRE)
            ->update(self::NOUVEAU + ['updated_at' => now()]);

        // Filet de sécurité indépendant du texte : toute slide encore branchée
        // sur l'ancien slug de catégorie repart vers le catalogue plutôt que
        // vers une 404, y compris si son titre a été réécrit entre-temps.
        DB::table('hero_slides')
            ->where('cta_url', 'like', '%voiles-hijabs%')
            ->update(['cta_url' => '/catalogue', 'updated_at' => now()]);
    }

    public function down(): void
    {
        DB::table('hero_slides')
            ->where('title', self::NOUVEAU['title'])
            ->update([
                'title' => self::ANCIEN_TITRE,
                'subtitle' => 'Voiles, parfums et accessoires choisis avec soin, pour une élégance moderne et raffinée.',
                'cta_label' => 'Découvrir les voiles',
                'cta_url' => '/categories/voiles-hijabs',
                'updated_at' => now(),
            ]);
    }
};
