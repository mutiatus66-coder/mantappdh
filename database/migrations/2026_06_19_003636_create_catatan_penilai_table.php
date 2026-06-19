<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('catatan_penilai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usulan_id')->constrained('usulans')->cascadeOnDelete();
            $table->foreignId('penilai_id')->constrained('penilai')->cascadeOnDelete();
            $table->text('catatan');
            $table->timestamps();
            $table->unique(['usulan_id', 'penilai_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catatan_penilai');
    }
};
