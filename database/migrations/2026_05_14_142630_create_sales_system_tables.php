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
        // 1. Tabel Master Barang
        Schema::create('ms_barang', function (Blueprint $table) {
            $table->id('mb_id');
            $table->string('mb_desc'); // Nama Produk
            $table->string('mb_grade', 50); // Grade Produk
            $table->timestamps();
        });

        // 2. Tabel Barang Masuk (Inventory)
        Schema::create('barang_masuk', function (Blueprint $table) {
            $table->id('bm_id');
            $table->foreignId('bm_mb_id')->constrained('ms_barang', 'mb_id')->onDelete('cascade');
            $table->integer('bm_qty');
            $table->date('bm_date');
            $table->timestamps();
        });

        // 3. Tabel Barang Keluar (Sales)
        Schema::create('barang_keluar', function (Blueprint $table) {
            $table->id('bk_id');
            $table->foreignId('bk_mb_id')->constrained('ms_barang', 'mb_id')->onDelete('cascade');
            $table->integer('bk_qty');
            $table->date('bk_date');
            $table->decimal('bk_total_harga', 15, 2)->default(0); // Pendukung KPI Total Sales
            $table->timestamps();
        });

        // 4. Tabel Akun & Role
        Schema::create('ms_account', function (Blueprint $table) {
            $table->id('ma_id');
            $table->string('ma_user')->unique();
            $table->string('ma_pass');
            $table->enum('role', ['admin', 'owner']); // Mendukung Login Role
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_system_tables');
    }
};
