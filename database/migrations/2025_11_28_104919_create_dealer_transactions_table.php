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
        Schema::create('dealer_transactions', function (Blueprint $table) {
            $table->id();
            // Liên kết với bảng users
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Loại giao dịch: 'deposit' (nạp tiền), 'payment' (thanh toán đơn hàng), 'refund' (hoàn tiền)...
            $table->string('type')->default('deposit'); 
            
            // Số tiền giao dịch
            $table->decimal('amount', 15, 2);
            
            // Số dư SAU KHI giao dịch (Cực kỳ quan trọng để đối soát lịch sử)
            $table->decimal('balance_after', 15, 2);
            
            // Ghi chú (Ví dụ: "Nạp tiền qua VCB", "Thanh toán đơn hàng #123")
            $table->string('note')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dealer_transactions');
    }
};
