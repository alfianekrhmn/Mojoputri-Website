<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Add grade column to barang_masuk and barang_keluar tables.
     * Also add hpp (cost price) and profit columns to barang_keluar for revenue calculation.
     */
    public function up(): void
    {
        // Add grade to barang_masuk
        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->string('bm_grade', 10)->nullable()->after('bm_mb_id'); // 'A', 'B', 'C'
        });

        // Add grade, hpp, profit to barang_keluar
        Schema::table('barang_keluar', function (Blueprint $table) {
            $table->string('bk_grade', 10)->nullable()->after('bk_mb_id');    // 'A', 'B', 'C'
            $table->decimal('bk_hpp', 12, 2)->default(0)->after('bk_qty');    // HPP per kg
            $table->decimal('bk_profit', 12, 2)->default(0)->after('bk_hpp'); // Keuntungan per kg
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->dropColumn('bm_grade');
        });

        Schema::table('barang_keluar', function (Blueprint $table) {
            $table->dropColumn(['bk_grade', 'bk_hpp', 'bk_profit']);
        });
    }
};
