<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            // Ligne unique (singleton) : un seul enregistrement gère tout le
            // contenu éditable hors hero/catalogue (bandeau de réassurance,
            // page À propos, coordonnées de contact).
            $table->json('trust_items')->nullable();
            $table->text('about_story')->nullable();
            $table->string('about_quote')->nullable();
            $table->json('about_values')->nullable();
            $table->string('contact_whatsapp')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_address')->nullable();
            $table->text('contact_hours')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
