<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ── penilaian_usulan ──────────────────────────────────────────────
        Schema::table('penilaian_usulan', function (Blueprint $table) {
            // Drop unique constraint dan FK lama
            $table->dropUnique('unique_penilaian_usulan');
            $table->dropForeign(['inovator_id']);
        });

        Schema::table('penilaian_usulan', function (Blueprint $table) {
            // Rename kolom inovator_id → usulan_id
            $table->renameColumn('inovator_id', 'usulan_id');
        });

        Schema::table('penilaian_usulan', function (Blueprint $table) {
            // Tambah FK baru ke usulans
            $table->foreign('usulan_id')->references('id')->on('usulans')->onDelete('cascade');
            // Tambah kembali unique constraint dengan nama baru
            $table->unique(['usulan_id', 'penilai_id', 'keterangan_indikator_id'], 'unique_penilaian_usulan');
        });

        // ── penilaian_pemenang ────────────────────────────────────────────
        Schema::table('pemenang', function (Blueprint $table) {
            $table->dropUnique('unique_penilaian_pemenang');
            $table->dropForeign(['inovator_id']);
        });

        Schema::table('pemenang', function (Blueprint $table) {
            $table->renameColumn('inovator_id', 'usulan_id');
        });

        Schema::table(' pemenang', function (Blueprint $table) {
            $table->foreign('usulan_id')->references('id')->on('usulans')->onDelete('cascade');
            $table->unique(['usulan_id', 'penilai_id', 'keterangan_tahap2_id'], 'unique_penilaian_pemenang');
        });
    }

    public function down(): void
    {
        Schema::table('penilaian_usulan', function (Blueprint $table) {
            $table->dropUnique('unique_penilaian_usulan');
            $table->dropForeign(['usulan_id']);
            $table->renameColumn('usulan_id', 'inovator_id');
            $table->foreign('inovator_id')->references('id')->on('inovator')->onDelete('cascade');
            $table->unique(['inovator_id', 'penilai_id', 'keterangan_indikator_id'], 'unique_penilaian_usulan');
        });

        Schema::table('pemenang', function (Blueprint $table) {
            $table->dropUnique('unique_penilaian_pemenang');
            $table->dropForeign(['usulan_id']);
            $table->renameColumn('usulan_id', 'inovator_id');
            $table->foreign('inovator_id')->references('id')->on('inovator')->onDelete('cascade');
            $table->unique(['inovator_id', 'penilai_id', 'keterangan_tahap2_id'], 'unique_penilaian_pemenang');
        });
    }
};