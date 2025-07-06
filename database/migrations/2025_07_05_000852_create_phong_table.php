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
            $table->id();
            $table->string('ten_phong')->unique(); 
            $table->float('dien_tich');
            $table->text('mo_ta')->nullable();
            $table->integer('tang');
            $table->decimal('gia', 10, 2);
            $table->enum('trang_thai', ['trong', 'da_thue', 'bao_tri'])->default('trong');
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
