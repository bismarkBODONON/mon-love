<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_settings', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->enum('theme_mode', ['light', 'dark', 'system'])->default('system');
            $table->string('color_theme')->default('pink'); // pink, purple, teal, blue...
            $table->string('font_family')->default('default');
            $table->string('wallpaper')->nullable(); // chemin/URL de l'image de fond
            $table->json('preset_messages')->nullable(); // ["Bonjour ❤️", ...]
            $table->boolean('music_enabled')->default(true);
            $table->boolean('notifications_enabled')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_settings');
    }
};