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
        Schema::create('dealer_profiles', function (Blueprint $table) {
            $table->id();
            
            // Liên kết 1-1 với bảng users. 
            // unique() đảm bảo 1 user chỉ có tối đa 1 hồ sơ đại lý.
            // onDelete('cascade') để khi xóa user thì profile này tự bay màu theo.
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->unique();
            
            // --- Thông tin định danh Doanh nghiệp ---
            $table->string('company_name')->nullable();       // Tên công ty / Cửa hàng
            $table->string('representative_name')->nullable(); // Tên người đại diện pháp luật (nếu cần)
            $table->string('tax_id')->nullable();             // Mã số thuế
            $table->string('phone')->nullable();              // SĐT hotline/kho (có thể khác SĐT đăng nhập)
            $table->string('address')->nullable();            // Địa chỉ kinh doanh
            
            // --- Thông tin Mạng xã hội (Phục vụ tính năng chat nhanh) ---
            $table->string('facebook_id')->nullable();        // Facebook ID/Username
            $table->string('zalo_phone')->nullable();         // SĐT Zalo (nếu khác SĐT chính)
            
            // --- Thông tin Tài chính & Cấp bậc ---
            $table->decimal('wallet_balance', 15, 2)->default(0); // Số dư ví hiện tại
            $table->decimal('total_spent', 15, 2)->default(0);    // Tổng tiền đã mua (dùng để xét hạng VIP)
            $table->integer('discount_rate')->default(0);         // % Chiết khấu riêng (VD: 10, 15, 20)
            
            // --- Quản trị ---
            $table->text('admin_note')->nullable();           // Ghi chú nội bộ của Admin (khách ko thấy)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dealer_profiles');
    }
};
