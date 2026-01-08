<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('post_categories', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_id')->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('post_categories', function (Blueprint $table) {
            // Reverting to previous state: not null, default 0
            // Note: This might fail if there are null values, so we update them first
            DB::table('post_categories')->whereNull('parent_id')->update(['parent_id' => 0]);
            $table->unsignedBigInteger('parent_id')->default(0)->change();
        });
    }
};
