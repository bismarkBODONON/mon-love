<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table "singleton" : une seule ligne existe toujours pour cette table.
 * Pourquoi une table dédiée plutôt que des champs sur "users" ?
 * -> Parce que ce n'est pas une donnée d'utilisateur, c'est une donnée
 *    "de couple" : elle ne dépend d'aucun compte en particulier et sera
 *    réutilisée par plusieurs écrans (Home, Compteur, Calendrier).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('relationship_settings', function (Blueprint $table) {
            $table->id();
            $table->string('partner_name');           // Nom affiché de l'être aimé
            $table->date('relationship_start_date');  // Point de départ du compteur
            $table->string('pin_hash')->nullable();    // PIN hashé (bcrypt), jamais en clair
            $table->string('accent_color')->default('#FF4D8D'); // Personnalisation thème
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('relationship_settings');
    }
};
