<?php

use App\Models\Post;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$posts = Post::latest()->take(5)->get();

foreach ($posts as $post) {
    echo "--------------------------------------------------\n";
    echo "ID: " . $post->id . "\n";
    echo "Title: " . $post->title . "\n";
    echo "Legacy 'image' column: " . ($post->image ?? 'NULL') . "\n";
    
    $mainImage = $post->mainImage();
    if ($mainImage) {
        echo "Relation 'mainImage': FOUND\n";
        echo " - Path: " . $mainImage->original_path . "\n";
        echo " - URL: " . $mainImage->url() . "\n";
    } else {
        echo "Relation 'mainImage': NULL\n";
    }

    echo "Final getImageUrlAttribute(): " . $post->image_url . "\n";
}
