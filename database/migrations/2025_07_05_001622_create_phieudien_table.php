<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('phieudien', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hopdong_id')->constrained()->onDelete('cascade');
            $table->integer('chi_so_dau');
            $table->integer('chi_so_cuoi');
            $table->decimal('don_gia', 10, 2);
            $table->date('thang');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phieudien');
    }
};
