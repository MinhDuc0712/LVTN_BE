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
        Schema::create('houses', function (Blueprint $table) {
            $table->id('MaNha');
            $table->string('TieuDe');
            $table->string('Tinh_TP');
            $table->string('Quan_Huyen');
            $table->string('Phuong_Xa');
            $table->string('Duong');
            $table->string('DiaChi');
            $table->integer('SoPhongNgu');
            $table->integer('SoPhongTam');
            $table->integer('SoTang')->nullable();
            $table->float('DienTich');
            $table->float('Gia');
            $table->date('NgayDang');
            $table->date('NgayHetHan')->nullable();
            $table->enum('TrangThai',['Đang hoạt động','Hết hạn','Đã xóa','Đang xử lý']);
            $table->boolean('NoiBat')->default(false);
            $table->text('MoTaChiTiet')->nullable();
            $table->foreignId('MaNguoiDung')->constrained('users')->onDelete('cascade');
            $table->foreignId('MaDanhMuc')->constrained('categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('houses');
    }
};
