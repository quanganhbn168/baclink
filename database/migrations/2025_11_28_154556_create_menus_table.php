<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Bảng Menus (Vị trí hiển thị: Top, Footer...)
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // VD: Menu Chính
            $table->string('location')->unique(); // VD: top_nav, footer_1
            $table->timestamps();
        });

        // 2. Bảng Menu Items (Các mục con)
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            
            // Liên kết cha
            $table->foreignId('menu_id')->constrained()->onDelete('cascade');
            
            // Hỗ trợ đa cấp (Cha - Con)
            $table->foreignId('parent_id')->nullable()->constrained('menu_items')->onDelete('cascade');
            
            // Thông tin hiển thị
            $table->string('title'); 
            $table->string('target')->default('_self'); // _blank, _self
            $table->integer('order')->default(0); // Để sắp xếp kéo thả

            // --- QUAN TRỌNG: Link ---
            $table->string('url')->nullable(); // Dành cho Custom Link (nhập tay)
            
            // Dành cho Link nội bộ (Polymorphic)
            // Tạo ra 2 cột: linkable_id (bigInt) và linkable_type (string)
            $table->nullableMorphs('linkable'); 
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_items');
        Schema::dropIfExists('menus');
    }
};