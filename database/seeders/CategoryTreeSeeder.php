<?php

namespace Database\Seeders;

use App\Models\Catalogue\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

/**
 * Installe l'arborescence de la maquette Aissatou Store (rapport d'identité p.3)
 * SANS reconstruire la base.
 *
 * Pourquoi ce seeder existe : `CatalogueSeeder` ne s'applique qu'après un
 * `migrate:fresh`, ce qui effacerait les slides du hero, les réglages du site,
 * les avis, les comptes et les images téléversées. Ici on met à jour l'arbre en
 * place, en conservant les produits déjà rattachés.
 *
 * Idempotent : le rattrapage se fait par slug, on peut le rejouer sans risque.
 *
 *     php artisan db:seed --class=CategoryTreeSeeder
 */
class CategoryTreeSeeder extends Seeder
{
    /**
     * Correspondance ancien slug → nouvelle identité. Les catégories de l'ancienne
     * marque sont RENOMMÉES plutôt que recréées, pour que les produits qu'elles
     * contiennent suivent au lieu de se retrouver orphelins.
     *
     * @var array<string, array{slug: string, name: string, description: string, position: int}>
     */
    private const RENAMES = [
        'voiles-hijabs' => [
            'slug' => 'hijabs',
            'name' => 'Hijabs',
            'description' => 'Le geste quotidien : des hijabs choisis pour leur tombé et leur confort.',
            'position' => 1,
        ],
        'voiles-en-soie' => [
            'slug' => 'foulards',
            'name' => 'Foulards',
            'description' => 'La matière et le mouvement, en soie, en modal ou en laine.',
            'position' => 2,
        ],
    ];

    /**
     * Catégories de la maquette absentes de l'ancien arbre : créées vides.
     *
     * @var array<int, array{slug: string, name: string, description: string, position: int}>
     */
    private const CREATES = [
        [
            'slug' => 'cols',
            'name' => 'Cols',
            'description' => 'La structure de silhouette : des cols qui achèvent une tenue.',
            'position' => 3,
        ],
        [
            'slug' => 'parfums',
            'name' => 'Parfums',
            'description' => 'La trace olfactive, entre tradition et modernité.',
            'position' => 4,
        ],
        [
            'slug' => 'cadeaux',
            'name' => 'Cadeaux',
            'description' => "L'art d'offrir : des objets choisis, présentés avec soin.",
            'position' => 5,
        ],
    ];

    public function run(): void
    {
        foreach (self::RENAMES as $oldSlug => $attributes) {
            $category = Category::where('slug', $oldSlug)->first();

            if (! $category) {
                continue;
            }

            // La maquette présente les cinq univers au même niveau : une
            // sous-catégorie promue doit perdre son parent.
            $category->update($attributes + ['parent_id' => null]);

            $this->command?->info("Renommée : {$oldSlug} → {$attributes['slug']} ({$category->products()->count()} produit·s conservé·s)");
        }

        foreach (self::CREATES as $attributes) {
            $category = Category::firstOrNew(['slug' => $attributes['slug']]);

            if ($category->exists) {
                // La catégorie a pu être personnalisée en admin : on ne touche ni
                // au nom ni à la description. En revanche l'ORDRE vient de la
                // maquette (p.3) et doit être appliqué, sans quoi Parfums
                // resterait devant Cols.
                $category->update([
                    'position' => $attributes['position'],
                    'parent_id' => null,
                ]);

                $this->command?->info("Repositionnée : {$attributes['slug']} (rang {$attributes['position']})");

                continue;
            }

            $category->fill($attributes + ['parent_id' => null, 'is_active' => true])->save();

            $this->command?->info("Créée : {$attributes['slug']}");
        }

        // Les catégories hors maquette (ex. « Sacs à main ») ne sont PAS supprimées :
        // elles contiennent des produits, et décider de leur sort appartient à la
        // cliente. On les repousse simplement en fin de navigation.
        $extras = Category::whereNotIn('slug', ['hijabs', 'foulards', 'cols', 'parfums', 'cadeaux'])->get();

        foreach ($extras as $extra) {
            $extra->update(['position' => 90]);
            $this->command?->warn("Hors maquette, conservée en fin de liste : {$extra->slug} ({$extra->products()->count()} produit·s) — à arbitrer avec la cliente.");
        }

        // La navigation est mise en cache (cf. AppServiceProvider::navCategories).
        Cache::forget('shop.nav_categories');
    }
}
