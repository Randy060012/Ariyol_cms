<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('realisations', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();       // Texte affiché à droite de la photo
            $table->string('image')->nullable();           // Photo de l'action (URL ou storage)
            $table->date('date')->nullable();              // Date de l'action
            $table->string('location', 160)->nullable();   // Lieu (Tsévié, Zéglé-Sagonou...)
            $table->boolean('is_published')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('realisations');
    }
};
