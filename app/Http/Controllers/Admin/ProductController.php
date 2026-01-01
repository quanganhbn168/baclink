<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Attribute;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $productService
    ) {}

    /**
     * AJAX: Kiểm tra trùng mã sản phẩm (giữ nguyên để dùng cho form nhập liệu)
     */
    public function validateUniqueness(Request $request)
    {
        $productId = $request->input('productId');

        $validator = Validator::make($request->only('code'), [
            'code' => ['required', 'string', Rule::unique('products', 'code')->ignore($productId)],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => ['code' => $validator->errors()->get('code')],
            ]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Danh sách sản phẩm (Phân trang thường)
     */
    public function index(Request $request)
    {
        // Hàm list trong Service của anh đã trả về Paginator rồi
        // nên ta chỉ cần lấy ra và đẩy sang view
        [$products, $filterCategories] = $this->productService->list($request);

        return view('admin.products.index', compact('products', 'filterCategories'));
    }

    /**
     * Form thêm mới
     */
    public function create()
    {
        // Lấy danh mục để select
        $categories = Category::pluck('name', 'id')->toArray();
        $attributes = Attribute::where('is_variant_defining', true)
        ->with('values')
        ->get();
        return view('admin.products.create', compact('categories', 'attributes'));
    }

    /**
     * Xử lý lưu mới
     */
    public function store(ProductRequest $request)
    {
        $data = $request->validated();

        // 1. Xử lý Checkbox (HTML không gửi nếu bỏ tick -> Gán mặc định 0)
        $data['status']       = $request->has('status') ? 1 : 0;
        $data['is_featured']  = $request->has('is_featured') ? 1 : 0;
        $data['is_on_sale']   = $request->has('is_on_sale') ? 1 : 0;
        $data['has_variants'] = $request->has('has_variants') ? 1 : 0;

        // 2. Lấy dữ liệu Media & Biến thể (Variants)
        // input() để lấy cả những trường không nằm trong quy tắc validation (nếu có)
        // hoặc để đảm bảo lấy đúng cấu trúc mảng
        $data['image_original_path']    = $request->input('image_original_path');
        $data['gallery_original_paths'] = $request->input('gallery_original_paths');
        $data['variants']               = $request->input('variants', []);

        // 3. Gọi Service
        $product = $this->productService->create($data);

        // 4. Điều hướng
        return $request->has('save_new')
            ? redirect()->route('admin.products.create')->with('success', 'Thêm sản phẩm mới thành công.')
            : redirect()->route('admin.products.index')->with('success', 'Sản phẩm đã được tạo thành công.');
    }

    public function edit(Product $product)
    {
        // 1. Load quan hệ
        $product->load(['variants.attributeValues.attribute', 'gallery']);

        // 2. Lấy danh sách để fill vào dropdown (như cũ)
        $categories = Category::where('status', true)->pluck('name', 'id')->toArray();
        $attributes = Attribute::where('is_variant_defining', true)->with('values')->get();

        // 3. XỬ LÝ MỚI: Tổng hợp Attributes đã được sử dụng bởi sản phẩm này
        // Cấu trúc mong muốn: [ {id: 1, values: [10, 11]}, {id: 2, values: [20, 21]} ]
        $usedAttributes = [];

        foreach ($product->variants as $variant) {
            foreach ($variant->attributeValues as $val) {
                $attrId = $val->attribute_id;
                $valId = $val->id;

                if (!isset($usedAttributes[$attrId])) {
                    $usedAttributes[$attrId] = [
                        'id' => $attrId,
                        'values' => []
                    ];
                }
                
                // Chỉ thêm value nếu chưa có trong mảng (tránh trùng lặp)
                if (!in_array($valId, $usedAttributes[$attrId]['values'])) {
                    $usedAttributes[$attrId]['values'][] = $valId;
                }
            }
        }

        // Chuyển về dạng mảng index 0, 1, 2... thay vì key là attrId để JS dễ loop
        $usedAttributes = array_values($usedAttributes);

        return view('admin.products.edit', compact('product', 'categories', 'attributes', 'usedAttributes'));
    }

    /**
     * Xử lý cập nhật
     */
    public function update(ProductRequest $request, Product $product)
    {
        ini_set('memory_limit', '512M');
        $data = $request->validated();

        // 1. Xử lý Checkbox
        $data['status']       = $request->has('status') ? 1 : 0;
        $data['is_featured']  = $request->has('is_featured') ? 1 : 0;
        $data['is_on_sale']   = $request->has('is_on_sale') ? 1 : 0;
        $data['has_variants'] = $request->has('has_variants') ? 1 : 0;

        // 2. Lấy dữ liệu Media & Biến thể
        $data['image_original_path']    = $request->input('image_original_path');
        $data['gallery_original_paths'] = $request->input('gallery_original_paths');
        $data['variants']               = $request->input('variants', []);

        // 3. Gọi Service cập nhật
        $this->productService->update($product, $data);

        return redirect()->back()->with('success', 'Sản phẩm đã được cập nhật thành công.');
    }

    /**
     * Xóa sản phẩm
     */
    public function destroy(Product $product)
    {
        // Gọi Service để xóa sạch cả ảnh và biến thể liên quan
        $this->productService->delete($product);
        
        return redirect()->route('admin.products.index')->with('success', 'Xóa sản phẩm thành công!');
    }
}