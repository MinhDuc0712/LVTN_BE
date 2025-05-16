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
            $table->string('DiaChi');
            $table->integer('SoPhongNgu');
            $table->integer('SoPhongTam');
            $table->integer('SoTang');
            $table->float('DienTich');
            $table->float('Gia');
            $table->date('NgayDang');
            $table->enum('TrangThai',['Đang hoạt động','Hết hạn','Đã xóa' ]);
            $table->boolean('NoiBat')->default(false);
            $table->foreignId('MaNguoiDung')->constrained('users')->onDelete('cascade');
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
