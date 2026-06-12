<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemenang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inovator_id')->constrained('inovator')->onDelete('cascade');
            $table->foreignId('penilai_id')->constrained('penilai')->onDelete('cascade');
            $table->foreignId('keterangan_tahap2_id')->constrained('keterangan_tahap2')->onDelete('cascade');
            $table->integer('nilai');
            $table->timestamps();

            $table->unique(['inovator_id', 'penilai_id', 'keterangan_tahap2_id'], 'unique_penilaian_pemenang');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemenang');
    }
};