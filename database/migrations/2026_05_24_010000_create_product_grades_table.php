<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * Stores per-grade stock and price for each product in ms_barang.
     */
    public function up(): void
    {
        Schema::create('product_grades', function (Blueprint $table) {
            $table->id('pg_id');
            $table->foreignId('pg_mb_id')->constrained('ms_barang', 'mb_id')->onDelete('cascade');
            $table->string('pg_grade', 10); // 'A', 'B', 'C'
            $table->integer('pg_stock')->default(0); // in kg
            $table->decimal('pg_hpp', 12, 2)->default(0);        // Harga Pokok Produksi (HPP)
            $table->decimal('pg_profit', 12, 2)->default(0);     // Keuntungan per kg
            $table->timestamps();

            // Each product can only have one row per grade
            $table->unique(['pg_mb_id', 'pg_grade']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_grades');
    }
};
