<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration

{public function up(): void
{
    Schema::table('formulasi_tahap1', function (Blueprint $table) {
        $table->renameColumn('nilai_inovasi', 'nilai_makalah');
        $table->renameColumn('nilai_presentasi', 'nilai_substansi');
    });
}

public function down(): void
{
    Schema::table('formulasi_tahap1', function (Blueprint $table) {
        $table->renameColumn('nilai_makalah', 'nilai_inovasi');
        $table->renameColumn('nilai_substansi', 'nilai_presentasi');
    });
}
};
