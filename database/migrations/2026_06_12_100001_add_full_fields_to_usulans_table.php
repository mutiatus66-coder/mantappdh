<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usulans', function (Blueprint $table) {
            $table->unsignedBigInteger('bidang_id')->nullable()->after('sub_event_id');
            $table->string('interaksi', 100)->nullable()->after('bidang_id');
            $table->string('alamat_ketua', 255)->nullable()->after('ketua_email');
            $table->string('ktp', 50)->nullable()->after('alamat_ketua');
            $table->string('asal_sekolah', 255)->nullable()->after('ktp');
            $table->string('nama_guru', 150)->nullable()->after('asal_sekolah');
            // Halaman 2
            $table->text('latar_belakang')->nullable()->after('nama_guru');
            $table->text('kondisi_sebelumnya')->nullable()->after('latar_belakang');
            $table->text('sasaran_tujuan')->nullable()->after('kondisi_sebelumnya');
            $table->text('materi_inovasi')->nullable()->after('sasaran_tujuan');
            $table->text('deskripsi')->nullable()->after('materi_inovasi');
            $table->text('bahan_baku')->nullable()->after('deskripsi');
            $table->text('cara_kerja')->nullable()->after('bahan_baku');
            $table->text('keunggulan')->nullable()->after('cara_kerja');
            $table->text('hasil_diharapkan')->nullable()->after('keunggulan');
            $table->text('manfaat')->nullable()->after('hasil_diharapkan');
            $table->text('rencana_berkelanjutan')->nullable()->after('manfaat');
            // Halaman 3
            $table->string('file_surat_pernyataan', 255)->nullable()->after('rencana_berkelanjutan');
            $table->string('file_proposal', 255)->nullable()->after('file_surat_pernyataan');
            $table->string('file_gambar', 255)->nullable()->after('file_proposal');
            $table->string('link_video', 255)->nullable()->after('file_gambar');
        });
    }

    public function down(): void
    {
        Schema::table('usulans', function (Blueprint $table) {
            $table->dropColumn([
                'bidang_id','interaksi','alamat_ketua','ktp','asal_sekolah','nama_guru',
                'latar_belakang','kondisi_sebelumnya','sasaran_tujuan','materi_inovasi',
                'deskripsi','bahan_baku','cara_kerja','keunggulan','hasil_diharapkan',
                'manfaat','rencana_berkelanjutan',
                'file_surat_pernyataan','file_proposal','file_gambar','link_video',
            ]);
        });
    }
};
