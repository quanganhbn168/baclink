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
        Schema::table('dealer_profiles', function (Blueprint $table) {
            $table->string('honorific')->nullable()->comment('Danh xưng: Ông/Bà');
            $table->string('position')->nullable()->comment('Chức danh người đại diện');
            $table->string('business_sector')->nullable()->comment('Nhóm ngành sản xuất');
            $table->text('company_intro')->nullable()->comment('Giới thiệu công ty');
            $table->text('featured_products')->nullable()->comment('Sản phẩm nổi bật');
            $table->string('website')->nullable()->comment('Website công ty');
            $table->string('assistant_name')->nullable()->comment('Họ tên trợ lý/thư ký');
            $table->string('assistant_phone')->nullable()->comment('SĐT trợ lý/thư ký');
            $table->string('assistant_email')->nullable()->comment('Email trợ lý/thư ký');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dealer_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'honorific',
                'position',
                'business_sector',
                'company_intro',
                'featured_products',
                'website',
                'assistant_name',
                'assistant_phone',
                'assistant_email'
            ]);
        });
    }
};
