<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('timeline_events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('event_date');
            $table->string('location')->nullable();
            $table->boolean('is_favorite')->default(false);

            // On stocke les chemins des médias en JSON plutôt que de créer
            // une table "media" séparée pour l'instant : c'est du YAGNI
            // volontaire (You Aren't Gonna Need It) tant qu'on n'a pas
            // besoin de requêtes complexes sur les médias eux-mêmes.
            // Si un jour la Galerie a besoin de partager ces médias,
            // on migrera vers une vraie table polymorphique "media".
            $table->json('photos')->nullable();
            $table->json('videos')->nullable();
            $table->json('voice_notes')->nullable();

            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index('event_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timeline_events');
    }
};
