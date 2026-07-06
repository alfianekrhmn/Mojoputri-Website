<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Increase pg_grade, bk_grade, bm_grade, and mb_grade column sizes
     * to support longer grade names like "Grade A (Bagian Badan)".
     */
    public function up(): void
    {
        Schema::table('product_grades', function (Blueprint $table) {
            $table->string('pg_grade', 100)->change();
        });

        Schema::table('ms_barang', function (Blueprint $table) {
            $table->string('mb_grade', 100)->change();
        });

        if (Schema::hasColumn('barang_keluar', 'bk_grade')) {
            Schema::table('barang_keluar', function (Blueprint $table) {
                $table->string('bk_grade', 100)->nullable()->change();
            });
        }

        if (Schema::hasColumn('barang_masuk', 'bm_grade')) {
            Schema::table('barang_masuk', function (Blueprint $table) {
                $table->string('bm_grade', 100)->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_grades', function (Blueprint $table) {
            $table->string('pg_grade', 10)->change();
        });

        Schema::table('ms_barang', function (Blueprint $table) {
            $table->string('mb_grade', 50)->change();
        });

        if (Schema::hasColumn('barang_keluar', 'bk_grade')) {
            Schema::table('barang_keluar', function (Blueprint $table) {
                $table->string('bk_grade', 10)->nullable()->change();
            });
        }

        if (Schema::hasColumn('barang_masuk', 'bm_grade')) {
            Schema::table('barang_masuk', function (Blueprint $table) {
                $table->string('bm_grade', 10)->nullable()->change();
            });
        }
    }
};
