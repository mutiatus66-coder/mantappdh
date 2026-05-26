<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengumuman', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->enum('status', ['Published', 'Draft'])->default('Draft');
            $table->string('file_path')->nullable();
            $table->timestamps();
        });

        // Insert data awal
        DB::table('pengumuman')->insert([
            [
                'judul' => 'POSTER INOTEK AWARD 2023',
                'deskripsi' => 'Pengumuman lomba poster INOTEK 2023',
                'status' => 'Published',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'judul' => 'Instruksi Bupati Magetan tentang Gerakan Magetan Berinovasi',
                'deskripsi' => 'Instruksi Bupati untuk percepatan inovasi daerah',
                'status' => 'Published',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('pengumuman');
    }
};
