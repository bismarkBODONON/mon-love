<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('musics', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('artist')->nullable();
            $table->string('cover_path')->nullable();
            $table->string('audio_path');
            $table->longText('lyrics')->nullable();
            $table->unsignedInteger('duration')->nullable(); // en secondes
            $table->boolean('is_favorite')->default(false);
            $table->unsignedInteger('position')->default(0); // ordre playlist
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('musics');
    }
};