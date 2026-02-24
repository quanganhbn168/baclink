<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Columns to convert per table.
     * Existing data will be wrapped: "Xin chào" → {"vi":"Xin chào"}
     */
    private array $tables = [
        'posts'               => ['title', 'description', 'content'],
        'products'            => ['name', 'description', 'content', 'meta_title', 'meta_description'],
        'categories'          => ['name', 'meta_description'],
        'services'            => ['name', 'description', 'content'],
        'service_categories'  => ['name', 'description', 'content'],
        'post_categories'     => ['name'],
        'pages'               => ['title', 'content'],
    ];

    public function up(): void
    {
        $defaultLocale = config('translatable.default', 'vi');

        foreach ($this->tables as $table => $columns) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            foreach ($columns as $column) {
                if (!Schema::hasColumn($table, $column)) {
                    continue;
                }

                // Step 1: Convert existing data to JSON format
                $rows = DB::table($table)->whereNotNull($column)->get(['id', $column]);
                foreach ($rows as $row) {
                    $value = $row->$column;

                    // Skip if already valid JSON with locale keys
                    $decoded = json_decode($value, true);
                    if (is_array($decoded) && isset($decoded[$defaultLocale])) {
                        continue;
                    }

                    // Wrap existing value in locale JSON
                    $jsonValue = json_encode([$defaultLocale => $value], JSON_UNESCAPED_UNICODE);

                    DB::table($table)
                        ->where('id', $row->id)
                        ->update([$column => $jsonValue]);
                }

                // Step 2: Change column type to JSON (longText to support large content)
                Schema::table($table, function (Blueprint $blueprint) use ($column) {
                    $blueprint->longText($column)->nullable()->change();
                });
            }
        }
    }

    public function down(): void
    {
        $defaultLocale = config('translatable.default', 'vi');

        foreach ($this->tables as $table => $columns) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            foreach ($columns as $column) {
                if (!Schema::hasColumn($table, $column)) {
                    continue;
                }

                // Extract default locale value back to plain text
                $rows = DB::table($table)->whereNotNull($column)->get(['id', $column]);
                foreach ($rows as $row) {
                    $decoded = json_decode($row->$column, true);
                    if (is_array($decoded) && isset($decoded[$defaultLocale])) {
                        DB::table($table)
                            ->where('id', $row->id)
                            ->update([$column => $decoded[$defaultLocale]]);
                    }
                }
            }
        }
    }
};
