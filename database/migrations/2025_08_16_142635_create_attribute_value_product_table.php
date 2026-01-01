<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('attribute_value_product', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('attribute_value_id')->constrained()->onDelete('cascade');
            $table->primary(['product_id', 'attribute_value_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('attribute_value_product');
    }
};