<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah kolom hak_akses jika belum ada
        if (!Schema::hasColumn('users', 'hak_akses')) {
            Schema::table('users', function (Blueprint $table) {
                $table->enum('hak_akses', ['admin', 'penilai', 'peserta', 'admin_bapperida'])
                      ->default('peserta')
                      ->after('email');
            });
        }

        // Tambah kolom status jika belum ada
        if (!Schema::hasColumn('users', 'status')) {
            Schema::table('users', function (Blueprint $table) {
                $table->enum('status', ['aktif', 'nonaktif'])->default('aktif')->after('hak_akses');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'hak_akses')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('hak_akses');
            });
        }
        if (Schema::hasColumn('users', 'status')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};
