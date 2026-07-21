<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Signal commercial optionnel (cf. App\Enums\Catalogue\ProductLabel).
            // Nullable par défaut : l'absence de label est le cas normal, le
            // rapport d'identité insistant pour qu'ils restent rares.
            $table->string('label')->nullable()->after('price');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('label');
        });
    }
};
