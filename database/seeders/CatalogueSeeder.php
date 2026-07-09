<?php

namespace Database\Seeders;

use App\Models\Catalogue\Category;
use App\Models\Catalogue\Product;
use App\Models\Catalogue\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CatalogueSeeder extends Seeder
{
    /**
     * Données de démonstration réalistes (noms/descriptions en français,
     * cohérentes avec l'identité de la boutique) plutôt que du texte Faker
     * générique — un catalogue qui ressemble à une vraie boutique, pas à un
     * jeu de données de test, est essentiel pour présenter le site à la
     * cliente. Les vraies fiches produit (photos, textes définitifs) restent
     * à saisir par la cliente une fois le back-office livré ; ceci n'est
     * qu'un jeu de démonstration cohérent en attendant.
     */
    public function run(): void
    {
        $voiles = Category::factory()->create([
            'name' => 'Voiles & Hijabs',
            'slug' => 'voiles-hijabs',
            'description' => 'Voiles, hijabs et turbans pour toutes les occasions.',
            'position' => 1,
        ]);

        $voilesEnSoie = Category::factory()->create([
            'name' => 'Voiles en soie',
            'slug' => 'voiles-en-soie',
            'description' => 'Une matière noble pour un tombé impeccable.',
            'parent_id' => $voiles->id,
            'position' => 1,
        ]);

        $parfums = Category::factory()->create([
            'name' => 'Parfums',
            'slug' => 'parfums',
            'description' => 'Des fragrances raffinées, entre tradition et modernité.',
            'position' => 2,
        ]);

        $sacs = Category::factory()->create([
            'name' => 'Sacs à main',
            'slug' => 'sacs-a-main',
            'description' => 'Des sacs élégants pour accompagner chaque tenue.',
            'position' => 3,
        ]);

        $this->seedProducts($voiles, [
            ['name' => 'Hijab Jersey Ivoire', 'description' => 'Un hijab en jersey extensible, doux et facile à draper, pour un maintien parfait toute la journée.', 'price' => 8000],
            ['name' => 'Voile Mousseline Bordeaux', 'description' => 'Voile léger en mousseline, idéal pour les journées chaudes, drapé fluide et élégant.', 'price' => 6500],
            ['name' => 'Turban Croisé Doré', 'description' => 'Turban prêt-à-porter avec bande croisée à liseré doré, pour un style affirmé sans effort.', 'price' => 5000],
        ], 'voile');

        $this->seedProducts($voilesEnSoie, [
            ['name' => 'Voile Soie Naturelle Beige', 'description' => 'Voile en soie naturelle, tombé fluide et brillance délicate, pour les grandes occasions.', 'price' => 15000],
            ['name' => 'Foulard Soie Imprimé Or', 'description' => 'Foulard en soie à motifs dorés délicats, une touche de raffinement au quotidien.', 'price' => 13000],
        ], 'voile');

        $this->seedProducts($parfums, [
            ['name' => 'Eau de Parfum Ambre Doré', 'description' => 'Notes chaudes d\'ambre et de vanille, un sillage enveloppant qui persiste toute la journée.', 'price' => 25000],
            ['name' => 'Musc Blanc Intense', 'description' => 'Un musc blanc pur et délicat, pour une signature olfactive discrète et raffinée.', 'price' => 18000],
            ['name' => 'Parfum Oud Royal', 'description' => 'Un oud intense et boisé, pour celles qui aiment les fragrances de caractère.', 'price' => 32000],
        ], 'parfum');

        $this->seedProducts($sacs, [
            ['name' => 'Sac Bandoulière Cuir Beige', 'description' => 'Sac bandoulière en cuir souple, format pratique pour le quotidien, finitions soignées.', 'price' => 22000],
            ['name' => 'Pochette Soirée Dorée', 'description' => 'Pochette compacte à fermoir doré, parfaite pour accompagner une tenue de soirée.', 'price' => 14000],
            ['name' => 'Sac Cabas Tissé Bordeaux', 'description' => 'Grand cabas tissé, spacieux et résistant, pour toutes les journées bien remplies.', 'price' => 19000],
        ], 'sac');
    }

    /**
     * @param  array<int, array{name: string, description: string, price: int}>  $products
     */
    private function seedProducts(Category $category, array $products, string $variantType): void
    {
        foreach ($products as $data) {
            $product = Product::factory()->for($category)->create([
                'name' => $data['name'],
                'slug' => Str::slug($data['name']),
                'description' => $data['description'],
                'price' => $data['price'],
            ]);

            $variants = match ($variantType) {
                'parfum' => array_map(
                    fn (string $contenance) => ['contenance' => $contenance],
                    ['30 ml', '50 ml', '100 ml']
                ),
                default => array_map(
                    fn (string $couleur) => ['couleur' => $couleur],
                    ['Ivoire', 'Bordeaux', 'Doré']
                ),
            };

            foreach (array_slice($variants, 0, 2) as $attributes) {
                ProductVariant::factory()->for($product)->create(['attributes' => $attributes]);
            }
        }
    }
}
