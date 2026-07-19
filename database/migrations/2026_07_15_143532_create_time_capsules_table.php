<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('time_capsules', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->date('open_date');
            $table->timestamps();

            $table->index('open_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('time_capsules');
    }
};