<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Traits\UploadImageTrait; // Sử dụng Trait xử lý ảnh của anh
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ProductUpdateImage extends Command
{
    // Tái sử dụng Trait của anh
    use UploadImageTrait;

    /**
     * Tên và chữ ký của câu lệnh console.
     * {--category= : (Tùy chọn) Chỉ cập nhật sản phẩm trong một danh mục cụ thể theo ID}
     */
    protected $signature = 'products:update-images {--category=}';

    /**
     * Mô tả của câu lệnh console.
     */
    protected $description = 'Cập nhật hàng loạt ảnh sản phẩm về một ảnh mặc định';

    /**
     * Thực thi câu lệnh console.
     */
    public function handle()
    {
        // 1. Xác định đường dẫn ảnh gốc
        $sourceImagePath = public_path('images/setting/product_item.png');
        
        // Kiểm tra xem ảnh gốc có tồn tại không
        if (!File::exists($sourceImagePath)) {
            $this->error('Lỗi: Không tìm thấy ảnh gốc tại public/images/setting/anh-nang-luong-mat-troi.png');
            return 1; // Kết thúc với mã lỗi
        }

        // 2. Lấy danh sách sản phẩm cần cập nhật
        $categoryId = $this->option('category');
        $query = Product::query();

        if ($categoryId) {
            $query->where('category_id', $categoryId);
            $this->info("Sẽ cập nhật ảnh cho các sản phẩm thuộc danh mục ID: {$categoryId}");
        } else {
            $this->info("Sẽ cập nhật ảnh cho TẤT CẢ sản phẩm.");
        }

        $products = $query->get();
        $productCount = $products->count();

        if ($productCount === 0) {
            $this->info('Không tìm thấy sản phẩm nào để cập nhật.');
            return 0;
        }

        // 3. Tạo thanh tiến trình (progress bar)
        $bar = $this->output->createProgressBar($productCount);
        $bar->start();

        // 4. Lặp qua từng sản phẩm để xử lý
        foreach ($products as $product) {
            // A. Xóa ảnh cũ (dùng hàm deleteImage từ Trait của anh)
            if ($product->image) {
                $this->deleteImage($product->image);
            }

            // B. Tạo tên file mới, duy nhất cho mỗi sản phẩm
            $newFileName = uniqid() . '-anh-nang-luong-mat-troi.png';
            $destinationPath = 'products/' . $newFileName; // Lưu trong storage/app/public/products

            // C. Sao chép ảnh gốc vào storage với tên mới
            Storage::disk('public')->put($destinationPath, file_get_contents($sourceImagePath));

            // D. Cập nhật database với đường dẫn mới
            $product->image = 'storage/' . $destinationPath;
            $product->save();

            // Cập nhật thanh tiến trình
            $bar->advance();
        }

        $bar->finish();
        $this->info("\nHoàn tất! Đã cập nhật ảnh cho {$productCount} sản phẩm thành công.");
        return 0; // Kết thúc thành công
    }
}