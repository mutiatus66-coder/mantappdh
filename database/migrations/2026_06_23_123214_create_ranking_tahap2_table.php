<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ranking_tahap2', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usulan_id')->constrained('usulans')->onDelete('cascade');
            $table->foreignId('penilai_id')->constrained('penilais')->onDelete('cascade');
            $table->unsignedSmallInteger('ranking');
            $table->timestamps();

            $table->unique(['usulan_id', 'penilai_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ranking_tahap2');
    }
};