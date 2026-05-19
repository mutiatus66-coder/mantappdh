<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('indikator_tahap2', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sub_event_id')->constrained('sub_events')->onDelete('cascade');
            $table->string('nama_indikator');
            $table->enum('jenis', ['Subtansi Inovasi', 'Peragaan'])->default('Subtansi Inovasi');
            $table->timestamps();
        });

        Schema::create('keterangan_tahap2', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indikator_tahap2_id')->constrained('indikator_tahap2')->onDelete('cascade');
            $table->text('keterangan');
            $table->integer('nilai_minimal');
            $table->integer('nilai_maksimal');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keterangan_tahap2');
        Schema::dropIfExists('indikator_tahap2');
    }
};