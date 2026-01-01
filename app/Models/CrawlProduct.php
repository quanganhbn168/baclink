<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrawlProduct extends Model
{
    protected $fillable = [
        'source', 'url', 'category',
        'name', 'sku', 'price',
        'image_url', 'detail_image_urls',
        'short_description', 'description_html', 'raw_html',
        'status', 'error_message', 'fetched_at',
    ];

    protected $casts = [
        'detail_image_urls' => 'array',
        'fetched_at'        => 'datetime',
        'price'             => 'integer', // đồng
    ];
}
