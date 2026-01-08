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
            $table->string('image')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('post_categories', function (Blueprint $table) {
            // Reverting to previous state: not null. 
            // Warning: explicit reversion might fail if nulls exist.
            // We set a default empty string for null values before reverting to ensure safety.
            DB::table('post_categories')->whereNull('image')->update(['image' => '']);
            $table->string('image')->nullable(false)->change();
        });
    }
};
