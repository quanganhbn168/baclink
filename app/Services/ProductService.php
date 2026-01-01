<?php
namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant; // <--- Thêm dòng này
use App\Models\Category;
use App\Contracts\MediaServiceContract;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ProductService
{
    private const MAIN_IMAGE_CONFIG = [
        'main' => ['width' => 1024],
        'variants' => ['thumbnail' => ['width' => 150, 'height' => 150, 'fit' => true]],
        'quality' => 85,
        'format' => 'webp',
    ];

    private const GALLERY_IMAGE_CONFIG = [
        'main' => ['width' => 1024],
        'variants' => ['thumbnail' => ['width' => 150, 'height' => 150, 'fit' => true]],
        'quality' => 85,
        'format' => 'webp',
    ];

    public function __construct(
        protected MediaServiceContract $mediaService
    ) {}

    public function getAll()
    {
        return Product::with('category')->latest()->paginate(20);
    }

    public function list($request): array
    {
        // ... (Giữ nguyên logic list cũ của bạn) ...
        $perPage = (int) $request->integer('per_page', 20);
        $products = Product::with('category')
            ->when($request->filled('keyword'), function ($q) use ($request) {
                $kw = trim($request->get('keyword'));
                $q->where(function ($qq) use ($kw) {
                    $qq->where('name', 'LIKE', "%{$kw}%")
                       ->orWhere('slug', 'LIKE', "%{$kw}%")
                       ->orWhere('code', 'LIKE', "%{$kw}%");
                });
            })
            ->when($request->filled('category_id'), fn($q) => $q->where('category_id', $request->get('category_id')))
            ->latest('id')
            ->paginate($perPage);

        $filterCategories = Category::orderBy('name')->pluck('name', 'id')->toArray();
        return [$products, $filterCategories];
    }

    /**
     * Tạo sản phẩm mới
     */
    public function create(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            // 1. Tách dữ liệu
            $mediaData = Arr::only($data, ['image_original_path', 'gallery_original_paths']);
            $variantsData = $data['variants'] ?? [];
            
            // Loại bỏ các trường không thuộc bảng products
            $productData = Arr::except($data, ['image_original_path', 'gallery_original_paths', 'variants', 'save', 'save_new']);

            // 2. Tạo Slug nếu chưa có
            if (empty($productData['slug'])) {
                $productData['slug'] = Str::slug($productData['name']);
            }

            // 3. Tạo Product
            $product = Product::create($productData);

            // 4. Xử lý Ảnh đại diện
            $this->mediaService->updateMedia(
                $product,
                $mediaData['image_original_path'] ?? null,
                'products',
                self::MAIN_IMAGE_CONFIG,
                fn($img) => $product->setMainImage($img),
                null,
                'Ảnh đại diện'
            );

            // 5. Xử lý Gallery
            $this->updateGallery($product, $mediaData['gallery_original_paths'] ?? null);

            // 6. Xử lý Biến thể (Variants)
            $this->syncVariants($product, $variantsData);

            return $product;
        });
    }

    /**
     * Cập nhật sản phẩm
     */
    public function update(Product $product, array $data): Product
    {
        return DB::transaction(function () use ($product, $data) {
            // 1. Tách dữ liệu
            $mediaData = Arr::only($data, ['image_original_path', 'gallery_original_paths']);
            $variantsData = $data['variants'] ?? [];
            
            $productData = Arr::except($data, ['image_original_path', 'gallery_original_paths', 'variants', 'save']);

            // 2. Cập nhật Product
            $product->update($productData);

            // 3. Cập nhật Ảnh
            $this->mediaService->updateMedia(
                $product,
                $mediaData['image_original_path'] ?? null,
                'products',
                self::MAIN_IMAGE_CONFIG,
                fn($img) => $product->setMainImage($img),
                fn() => $product->mainImage(),
                'Ảnh đại diện'
            );

            // 4. Cập nhật Gallery
            $this->updateGallery($product, $mediaData['gallery_original_paths'] ?? null);

            // 5. Cập nhật Biến thể
            $this->syncVariants($product, $variantsData);

            return $product;
        });
    }

    protected function syncVariants(Product $product, array $variantsData): void
    {
        if (!$product->has_variants) {
            $product->variants()->delete();
            return;
        }

        // Lấy danh sách ID các biến thể được gửi lên để so sánh xóa
        $submittedIds = [];

        foreach ($variantsData as $item) {
            // 1. Check cờ xóa
            if (isset($item['delete_flag']) && $item['delete_flag'] == '1') {
                if (!empty($item['id'])) {
                    // Xóa variant (Cascade sẽ tự xóa bên bảng trung gian)
                    ProductVariant::destroy($item['id']);
                }
                continue;
            }

            // 2. Tạo hoặc Update Variant
            $variant = ProductVariant::updateOrCreate(
                ['id' => $item['id'] ?? null],
                [
                    'product_id'       => $product->id,
                    'variant_name'     => $item['variant_name'],
                    'sku'              => $item['sku'],
                    'price'            => $item['price'],
                    'compare_at_price' => $item['compare_at_price'] ?? null,
                    'stock'            => $item['stock'] ?? 0,
                ]
            );

            if ($variant) {
                $submittedIds[] = $variant->id;

                // ====================================================
                // 3. QUAN TRỌNG: LƯU RELATION VÀO BẢNG TRUNG GIAN
                // ====================================================
                // Lấy chuỗi "1,5" từ input hidden
                if (!empty($item['attribute_value_ids'])) {
                    // Chuyển "1,5" thành mảng [1, 5]
                    $attrValueIds = explode(',', $item['attribute_value_ids']);
                    
                    // Lưu vào bảng pivot
                    $variant->attributeValues()->sync($attrValueIds);
                }
            }
        }
        
        // (Tùy chọn) Xóa các biến thể cũ trong DB mà không nằm trong danh sách gửi lên
        // $product->variants()->whereNotIn('id', $submittedIds)->delete();
    }

    private function updateGallery(Product $product, ?string $rawJson): void
    {
        // ... (Giữ nguyên logic cũ của bạn) ...
         if (!$rawJson) return;
        $paths = json_decode($rawJson, true) ?? [];
        foreach ($product->gallery as $img) {
            $this->mediaService->deleteProcessedImages($img);
            $img->delete();
        }
        foreach ($paths as $index => $path) {
            $imageData = $this->mediaService->processAndPrepareData($path, 'products/gallery', self::GALLERY_IMAGE_CONFIG);
            if ($imageData) {
                $product->addGalleryImage($imageData, $index);
            }
        }
    }
    
     public function delete(Product $product): void
    {
        // ... (Giữ nguyên logic cũ) ...
        foreach ($product->images as $img) {
            $this->mediaService->deleteProcessedImages($img);
        }
        $product->images()->delete();
        $product->variants()->delete(); // Xóa thêm variants
        $product->delete();
    }
}