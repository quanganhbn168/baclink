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
        // 1. Add missing columns to images table
        Schema::table('images', function (Blueprint $table) {
            // Check if column exists before adding to avoid duplicate error if re-running partial
            if (!Schema::hasColumn('images', 'filename')) {
                $table->string('filename')->nullable()->after('name');
            }
            if (!Schema::hasColumn('images', 'ext')) {
                $table->string('ext', 10)->nullable()->after('filename');
            }
            if (!Schema::hasColumn('images', 'mime')) {
                $table->string('mime')->nullable()->after('ext');
            }
            if (!Schema::hasColumn('images', 'size')) {
                $table->unsignedBigInteger('size')->nullable()->after('mime');
            }
            if (!Schema::hasColumn('images', 'width')) {
                $table->integer('width')->nullable()->after('size');
            }
            if (!Schema::hasColumn('images', 'height')) {
                $table->integer('height')->nullable()->after('width');
            }
            if (!Schema::hasColumn('images', 'disk')) {
                $table->string('disk')->default('public')->after('image');
            }
            if (!Schema::hasColumn('images', 'dir')) {
                $table->string('dir')->nullable();
            }
            if (!Schema::hasColumn('images', 'main_path')) {
                $table->string('main_path')->nullable();
            }
             if (!Schema::hasColumn('images', 'original_path')) {
                $table->string('original_path')->nullable();
            }
            if (!Schema::hasColumn('images', 'variants')) {
                $table->json('variants')->nullable();
            }
            if (!Schema::hasColumn('images', 'custom')) {
                $table->json('custom')->nullable();
            }
            if (!Schema::hasColumn('images', 'alt')) {
                $table->string('alt')->nullable();
            }
            if (!Schema::hasColumn('images', 'title')) {
                $table->string('title')->nullable();
            }
        });

        // 2. Add description to slides table
        if (Schema::hasTable('slides')) {
             Schema::table('slides', function (Blueprint $table) {
                if (!Schema::hasColumn('slides', 'description')) {
                    $table->text('description')->nullable()->after('title');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('images', function (Blueprint $table) {
            $table->dropColumn([
                'filename', 'ext', 'mime', 'size', 'width', 'height', 
                'disk', 'dir', 'main_path', 'original_path', 'variants', 'custom', 'alt', 'title'
            ]);
        });

        if (Schema::hasTable('slides')) {
             Schema::table('slides', function (Blueprint $table) {
                $table->dropColumn('description');
            });
        }
    }
};
