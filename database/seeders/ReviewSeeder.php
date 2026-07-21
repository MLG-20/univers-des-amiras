<?php

namespace Database\Seeders;

use App\Models\Content\Review;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        // Avis de démonstration — modifiables/supprimables depuis l'admin.
        // On n'insère que si la table est vide, pour ne pas dupliquer à chaque run.
        if (Review::query()->exists()) {
            return;
        }

        $reviews = [
            ['author_name' => 'Aminata D.', 'location' => 'Dakar', 'rating' => 5, 'comment' => "Un voile d'une qualité incroyable, exactement comme sur les photos. Livraison rapide et emballage soigné, j'ai été bluffée.", 'position' => 0],
            ['author_name' => 'Fatou S.', 'location' => 'Thiès', 'rating' => 5, 'comment' => "Le paiement à la livraison m'a mise en confiance pour ma première commande. Je recommande les yeux fermés !", 'position' => 1],
            ['author_name' => 'Khadija B.', 'location' => 'Mbour', 'rating' => 5, 'comment' => 'Des matières douces, des couleurs magnifiques et une équipe adorable qui répond à toutes les questions.', 'position' => 2],
            ['author_name' => 'Mariama N.', 'location' => 'Dakar', 'rating' => 4, 'comment' => 'Très satisfaite de mon parfum, il tient toute la journée. Je reviendrai pour les accessoires.', 'position' => 3],
        ];

        foreach ($reviews as $review) {
            Review::create($review + ['is_published' => true]);
        }
    }
}
