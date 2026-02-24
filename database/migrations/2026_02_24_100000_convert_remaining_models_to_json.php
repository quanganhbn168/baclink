<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;

return new class extends Migration
{
    /**
     * Bulk convert all remaining translatable columns.
     * Wraps existing plain text in {"vi": "..."} for Spatie HasTranslations.
     * Keeps columns as LONGTEXT (Spatie works fine with text columns storing JSON strings).
     */
    public function up(): void
    {
        $tables = [
            'intros'              => ['title', 'description', 'content'],
            'careers'             => ['name', 'description', 'requirements', 'benefits'],
            'fields'              => ['name', 'summary', 'content'],
            'field_categories'    => ['name', 'description', 'content'],
            'projects'            => ['name', 'description', 'content'],
            'project_categories'  => ['name', 'description'],
            'teams'               => ['name', 'position', 'bio'],
            'testimonials'        => ['name', 'position', 'content'],
            'branches'            => ['name', 'address'],
            'brands'              => ['name'],
        ];

        foreach ($tables as $table => $columns) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            // Wrap existing plain text data in {"vi": "..."}
            $rows = DB::table($table)->get();
            foreach ($rows as $row) {
                $updates = [];
                foreach ($columns as $col) {
                    $value = $row->$col ?? null;
                    if ($value === null || $value === '') {
                        continue;
                    }
                    // Skip if already valid JSON with locale keys
                    $decoded = json_decode($value, true);
                    if (is_array($decoded) && (isset($decoded['vi']) || isset($decoded['en']))) {
                        continue;
                    }
                    // Wrap in vi locale
                    $updates[$col] = json_encode(['vi' => $value], JSON_UNESCAPED_UNICODE);
                }
                if (!empty($updates)) {
                    DB::table($table)->where('id', $row->id)->update($updates);
                }
            }
        }

        Cache::flush();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'intros'              => ['title', 'description', 'content'],
            'careers'             => ['name', 'description', 'requirements', 'benefits'],
            'fields'              => ['name', 'summary', 'content'],
            'field_categories'    => ['name', 'description', 'content'],
            'projects'            => ['name', 'description', 'content'],
            'project_categories'  => ['name', 'description'],
            'teams'               => ['name', 'position', 'bio'],
            'testimonials'        => ['name', 'position', 'content'],
            'branches'            => ['name', 'address'],
            'brands'              => ['name'],
        ];

        foreach ($tables as $table => $columns) {
            if (!Schema::hasTable($table)) continue;

            // Extract vi value back to plain text
            $rows = DB::table($table)->get();
            foreach ($rows as $row) {
                $updates = [];
                foreach ($columns as $col) {
                    $value = $row->$col ?? null;
                    if ($value === null) continue;
                    $decoded = json_decode($value, true);
                    if (is_array($decoded) && isset($decoded['vi'])) {
                        $updates[$col] = $decoded['vi'];
                    }
                }
                if (!empty($updates)) {
                    DB::table($table)->where('id', $row->id)->update($updates);
                }
            }
        }
    }
};
