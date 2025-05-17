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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id('MaThongBao');
            $table->foreignId('MaNguoiDung')->constrained('users')->onDelete('cascade');
            $table->text('NoiDung');
            $table->enum('TrangThai', ['Chưa đọc', 'Đã đọc'])->default('Chưa đọc');
            $table->enum('LoaiThongBao', ['Thông báo', 'Cảnh báo','Hệ thống'])->default('Thông báo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
