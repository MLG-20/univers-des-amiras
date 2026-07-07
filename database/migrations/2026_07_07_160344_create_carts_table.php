<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            // Un seul des deux est renseigné : un panier invité n'a que
            // session_id, un panier de compte n'a que user_id (posé lors de
            // la fusion à la connexion). Les deux colonnes sont uniques pour
            // qu'une recherche ne puisse jamais résoudre plus d'un « panier
            // courant » pour un visiteur/compte donné.
            $table->foreignId('user_id')->nullable()->unique()->constrained()->cascadeOnDelete();
            $table->string('session_id')->nullable()->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
