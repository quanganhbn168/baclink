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
        // 1. Make image nullable
        Schema::table('posts', function (Blueprint $table) {
            $table->string('image')->nullable()->change();
        });

        // 2. Clear existing junk/seeded data so "random images" don't show up.
        // We only clear if it looks like a URL or isn't a simple local path, 
        // to render the 'no-image.png' fallback active.
        // Or simpler: just clear all, forcing user to re-upload real content.
        DB::table('posts')->update(['image' => null]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            // We cannot easily restore lost data, but we revert the schema.
            // Ensure no nulls before reverting to NOT NULL
            DB::table('posts')->whereNull('image')->update(['image' => '']);
            $table->string('image')->nullable(false)->change();
        });
    }
};
