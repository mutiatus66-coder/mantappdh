<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
        {
            Schema::table('indikators', function (Blueprint $table) {
                $table->enum('jenis', ['makalah', 'substansi'])
                    ->default('substansi')
                    ->after('nama_indikator');
            });
        }

    public function down(): void
    {
        Schema::table('indikators', function (Blueprint $table) {
            $table->dropColumn('jenis');
        });
    }
};