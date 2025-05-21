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
        Schema::create('deposit_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ma_nguoi_dung');
            $table->decimal('so_tien',15,2);
            $table->float('khuyen_mai')->nullable();
            $table->float('thuc_nhan');
            $table->string('phuong_thuc');
            $table->string('ma_giao_dich')->unique();
            $table->string('trang_thai');
            $table->text('ghi_chu')->nullable();
            $table->timestamp('ngay_nap')->useCurrent();
            $table->foreign('ma_nguoi_dung')->references('MaNguoiDung')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deposit_history');
    }
};
