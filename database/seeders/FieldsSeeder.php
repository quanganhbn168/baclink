<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FieldCategory;
use App\Models\Field;
use Illuminate\Support\Str;

class FieldsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create or Get Category
        $category = FieldCategory::firstOrCreate(
            ['slug' => 'linh-vuc-hoat-dong'],
            [
                'name' => 'Lĩnh vực hoạt động',
                'description' => 'Các lĩnh vực hoạt động chính của hiệp hội',
                'status' => 1,
                'parent_id' => null
            ]
        );

        // 2. Define Fields
        $fields = [
            [
                'name' => 'HỖ TRỢ CHÍNH SÁCH',
                'image' => 'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&q=80&w=600',
                'summary' => 'Tư vấn, hỗ trợ doanh nghiệp tiếp cận và thụ hưởng các chính sách ưu đãi của nhà nước.',
                'content' => 'Nội dung chi tiết về hỗ trợ chính sách...',
            ],
            [
                'name' => 'THAM GIA CHƯƠNG TRÌNH XÚC TIẾN THƯƠNG MẠI',
                'image' => 'https://images.unsplash.com/photo-1542744173-8e7e53415bb0?auto=format&fit=crop&q=80&w=600',
                'summary' => 'Tổ chức các đoàn doanh nghiệp tham gia hội chợ, triển lãm trong và ngoài nước.',
                'content' => 'Nội dung chi tiết về xúc tiến thương mại...',
            ],
            [
                'name' => 'GIAO THƯƠNG NỘI - NGOẠI KHỐI',
                'image' => 'https://images.unsplash.com/photo-1521791136064-7986c2920216?auto=format&fit=crop&q=80&w=600',
                'summary' => 'Kết nối giao thương, mở rộng thị trường tiêu thụ sản phẩm cho hội viên.',
                'content' => 'Nội dung chi tiết về giao thương...',
            ],
            [
                'name' => 'ĐÀO TẠO NÂNG CAO NĂNG LỰC DOANH NGHIỆP',
                'image' => 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?auto=format&fit=crop&q=80&w=600',
                'summary' => 'Tổ chức các khóa đào tạo quản trị, kỹ năng mềm, chuyển đổi số cho lãnh đạo và nhân viên.',
                'content' => 'Nội dung chi tiết về đào tạo...',
            ],
            [
                'name' => 'HỢP TÁC QUỐC TẾ',
                'image' => 'https://images.unsplash.com/photo-1556761175-5973dc0f32e7?auto=format&fit=crop&q=80&w=600',
                'summary' => 'Xây dựng mối quan hệ hợp tác với các tổ chức quốc tế, hiệp hội doanh nghiệp nước ngoài.',
                'content' => 'Nội dung chi tiết về hợp tác quốc tế...',
            ],
        ];

        // 3. Insert Fields
        foreach ($fields as $data) {
            $field = Field::firstOrCreate(
                ['slug' => Str::slug($data['name'])], // Check by slug to avoid duplicates
                [
                    'field_category_id' => $category->id,
                    'name' => $data['name'],
                    'image' => $data['image'],
                    'summary' => $data['summary'],
                    'content' => $data['content'],
                    'status' => 1,
                    'is_featured' => 1,
                ]
            );

            // Ensure Slug entry exists for SlugController resolution
            if (!$field->slug()->exists()) {
                $field->slug()->create(['slug' => $field->slug]);
            }
        }
    }
}
