<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $columns = ['title', 'description'];
        $items = DB::table('slides')->get();

        foreach ($items as $item) {
            $updates = [];
            foreach ($columns as $col) {
                $val = $item->$col;
                if ($val && !$this->isJson($val)) {
                    $updates[$col] = json_encode(['vi' => $val], JSON_UNESCAPED_UNICODE);
                }
            }
            if (!empty($updates)) {
                DB::table('slides')->where('id', $item->id)->update($updates);
            }
        }
    }

    public function down(): void
    {
        $columns = ['title', 'description'];
        $items = DB::table('slides')->get();

        foreach ($items as $item) {
            $updates = [];
            foreach ($columns as $col) {
                $decoded = json_decode($item->$col, true);
                if (is_array($decoded) && isset($decoded['vi'])) {
                    $updates[$col] = $decoded['vi'];
                }
            }
            if (!empty($updates)) {
                DB::table('slides')->where('id', $item->id)->update($updates);
            }
        }
    }

    private function isJson($string): bool
    {
        if (!is_string($string)) return false;
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE && (str_starts_with($string, '{') || str_starts_with($string, '['));
    }
};
