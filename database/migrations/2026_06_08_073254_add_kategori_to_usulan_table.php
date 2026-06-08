<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usulans', function (Blueprint $table) {
            if (!Schema::hasColumn('usulans', 'kategori')) {
                $table->string('kategori')->default('umum')->after('status');
                // kategori: 'umum' atau 'pelajar'
            }
        });
    }

    public function down(): void
    {
        Schema::table('usulans', function (Blueprint $table) {
            $table->dropColumn('kategori');
        });
    }
};