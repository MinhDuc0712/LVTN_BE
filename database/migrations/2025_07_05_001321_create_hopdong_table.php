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
        Schema::create('hopdong', function (Blueprint $table) {
            $table->id();
            $table->foreignId('phong_id')->constrained()->onDelete('cascade');
            $table->foreignId('khach_id')->constrained()->onDelete('cascade');
            $table->date('ngay_bat_dau');
            $table->date('ngay_ket_thuc')->nullable();
            $table->decimal('tien_coc', 10, 2);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hopdong');
    }
};
