<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        if (!Schema::hasColumn('users', 'nama')) {
            $table->string('nama')->after('id');
        }
        if (!Schema::hasColumn('users', 'hak_akses')) {
            $table->string('hak_akses')->default('user')->after('email');
        }
        if (!Schema::hasColumn('users', 'status')) {
            $table->string('status')->default('aktif')->after('hak_akses');
        }
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['nama', 'hak_akses', 'status']);
    });
}

};
