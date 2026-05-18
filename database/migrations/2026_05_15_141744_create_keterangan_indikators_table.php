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
       Schema::create('keterangan_indikators', function (Blueprint $table) {
    $table->id();
    $table->foreignId('indikator_id')->constrained('indikators')->onDelete('cascade');
    $table->string('keterangan');
    $table->integer('nilai_minimal');
    $table->integer('nilai_maksimal');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keterangan_indikators');
    }
};
