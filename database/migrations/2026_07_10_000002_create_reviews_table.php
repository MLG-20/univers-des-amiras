<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->string('author_name');
            $table->string('location')->nullable();
            $table->unsignedTinyInteger('rating')->nullable();
            $table->text('comment');
            // Modération : seuls les avis publiés apparaissent sur le site.
            $table->boolean('is_published')->default(true);
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();

            $table->index(['is_published', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
