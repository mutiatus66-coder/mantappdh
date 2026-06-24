<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel indikators (Tahap 1) — nilai: 'makalah' | 'substansi'
        if (Schema::hasTable('indikators') && !Schema::hasColumn('indikators', 'jenis')) {
            Schema::table('indikators', function (Blueprint $table) {
                $table->string('jenis')->default('substansi')->after('nama_indikator');
            });
        }

        // Tabel indikator_tahap2 (tanpa s) — nilai: 'Subtansi Inovasi' | 'Peragaan'
        if (Schema::hasTable('indikator_tahap2') && !Schema::hasColumn('indikator_tahap2', 'jenis')) {
            Schema::table('indikator_tahap2', function (Blueprint $table) {
                $table->string('jenis')->default('Subtansi Inovasi')->after('nama_indikator');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('indikators', 'jenis')) {
            Schema::table('indikators', function (Blueprint $table) {
                $table->dropColumn('jenis');
            });
        }

        if (Schema::hasColumn('indikator_tahap2', 'jenis')) {
            Schema::table('indikator_tahap2', function (Blueprint $table) {
                $table->dropColumn('jenis');
            });
        }
    }
};