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
        Schema::create('crawl_products', function (Blueprint $table) {
            $table->id();

            // Nguồn & nhận dạng
            $table->string('source')->default('ekokemika.com.vn'); // domain nguồn
            $table->string('url')->unique();                       // link chi tiết sp
            $table->string('category')->nullable();                // danh mục (từ breadcrumb)

            // Dữ liệu chính
            $table->string('name');                                // tên sản phẩm
            $table->string('sku')->nullable();                     // sku nếu có
            $table->bigInteger('price')->nullable();               // lưu VND (đơn vị đồng)
            $table->string('image_url')->nullable();               // ảnh đại diện
            $table->json('detail_image_urls')->nullable();         // mảng ảnh chi tiết (bỏ video)

            // Nội dung
            $table->text('short_description')->nullable();         // mô tả ngắn nếu tách được
            $table->longText('description_html')->nullable();      // mô tả/chi tiết dạng HTML sạch
            $table->longText('raw_html')->nullable();              // HTML gốc để debug/đối chiếu

            // Trạng thái xử lý
            $table->enum('status', ['pending', 'parsed', 'reviewed', 'imported', 'error'])
                  ->default('pending');
            $table->string('error_message')->nullable();           // log lỗi parse nếu có

            // Dấu vết
            $table->timestamp('fetched_at')->nullable();           // thời điểm fetch
            $table->timestamps();

            // Index phục vụ tra cứu nhanh
            $table->index(['source', 'status']);
            $table->index('sku');
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crawl_products');
    }
};
