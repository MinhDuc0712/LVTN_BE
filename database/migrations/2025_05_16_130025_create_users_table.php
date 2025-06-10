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
        Schema::create('users', function (Blueprint $table) {
            $table->id('MaNguoiDung');
            $table->string('HoTen');
            $table->string('Email')->unique();
            $table->string('SDT',20);
            $table->string('Password');
            $table->longText('HinhDaiDien')->nullable();           
            $table->text('DiaChi')->nullable();
            $table->float('so_du')->nullable();
            $table->enum('TrangThai', ['Đang hoạt động', 'Tạm khóa', 'Bị cấm'])->default('Đang hoạt động');
            $table->text('LyDoCam')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
