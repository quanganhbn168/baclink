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
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('subtotal', 15, 2)->after('note')->default(0);
        
        // Số tiền được giảm
        $table->decimal('discount_amount', 15, 2)->after('subtotal')->default(0);
        
        // % chiết khấu tại thời điểm mua (lưu lại để đối soát sau này nhỡ profile thay đổi)
        $table->integer('discount_rate')->after('discount_amount')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            //
        });
    }
};
