<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inovator', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sub_event_id');
            $table->string('inovator');
            $table->string('nama_inovasi');
            $table->enum('kategori', ['umum', 'pelajar']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inovator');
    }
};