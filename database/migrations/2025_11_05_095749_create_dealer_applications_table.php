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
        Schema::create('dealer_applications', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone', 30);
            $table->string('email')->nullable();
            $table->string('company')->nullable();
            $table->string('address')->nullable();
            $table->string('source', 50)->nullable(); 
            $table->text('message')->nullable();
            $table->string('ip', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->tinyInteger('status')->default(0); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dealer_applications');
    }
};
