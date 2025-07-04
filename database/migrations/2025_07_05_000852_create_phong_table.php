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
        Schema::create('phong', function (Blueprint $table) {
            $table->id('ma_phong');
            $table->float('dien_tich');
            $table->text('mo_ta')->nullable();
            $table->integer('tang');
            $table->decimal('gia', 10, 2);
            $table->string('hinh_anh')->nullable();
            $table->string('trang_thai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phong');
    }
};
