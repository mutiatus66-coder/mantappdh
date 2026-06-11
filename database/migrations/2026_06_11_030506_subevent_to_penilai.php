<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_add_sub_event_id_to_penilai_table.php
    public function up(): void
        {
            Schema::table('penilai', function (Blueprint $table) {
                $table->foreignId('sub_event_id')->nullable()->constrained('sub_events')->nullOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            });
        }

    public function down(): void
        {
            Schema::table('penilai', function (Blueprint $table) {
                $table->dropForeignIdFor(\App\Models\SubEvent::class);
                $table->dropForeignIdFor(\App\Models\User::class);
                $table->dropColumn(['sub_event_id', 'user_id']);
            });
        }
};
