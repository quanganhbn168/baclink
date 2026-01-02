<?php
use App\Models\PostCategory;
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$categories = PostCategory::where('status', 1)
    ->where(function($query) {
        $query->whereNull('parent_id')->orWhere('parent_id', 0);
    })
    ->with(['posts' => function($query) {
        $query->where('status', 1)->latest()->take(4);
    }])
    ->get()
    ->filter(function($cat) {
        return $cat->posts->count() > 0;
    });

foreach ($categories as $cat) {
    echo "Category: " . $cat->name . " (Posts: " . $cat->posts->count() . ")\n";
}
