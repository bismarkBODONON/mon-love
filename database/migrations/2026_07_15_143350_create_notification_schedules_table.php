<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('message');
            $table->time('send_time');
            $table->enum('frequency', ['once', 'daily', 'weekly'])->default('daily');
            $table->json('days_of_week')->nullable(); // ex: [1,3,5] (lundi=1 ... dimanche=7), utilisé si weekly
            $table->date('send_date')->nullable(); // utilisé si frequency = once
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_schedules');
    }
};