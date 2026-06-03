<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        if (!Schema::hasColumn('users', 'hak_akses')) {
            $table->string('hak_akses')->nullable()->after('status');
        }
    });
}

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('hak_akses');
        });
    }
};
