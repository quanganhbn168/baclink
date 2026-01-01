<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;
use App\Models\Slide;
use App\Models\Intro;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Support\Str;

class BaclinkSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Settings
        Setting::updateOrCreate(['id' => 1], [
            'name' => 'Baclink',
            'email' => 'contact@baclink.test',
            'phone' => '0965625210',
            'address' => 'Bắc Ninh, Việt Nam',
            'logo' => 'images/setting/logo.png',
            'favicon' => 'images/setting/favicon.ico',
            'meta_description' => 'Baclink - Giải pháp liên kết doanh nghiệp hàng đầu.',
            'meta_keywords' => 'baclink, kết nối doanh nghiệp, Bắc Ninh',
        ]);

        // 2. Intro
        Intro::updateOrCreate(['id' => 1], [
            'title' => 'Chào mừng đến với Baclink',
            'slug' => 'gioi-thieu',
            'description' => 'Chúng tôi cung cấp giải pháp liên kết doanh nghiệp hiệu quả, giúp nâng tầm vị thế và tối đa hóa lợi nhuận cho đối tác.',
            'status' => 1,
        ]);

        // 3. Slides (Theme Style)
        $slides = [
            ['title' => 'Hợp tác cùng phát triển', 'link' => '#', 'type' => 1],
            ['title' => 'Kết nối doanh nghiệp toàn cầu', 'link' => '#', 'type' => 1],
        ];
        foreach ($slides as $s) {
            $slide = Slide::updateOrCreate(
                ['title' => $s['title']], 
                array_merge($s, [
                    'status' => 1, 
                    'is_home' => 1,
                    // 'image' field update logic handled below or ignored if complex
                ])
            );
            if (!$slide->images()->where('role', 'main')->exists()) {
                $slide->images()->create([
                    'image' => 'https://placehold.co/1920x800?text=Baclink+Slide',
                    'role' => 'main',
                ]);
            }
        }

        // 4. Categories & Products
        $cat = Category::firstOrCreate(['slug' => 'san-pham-chu-luc'], [
            'name' => 'Sản phẩm chủ lực',
            'status' => 1,
            'is_home' => 1,
            'image' => 'https://placehold.co/600x400?text=Category'
        ]);

        for ($i = 1; $i <= 8; $i++) {
            $product = Product::updateOrCreate(
                ['code' => "PROD-$i"],
                [
                    'name' => "Sản phẩm công nghiệp $i",
                    'slug' => "san-pham-cong-nghiep-$i",
                    'product_type' => 'physical',
                    'category_id' => $cat->id,
                    'price' => rand(1000000, 5000000),
                    'status' => 1,
                    'is_featured' => 1,
                    'description' => "Mô tả cho sản phẩm công nghiệp $i",
                ]
            );
            if (!$product->images()->where('role', 'main')->exists()) {
                $product->images()->create([
                    'image' => "https://placehold.co/600x600?text=Product+$i",
                    'role' => 'main',
                ]);
            }
        }

        // 5. Brands (Members)
        for ($i = 1; $i <= 6; $i++) {
            $brand = Brand::updateOrCreate(
                ['slug' => "hoi-vien-$i"],
                [
                    'name' => "Hội viên $i",
                    'status' => 1,
                ]
            );
            if (!$brand->images()->where('role', 'main')->exists()) {
                $brand->images()->create([
                    'image' => "https://placehold.co/200x100?text=Brand+$i",
                    'role' => 'main',
                ]);
            }
        }

        // 6. Posts
        $postCat = PostCategory::firstOrCreate(['slug' => 'tin-tuc-su-kien'], [
            'name' => 'Tin tức & Sự kiện', 
            'status' => 1,
            'image' => 'https://placehold.co/600x400?text=Post+Category'
        ]);
        for ($i = 1; $i <= 4; $i++) {
            $post = Post::updateOrCreate(
                ['slug' => "tin-tuc-su-kien-baclink-$i"],
                [
                    'title' => "Tin tức sự kiện Baclink $i",
                    'post_category_id' => $postCat->id,
                    'description' => "Tóm tắt tin tức $i...",
                    'content' => "Nội dung chi tiết tin tức $i...",
                    'status' => 1,
                    'is_home' => 1,
                ]
            );
            if (!$post->images()->where('role', 'main')->exists()) {
                $post->images()->create([
                    'image' => "https://placehold.co/800x600?text=News+$i",
                    'role' => 'main',
                ]);
            }
        }

        // 7. Menus
        // 7.1 Header Menu
        $headerMenu = Menu::updateOrCreate(['location' => 'top_nav'], ['name' => 'Header Menu']);
        // Xóa cũ để seed mới sạch sẽ
        MenuItem::where('menu_id', $headerMenu->id)->delete();

        // Trang chủ
        MenuItem::create([
            'menu_id' => $headerMenu->id,
            'title' => 'TRANG CHỦ',
            'url' => '/',
            'order' => 1
        ]);

        // Giới thiệu
        MenuItem::create([
            'menu_id' => $headerMenu->id,
            'title' => 'GIỚI THIỆU',
            'order' => 2,
            'linkable_type' => Intro::class,
            'linkable_id' => 1 
        ]);

        // Sản phẩm (Dynamic)
        $prodItem = MenuItem::create([
            'menu_id' => $headerMenu->id,
            'title' => 'SẢN PHẨM',
            'url' => route('products.index'), // Route danh sách sản phẩm
            'order' => 3
        ]);
        // Giả sử muốn nhúng Category con vào menu Sản phẩm (tùy logic MenuBuilderService)
        // Ở đây mình cứ để link tĩnh hoặc trỏ về Cate cha

        // Hội viên
        MenuItem::create([
            'menu_id' => $headerMenu->id,
            'title' => 'HỘI VIÊN',
            'url' => '#',
            'order' => 4
        ]);

        // Tin tức
        MenuItem::create([
            'menu_id' => $headerMenu->id,
            'title' => 'TIN TỨC',
            'linkable_type' => PostCategory::class,
            'linkable_id' => $postCat->id,
            'order' => 5
        ]);

        // Liên hệ
        MenuItem::create([
            'menu_id' => $headerMenu->id,
            'title' => 'LIÊN HỆ',
            'url' => route('contact.show'), 
            'order' => 6
        ]);

        // 7.2 Footer Menu
        $footerMenu = Menu::updateOrCreate(['location' => 'footer_nav'], ['name' => 'Footer Menu']);
        MenuItem::where('menu_id', $footerMenu->id)->delete();

        MenuItem::create(['menu_id' => $footerMenu->id, 'title' => 'Giới thiệu', 'linkable_type' => Intro::class, 'linkable_id' => 1, 'order' => 1]);
        MenuItem::create(['menu_id' => $footerMenu->id, 'title' => 'Hội viên tiêu biểu', 'url' => '#', 'order' => 2]);
        MenuItem::create(['menu_id' => $footerMenu->id, 'title' => 'Tin tức & Sự kiện', 'linkable_type' => PostCategory::class, 'linkable_id' => $postCat->id, 'order' => 3]);
        MenuItem::create(['menu_id' => $footerMenu->id, 'title' => 'Liên hệ', 'url' => route('contact.show'), 'order' => 4]);
    }
}
