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
        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->integer('bm_berat')->nullable()->after('bm_mb_id');
            $table->integer('bm_jumlah')->nullable()->after('bm_berat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->dropColumn(['bm_berat', 'bm_jumlah']);
        });
    }
};
