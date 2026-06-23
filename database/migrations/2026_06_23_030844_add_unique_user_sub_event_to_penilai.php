<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Pastikan satu user hanya bisa menjadi penilai 1x per sub event.
     * Ini juga dibutuhkan agar query getPenilaiLogin(subEventId) selalu
     * mengembalikan tepat 1 record atau null.
     */
    public function up(): void
    {
        Schema::table('penilai', function (Blueprint $table) {
            // Hapus duplikat sebelum menambahkan constraint
            // (safe to run even if index doesn't exist yet)
            $table->unique(['user_id', 'sub_event_id'], 'penilai_user_subevent_unique');
        });
    }

    public function down(): void
    {
        Schema::table('penilai', function (Blueprint $table) {
            $table->dropUnique('penilai_user_subevent_unique');
        });
    }
};