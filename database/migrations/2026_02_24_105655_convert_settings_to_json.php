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
        // First convert columns to longtext
        Schema::table('settings', function (Blueprint $table) {
            $table->longText('name')->nullable()->change();
            $table->longText('address')->nullable()->change();
            $table->longText('meta_description')->nullable()->change();
            $table->longText('meta_keywords')->nullable()->change();
        });

        // Loop over settings and update them to JSON
        $settings = DB::table('settings')->get();
        foreach ($settings as $setting) {
            $updateData = [];
            foreach (['name', 'address', 'meta_description', 'meta_keywords'] as $col) {
                if (!empty($setting->$col) && !is_array(json_decode($setting->$col, true))) {
                    $updateData[$col] = json_encode(['vi' => $setting->$col], JSON_UNESCAPED_UNICODE);
                }
            }
            if (!empty($updateData)) {
                DB::table('settings')->where('id', $setting->id)->update($updateData);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Phục hồi
        $settings = DB::table('settings')->get();
        foreach ($settings as $setting) {
            $updateData = [];
            foreach (['name', 'address', 'meta_description', 'meta_keywords'] as $col) {
                if (!empty($setting->$col)) {
                    $decoded = json_decode($setting->$col, true);
                    if (is_array($decoded) && isset($decoded['vi'])) {
                        $updateData[$col] = $decoded['vi'];
                    }
                }
            }
            if (!empty($updateData)) {
                DB::table('settings')->where('id', $setting->id)->update($updateData);
            }
        }
        
        Schema::table('settings', function (Blueprint $table) {
            $table->string('name')->nullable()->change();
            $table->string('address')->nullable()->change();
            $table->string('meta_description', 500)->nullable()->change();
            $table->string('meta_keywords')->nullable()->change();
        });
    }
};
