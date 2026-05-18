<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formulasi_tahap2', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sub_event_id')->constrained('sub_events')->onDelete('cascade');
            $table->integer('nilai_inovasi');   // persentase
            $table->integer('nilai_peragaan');  // persentase
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formulasi_tahap2');
    }
};