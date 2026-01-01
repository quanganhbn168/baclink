<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Attribute;
use App\Models\AttributeValue;

class CategoryProductSeeder extends Seeder
{
    // Ảnh nguồn nằm trong public/images/setting/
    private string $sourceImagePublic = 'images/setting/image_cate_3.jpg';

    // Số ảnh gallery / sản phẩm
    private int $galleryCount = 3;

    public function run(): void
    {
        // ===== 1) Thuộc tính & Giá trị mẫu =====
        $colorAttr = Attribute::firstOrCreate(['name' => 'Màu sắc']);
        $sizeAttr  = Attribute::firstOrCreate(['name' => 'Kích thước']);

        $colorValues = collect(['Đỏ', 'Xanh', 'Vàng'])->map(fn($c) =>
            AttributeValue::firstOrCreate(['attribute_id' => $colorAttr->id, 'value' => $c])
        );
        $sizeValues = collect(['S', 'M', 'L'])->map(fn($s) =>
            AttributeValue::firstOrCreate(['attribute_id' => $sizeAttr->id, 'value' => $s])
        );

        // ===== 2) Danh sách danh mục =====
        $categories = [
            'Dây đai PET',
            'Băng dính , Màng chít',
            'Văn phòng phẩm',
            'Khăn lau phòng sạch',
            'Khẩu trang',
            'Găng tay bảo hộ',
            'Giầy ủng bảo hộ',
            'Quần áo bảo hộ',
            'Tem in các loại',
            'Mặt hàng khác...',
        ];

        foreach ($categories as $cateName) {
            // 2.1) Ảnh RIÊNG cho danh mục (copy từ public/images/setting/...)
            $catSlug       = Str::slug($cateName) . '-' . Str::lower(Str::ulid());
            $categoryImage = $this->copyFromPublicToStorage(
                $this->sourceImagePublic,
                "uploads/categories/{$catSlug}",
                "cat-{$catSlug}"
            );

            $category = Category::create([
                'parent_id'  => 0,
                'name'       => $cateName,
                'slug'       => $catSlug,
                'image'      => $categoryImage,  // storage/uploads/categories/{slug}/cat-*.jpg
                'banner'     => null,
                'status'     => 1,
                'is_home'    => 0,
                'is_menu'    => 1,
                'is_footer'  => 0,
                'meta_description'   => $cateName,
                'meta_keywords'   => $cateName,
                'meta_image' => $categoryImage,
            ]);

            // ===== 3) 10 sản phẩm / danh mục =====
            for ($i = 1; $i <= 10; $i++) {
                $productName = $cateName . ' SP ' . $i;
                $prodSlug    = Str::slug($productName) . '-' . Str::lower(Str::ulid());

                // 3.1) Ảnh RIÊNG cho sản phẩm
                $productImage = $this->copyFromPublicToStorage(
                    $this->sourceImagePublic,
                    "uploads/products/{$prodSlug}",
                    "prod-{$prodSlug}"
                );

                $product = Product::create([
                    'category_id'     => $category->id,
                    'name'            => $productName,
                    'code'            => 'CODE-' . Str::upper(Str::random(6)),
                    'slug'            => $prodSlug,
                    'image'           => $productImage,  // storage/uploads/products/{slug}/prod-*.jpg
                    'banner'          => null,
                    'price'           => rand(100000, 500000),
                    'price_discount'  => rand(50000, 90000),
                    'description'     => 'Mô tả ngắn cho ' . $productName,
                    'content'         => 'Nội dung chi tiết cho ' . $productName,
                    'specifications'  => 'Thông số kỹ thuật mẫu cho ' . $productName,
                    'status'          => 1,
                    'is_home'         => 1,
                    'is_featured'     => 1,
                    'is_on_sale'      => 0,
                    'meta_description'        => $productName,
                    'meta_keywords'        => $productName,
                    'meta_image'      => $productImage,
                ]);

                // 3.2) GALLERY: 3 ảnh, mỗi ảnh là 1 file riêng
                for ($g = 1; $g <= $this->galleryCount; $g++) {
                    $galleryPath = $this->copyFromPublicToStorage(
                        $this->sourceImagePublic,
                        "uploads/products/{$prodSlug}/gallery",
                        "gal-{$prodSlug}-{$g}"
                    );
                    $this->saveGalleryImage($product, $galleryPath, $g);
                }

                // 3.3) VARIANTS: 3 màu × 3 size = 9 biến thể
                foreach ($colorValues as $colorVal) {
                    foreach ($sizeValues as $sizeVal) {
                        $variant = ProductVariant::create([
                            'product_id'     => $product->id,
                            'sku'            => 'SKU-' . Str::upper(Str::random(8)),
                            'price'          => $product->price + rand(10000, 30000),
                            'compare_at_price' => $product->price,
                            'stock'       => rand(5, 20),
                            'is_default'     => 0,
                        ]);

                        $variant->attributeValues()->attach([$colorVal->id, $sizeVal->id]);
                    }
                }

                // 3.4) Mark biến thể mặc định
                $product->variants()->first()?->update(['is_default' => 1]);
            }
        }
    }

    /**
     * Copy từ public/images/... sang disk 'public' (storage/app/public/...).
     * Trả về đường dẫn để render: storage/{destPath}
     */
    private function copyFromPublicToStorage(string $srcPublicRel, string $destDir, string $basename): string
    {
        $absSrc = public_path($srcPublicRel);
        if (!file_exists($absSrc)) {
            throw new \RuntimeException("Source image not found: " . $absSrc);
        }

        $disk = Storage::disk('public');
        $disk->makeDirectory($destDir);

        $ext      = pathinfo($absSrc, PATHINFO_EXTENSION) ?: 'jpg';
        $filename = $basename . '-' . Str::lower(Str::ulid()) . '.' . $ext;
        $destRel  = rtrim($destDir, '/') . '/' . $filename;

        // Đọc file từ public và ghi sang storage/app/public
        $disk->put($destRel, file_get_contents($absSrc));

        // Đường dẫn public để render thông qua symlink public/storage
        return 'storage/' . $destRel;
    }

    /**
     * Lưu 1 item gallery cho sản phẩm, tương thích nhiều schema (path/image, position, alt).
     */
    private function saveGalleryImage(Product $product, string $publicPath, int $position = 0): void
    {
        if (method_exists($product, 'addImage')) {
            try {
                $product->addImage($publicPath, [
                    'position' => $position,
                ]);
                return;
            } catch (\Throwable $e) {
                // fallback
            }
        }

        try {
            $product->images()->create([
                'path'      => $publicPath,
                'position'  => $position,
            ]);
        } catch (\Throwable $e) {
            $product->images()->create([
                'image'     => $publicPath,
                'position'  => $position,
            ]);
        }
    }
}
