<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usulans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('sub_event_id')->constrained('sub_events');
            $table->string('judul');
            $table->string('inovator');
            $table->string('nama_inovasi');
            $table->string('nama_tim')->nullable();
            $table->string('ketua_nama');
            $table->string('ketua_email');
            $table->string('ketua_wa')->nullable();
            $table->string('kategori')->default('umum');
            $table->enum('status', ['Melengkapi Data', 'Sedang Dinilai', 'Selesai'])->default('Melengkapi Data');
            $table->boolean('is_submitted')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usulans');
    }
};
