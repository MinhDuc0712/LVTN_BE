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
            $table->decimal('so_tien',15,2);
            $table->decimal('khuyen_mai',15,2)->nullable();
            $table->decimal('thuc_nhan',15,2);
            $table->string('phuong_thuc');
            $table->string('ma_giao_dich')->unique();
            $table->string('trang_thai');
            $table->text('ghi_chu');
            $table->timestamp('ngay_nap')->useCurrent();
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
