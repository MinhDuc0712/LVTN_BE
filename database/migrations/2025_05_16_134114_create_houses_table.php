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
        Schema::create('houses', function (Blueprint $table) {
            $table->id('MaNha');
            $table->string('TieuDe');
            $table->string('Tinh_TP');
            $table->string('Quan_Huyen');
            $table->string('Phuong_Xa');
            $table->string('Duong')->nullable();
            $table->string('DiaChi');
            $table->integer('SoPhongNgu');
            $table->integer('SoPhongTam');
            $table->integer('SoTang')->nullable();
            $table->float('DienTich');
            $table->float('Gia');
            $table->timestamp('NgayDang')->useCurrent();
            $table->date('NgayHetHan')->nullable();
            $table->enum('TrangThai', ['Đang chờ thanh toán', 'Đang xử lý', 'Đã duyệt', 'Đã từ chối', 'Đã cho thuê', 'Đã ẩn', 'Tin hết hạn', 'Đã xóa'])->default('Đang chờ thanh toán');
            $table->boolean('NoiBat')->default(false);
            $table->text('MoTaChiTiet')->nullable();
            // $table->longText('HinhAnh')->nullable();
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
