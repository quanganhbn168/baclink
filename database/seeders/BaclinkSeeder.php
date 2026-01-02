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
use App\Models\User;
use Illuminate\Support\Str;

class BaclinkSeeder extends Seeder
{
    public function run(): void
    {
        // --- 0. Clean up removed (protect user data) ---
        // MenuItem::query()->delete(); ... REMOVED

        // 1. Settings
        $setting = Setting::firstOrCreate(['id' => 1], [
            'name' => 'Baclink',
            'email' => 'contact@baclink.test',
            'phone' => '0965625210',
            'address' => 'Bắc Ninh, Việt Nam',
            'logo' => 'images/setting/logo-t.png',
            'favicon' => 'favicon.png',
            'meta_description' => 'Baclink - Giải pháp liên kết doanh nghiệp hàng đầu.',
            'meta_keywords' => 'baclink, kết nối doanh nghiệp, Bắc Ninh',
        ]);
        // Only update phone/email if they are default values or empty (optional, but keeps user work)
        if (empty($setting->phone)) $setting->update(['phone' => '0965625210']);

        // 2. Intro
        $introData = [
            [
                'title' => 'Giới thiệu chung',
                'slug' => 'gioi-thieu-chung',
                'description' => 'Giới thiệu chung về Hội Công nghiệp chủ lực Bắc Ninh (Baclink)',
                'content' => '<p>Hội Công nghiệp chủ lực Bắc Ninh (Baclink) là tổ chức xã hội - nghề nghiệp tự nguyện...</p>',
            ],
            [
                'title' => 'Cơ cấu tổ chức',
                'slug' => 'co-cau-to-chuc',
                'description' => 'Cơ cấu tổ chức của Hội',
                'content' => '<p>Sơ đồ cơ cấu tổ chức...</p>',
            ],
            [
                'title' => 'Điều lệ Hội',
                'slug' => 'dieu-le-hoi',
                'description' => 'Điều lệ hoạt động của Hội',
                'content' => '<p>Các quy định và điều lệ...</p>',
            ],
            [
                'title' => 'Ban chấp hành',
                'slug' => 'ban-chap-hanh',
                'description' => 'Danh sách Ban chấp hành',
                'content' => '<p>Danh sách các thành viên Ban chấp hành...</p>',
            ]
        ];

        foreach ($introData as $data) {
            Intro::updateOrCreate(['slug' => $data['slug']], array_merge($data, ['status' => 1]));
        }

        // 3. Slides (Theme Style)
        $slides = [
            ['title' => 'Hợp tác cùng phát triển', 'link' => '#', 'type' => 1],
            ['title' => 'Kết nối doanh nghiệp toàn cầu', 'link' => '#', 'type' => 1],
        ];
        foreach ($slides as $s) {
            $slide = Slide::updateOrCreate(
                ['title' => $s['title']], 
                array_merge($s, ['status' => 1, 'is_home' => 1])
            );
            if (!$slide->images()->where('role', 'main')->exists()) {
                $slide->images()->create([
                    'image' => 'https://placehold.co/1920x800?text=Baclink+Slide',
                    'role' => 'main',
                ]);
            }
        }

        // 4. Categories & Products
        $cat = Category::updateOrCreate(['slug' => 'san-pham-chu-luc'], [
            'name' => 'Sản phẩm chủ lực',
            'status' => 1,
            'is_home' => 1,
            'image' => 'images/setting/no-image.png'
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
                    'is_home' => 1,
                    'description' => "Mô tả cho sản phẩm công nghiệp $i",
                ]
            );
            $product->setMainImage([
                'image' => "https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?auto=format&fit=crop&q=80&w=600&h=600&text=Product+$i",
            ]);
        }

        // 5. Brands (Đối tác)
        for ($i = 1; $i <= 10; $i++) {
            $brand = Brand::updateOrCreate(
                ['slug' => "doi-tac-$i"],
                [
                    'name' => "Đối tác tiêu biểu $i",
                    'status' => 1,
                ]
            );
            $brand->setMainImage([
                'image' => "https://images.unsplash.com/photo-1560179707-f14e90ef3623?auto=format&fit=crop&q=80&w=200&h=100&text=Partner+$i",
            ]);
        }

        // 6. Posts (Hoạt động của Baclink)
        // Clean up old news data to avoid duplicates/clutter
        Post::query()->delete();
        PostCategory::query()->delete();

        $categories = [
            'Hoạt động Ban Hội viên',
            'Hoạt động Truyền thông',
            'Hoạt động Xúc tiến thương mại',
            'Hoạt động Ban Công nghệ sản xuất',
            'Sự kiện'
        ];
        foreach ($categories as $catName) {
            $postCat = PostCategory::create([
                'name' => $catName,
                'slug' => Str::slug($catName),
                'status' => 1,
                'is_home' => 1,
                'image' => 'images/setting/no-image.png'
            ]);
            
            for ($i = 1; $i <= 5; $i++) {
                $pSlug = Str::slug($catName) . "-$i";
                $post = Post::updateOrCreate(
                    ['slug' => $pSlug],
                    [
                        'title' => "$catName số $i: Gắn kết doanh nghiệp Bắc Ninh",
                        'post_category_id' => $postCat->id,
                        'image' => 'images/setting/no-image.png',
                        'description' => "Tóm tắt của bài viết $catName số $i. Đây là nội dung mô tả ngắn gọn giúp người đọc nắm bắt thông tin nhanh chóng về các hoạt động của Baclink tại Bắc Ninh.",
                        'content' => "Nội dung chi tiết của bài viết $catName số $i...",
                        'status' => 1,
                        'is_home' => 1,
                    ]
                );
                if (!$post->images()->where('role', 'main')->exists()) {
                    $post->images()->create([
                        'image' => "https://images.unsplash.com/photo-1504711434969-e33886168f5c?auto=format&fit=crop&q=80&w=800&h=600&text=News+$i",
                        'role' => 'main',
                    ]);
                }
            }
        }

        // 8. Sample Users (Hội viên)
        $companyNames = [
            'Công ty Cổ phần Tập đoàn Nhựa Bình Thuận',
            'Công ty cổ phần chế tạo Điện cơ Bắc Ninh',
            'Công ty cổ phần TOMECO An Khang',
            'Công ty cổ phần cơ điện Trần Phú',
            'Công ty cổ phần LUMI Việt Nam',
            'Công ty TNHH Giải pháp Công nghệ Baclink',
            'Tập đoàn Công nghiệp Việt Nam',
            'Hợp tác xã Nông nghiệp Xanh',
            'Công ty Xây dựng và Phát triển Hạ tầng',
            'Công ty May mặc Xuất khẩu'
        ];
        
        for ($i = 1; $i <= 10; $i++) {
            $user = User::updateOrCreate(
                ['email' => "member$i@baclink.test"],
                [
                    'name' => "Hội viên $i",
                    'phone' => '0965625' . str_pad($i, 3, '0', STR_PAD_LEFT),
                    'password' => \Illuminate\Support\Facades\Hash::make('password'),
                    'avatar' => "https://i.pravatar.cc/150?u=member$i@baclink.test",
                ]
            );
            
            \App\Models\DealerProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'company_name' => $companyNames[$i-1] ?? "Công ty Thành viên $i",
                    'representative_name' => $user->name,
                    'address' => 'Bắc Ninh, Việt Nam',
                ]
            );
        }

        // 7. Menus
        $headerMenu = Menu::updateOrCreate(['location' => 'top_nav'], ['name' => 'Header Menu']);
        // Clean up old items to ensure exact match
        $headerMenu->items()->delete();

        // Header items (update simple order/slug matching)
        $menuItems = [
            ['title' => 'TRANG CHỦ', 'url' => '/', 'order' => 1],
            ['title' => 'GIỚI THIỆU', 'order' => 2, 'linkable_type' => Intro::class, 'linkable_id' => 1],
            ['title' => 'HỘI VIÊN', 'url' => route('frontend.members.index'), 'order' => 3],
            ['title' => 'TIN TỨC', 'url' => route('frontend.posts.index'), 'order' => 4],
            ['title' => 'LIÊN HỆ', 'url' => route('contact.show'), 'order' => 5],
        ];
        foreach ($menuItems as $mi) {
            MenuItem::updateOrCreate(
                ['menu_id' => $headerMenu->id, 'title' => $mi['title']],
                $mi
            );
        }

        // 7.2 Footer Menu
        $footerMenu = Menu::updateOrCreate(['location' => 'footer_nav'], ['name' => 'Footer Menu']);
        $footerMenu->items()->delete();

        $footerItems = [
            ['title' => 'Giới thiệu', 'linkable_type' => Intro::class, 'linkable_id' => 1, 'order' => 1],
            ['title' => 'Hội viên tiêu biểu', 'url' => '#', 'order' => 2],
            ['title' => 'Tin tức', 'url' => route('frontend.posts.index'), 'order' => 3],
            ['title' => 'Liên hệ', 'url' => route('contact.show'), 'order' => 4],
        ];
        foreach ($footerItems as $fi) {
            MenuItem::updateOrCreate(
                ['menu_id' => $footerMenu->id, 'title' => $fi['title']],
                $fi
            );
        }
        
        // Clear Menu Cache
        \Illuminate\Support\Facades\Cache::forget('header_menu_structure');
        \Illuminate\Support\Facades\Cache::forget('footer_menu_structure');
    }
}
