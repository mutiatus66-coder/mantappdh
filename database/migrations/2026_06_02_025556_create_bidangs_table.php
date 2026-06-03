<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bidangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sub_event_id')->constrained('sub_events')->cascadeOnDelete();
            $table->string('nama');
            $table->enum('status', ['aktif', 'tidak_aktif'])->default('aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bidangs');
    }
};