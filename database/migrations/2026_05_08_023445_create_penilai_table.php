<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('penilai', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sub_event_id'); // Pastikan ada kolom ini
            $table->foreign('sub_event_id')->references('id')->on('sub_events')->onDelete('cascade');

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->string('nama');
            $table->string('email'); // ✅ HAPUS ->unique() DI SINI
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('penilai');
    }
};
