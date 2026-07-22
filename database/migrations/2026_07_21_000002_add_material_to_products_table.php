<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Niveau 03 de la hiérarchie produit du rapport d'identité (p.3),
            // entre le nom et le prix : « Modal souple - tombé fluide ».
            //
            // Ce n'est pas une caractéristique technique de plus : le principe
            // non négociable n°3 (p.16) veut que la matière et le tombé soient
            // MONTRÉS, et la p.12 range la lecture produit comme « visuel,
            // catégorie, nom, prix et variante ». D'où une phrase courte, libre
            // et éditoriale — pas une liste fermée de matières.
            //
            // Le filtre « Matière » de la maquette (p.12) est un autre objet :
            // il suppose une valeur normalisée et arrive en Phase 2.2. Cette
            // colonne ne le remplace pas et ne le bloque pas.
            $table->string('material')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('material');
        });
    }
};
