<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('auth_title')->nullable();
            $table->text('auth_subtitle')->nullable();
            $table->string('auth_image_path')->nullable();
        });

        // La ligne singleton existe déjà : on la remplit avec les textes par
        // défaut (firstOrCreate ne réapplique pas les défauts à une ligne
        // existante), pour que les pages login/register ne soient jamais vides.
        DB::table('site_settings')
            ->whereNull('auth_title')
            ->update(['auth_title' => "L'élégance voilée, pensée pour vous."]);

        DB::table('site_settings')
            ->whereNull('auth_subtitle')
            ->update(['auth_subtitle' => 'Suivez vos commandes, gérez vos adresses et retrouvez votre univers en un instant.']);
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn(['auth_title', 'auth_subtitle', 'auth_image_path']);
        });
    }
};
