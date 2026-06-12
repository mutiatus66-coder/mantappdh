<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penilaian_usulan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inovator_id')->constrained('inovator')->onDelete('cascade');
            $table->foreignId('penilai_id')->constrained('penilai')->onDelete('cascade');
            $table->foreignId('keterangan_indikator_id')->constrained('keterangan_indikators')->onDelete('cascade');
            $table->integer('nilai');
            $table->timestamps();

            // Satu penilai hanya boleh input satu nilai per keterangan per inovator
            $table->unique(['inovator_id', 'penilai_id', 'keterangan_indikator_id'], 'unique_penilaian_usulan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaian_usulan');
    }
};