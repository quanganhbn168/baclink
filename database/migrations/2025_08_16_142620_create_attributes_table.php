<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('type')->default('button')->comment('Kiểu hiển thị: button, dropdown, color_swatch, image_swatch');
            $table->boolean('is_variant_defining')->default(false)->comment('Công tắc để phân biệt thuộc tính tạo biến thể và thuộc tính lọc');
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('attributes');
    }
};