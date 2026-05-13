<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sub_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->integer('tahun');
            $table->string('sub_event');
            $table->string('kategori')->default('SEMUA BIDANG');
            $table->date('mulai');
            $table->date('berakhir');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sub_events');
    }
};