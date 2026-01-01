<?php

namespace Database\Seeders;

use App\Models\CrawlProduct;
use Illuminate\Database\Seeder;

class CrawlProductsSeeder extends Seeder
{
    public function run(): void
    {
        CrawlProduct::updateOrCreate(
            ['url' => 'https://ekokemika.com.vn/san-pham/dung-dich-khu-mui-noi-that-deomix-lavanda.html'],
            [
                'source' => 'ekokemika.com.vn',
                'category' => 'Vệ sinh nội thất',
                'name' => 'Dung dịch khử mùi nội thất Deomix Lavanda',
                'sku' => 'DEOMIX-LAVANDA', // sẽ cập nhật đúng sau khi crawl
                'price' => null,           // sẽ cập nhật đúng sau khi crawl
                'image_url' => null,
                'detail_image_urls' => [],
                'short_description' => null,
                'description_html' => null,
                'raw_html' => null,
                'status' => 'pending',
                'fetched_at' => now(),
            ]
        );
    }
}
