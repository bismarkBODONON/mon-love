<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('love_letters', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('content');            // Contenu riche (HTML simple depuis un éditeur)
            $table->string('category')->default('general'); // ex: anniversaire, excuse, simple pensée
            $table->boolean('is_favorite')->default(false);
            $table->string('voice_note_path')->nullable();
            $table->json('photos')->nullable();
            $table->timestamp('written_at')->useCurrent();
            $table->timestamps();

            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('love_letters');
    }
};
