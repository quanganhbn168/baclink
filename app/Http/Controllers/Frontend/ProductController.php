<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // == Input ==
        $slug = $request->query('category_slug');
        $sort = $request->query('sort', 'new');
        $perPage = (int) $request->query('per_page', 12);

        // == Sidebar categories (root) ==
        // Phần này chỉ để hiển thị cái Menu bên trái/trên, giữ nguyên
        $categories = Category::query()
            ->where('status', 1)
            ->where('parent_id', null)
            ->withCount(['products' => fn($q) => $q->where('status', 1)])
            ->orderBy('position', 'asc')
            ->get();

        // == Current category (optional) ==
        $currentCategory = null;
        if ($slug) {
            $currentCategory = Category::where('slug', $slug)->where('status', 1)->firstOrFail();
        }

        // ==========================================================
        // SỬA ĐOẠN NÀY: JOIN VỚI BẢNG CATEGORY ĐỂ LẤY POSITION
        // ==========================================================
        $productsQ = Product::query()
            ->join('categories', 'products.category_id', '=', 'categories.id') // 1. Nối bảng
            ->select('products.*') // 2. Chỉ lấy dữ liệu của sản phẩm để tránh trùng tên cột (ví dụ id, name)
            ->where('products.status', 1) // Sửa thành products.status cho rõ ràng
            ->where('categories.status', 1); // Đảm bảo danh mục cha cũng đang active

        if ($currentCategory) {
            $productsQ->where('products.category_id', $currentCategory->id);
        }

        // ==========================================================
        // QUAN TRỌNG: LUÔN ƯU TIÊN SẮP XẾP THEO VỊ TRÍ DANH MỤC TRƯỚC
        // ==========================================================
        // Dòng này sẽ giúp các sản phẩm thuộc Danh mục có position 1 hiện lên trước,
        // sau đó đến position 2...
        $productsQ->orderBy('categories.position', 'asc');


        // == Sorting (Sắp xếp phụ bên trong từng nhóm danh mục) ==
        // Lưu ý: Phải thêm 'products.' trước tên cột để tránh lỗi "Ambiguous column"
        switch ($sort) {
            case 'old':
                $productsQ->orderBy('products.created_at', 'asc');
                break;
            case 'name_asc':
                $productsQ->orderBy('products.name', 'asc');
                break;
            case 'price_asc':
                // Sửa query raw một chút để trỏ đúng cột giá của products
                $productsQ->orderByRaw('COALESCE(NULLIF(products.price_discount,0), products.price) asc');
                break;
            case 'price_desc':
                $productsQ->orderByRaw('COALESCE(NULLIF(products.price_discount,0), products.price) desc');
                break;
            case 'new':
            default:
                $productsQ->orderBy('products.created_at', 'desc');
                break;
        }

        // == Paginate ==
        $products = $productsQ->paginate($perPage)->withQueryString();

        return view('frontend.products.index', compact(
            'categories',
            'currentCategory',
            'products',
            'sort',
            'perPage'
        ));
    }
    /**
     * Route cho trang danh mục, sẽ chuyển hướng logic về hàm index.
     * -> Phần này của anh đã tốt, giữ nguyên.
     */
    public function byCategory(Category $category, Request $request)
    {
        $request->query->set('category_slug', $category->slug);
        return $this->index($request);
    }

    /**
     * Hiển thị chi tiết sản phẩm.
     */
    public function show(Product $product)
{
    // 1. Load dữ liệu
    // Lưu ý: Dùng slug hoặc id tùy theo route của bạn. Ở đây tôi dùng slug theo chuẩn SEO.
    $product->load(['gallery', 'category', 'variants.attributeValues.attribute']);

    // 2. Xử lý Attributes để hiển thị ra View (Group by Attribute ID)
    $attributes = [];
    foreach ($product->variants as $variant) {
        foreach ($variant->attributeValues as $value) {
            $attrId = $value->attribute->id;
            
            if (!isset($attributes[$attrId])) {
                $attributes[$attrId] = [
                    'name' => $value->attribute->name,
                    'type' => $value->attribute->type, // Quan trọng: Lấy type (color/text)
                    'values' => []
                ];
            }

            // Chỉ thêm value nếu chưa có trong danh sách (tránh trùng lặp)
            $exists = false;
            foreach ($attributes[$attrId]['values'] as $existingVal) {
                if ($existingVal['id'] == $value->id) {
                    $exists = true; 
                    break;
                }
            }

            if (!$exists) {
                $attributes[$attrId]['values'][] = [
                    'id' => $value->id,
                    'value' => $value->value,
                    'color_code' => $value->color_code
                ];
            }
        }
    }

    // 3. Chuẩn bị JSON biến thể để JS xử lý (Tìm biến thể dựa trên các thuộc tính đã chọn)
    $variantsJson = $product->variants->map(function($v) {
        return [
            'id' => $v->id,
            'price' => (float)$v->price,
            'compare_at_price' => (float)$v->compare_at_price,
            'stock' => $v->stock,
            // Tạo mảng ID thuộc tính đã sort để dễ so sánh trong JS: [1, 5]
            'attr_ids' => $v->attributeValues->pluck('id')->sort()->values()->all()
        ];
    });

    // 4. Sản phẩm liên quan
    $relatedProducts = Product::where('category_id', $product->category_id)
        ->where('id', '!=', $product->id)
        ->take(8)
        ->get();

    return view('frontend.products.detail', compact('product', 'attributes', 'variantsJson', 'relatedProducts'));
}
}
