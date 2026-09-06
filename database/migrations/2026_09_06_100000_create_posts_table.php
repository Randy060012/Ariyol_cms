<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();          // Résumé (cartes du blog)
            $table->text('content')->nullable();          // Corps de l'article (paragraphes séparés par une ligne vide)
            $table->string('main_image')->nullable();     // Image principale (URL ou storage)
            $table->json('gallery')->nullable();          // Autres images : ['storage/posts/xxx.jpg', ...]
            $table->string('author', 120)->nullable();
            $table->string('category', 80)->nullable();
            $table->boolean('is_published')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
