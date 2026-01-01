<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Lấy ID sản phẩm để bỏ qua check unique khi update
        $routeParam = $this->route('product'); // Lấy object Product từ route binding
        // Nếu route parameter là ID (trường hợp hiếm nếu dùng binding), ép kiểu int
        $productId = is_object($routeParam) ? $routeParam->id : $routeParam;

        return [
            // --- THÔNG TIN CƠ BẢN ---
            'name'        => ['required', 'string', 'max:255'],
            'code'        => ['required', 'string', 'max:50', Rule::unique('products', 'code')->ignore($productId)],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'slug'        => ['nullable', 'string', 'max:255', Rule::unique('products', 'slug')->ignore($productId)],
            'brand_id'    => ['nullable', 'integer', 'exists:brands,id'],

            // --- TRẠNG THÁI (BOOLEAN) ---
            // Để nullable vì checkbox không tick sẽ không gửi lên
            'status'       => ['nullable', 'boolean'],
            'is_featured'  => ['nullable', 'boolean'], // Đã đổi từ is_home
            'is_on_sale'   => ['nullable', 'boolean'],
            'has_variants' => ['nullable', 'boolean'],

            // --- GIÁ & KHO (SẢN PHẨM ĐƠN) ---
            // Chỉ validate khi KHÔNG có biến thể (hoặc validate lỏng hơn)
            'price'          => ['nullable', 'numeric', 'min:0'],
            'price_discount' => ['nullable', 'numeric', 'min:0', 'lt:price'], // Giá giảm phải nhỏ hơn giá gốc
            'stock'          => ['nullable', 'integer', 'min:0'],

            // --- NỘI DUNG ---
            'description' => ['nullable', 'string'],
            'content'     => ['nullable', 'string'],
            'specifications' => ['nullable', 'string'],

            // --- MEDIA ---
            'image_original_path'    => ['nullable', 'string'],
            'gallery_original_paths' => ['nullable', 'string'], // JSON string

            // --- SEO ---
            'meta_title'       => ['nullable', 'string', 'max:255'],
            'meta_keywords'    => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'meta_image'       => ['nullable', 'string'],

            // --- BIẾN THỂ (VARIANTS) ---
            'variants'                 => ['nullable', 'array'],
            'variants.*.id'            => ['nullable', 'integer'],
            'variants.*.delete_flag'   => ['nullable'],
            'variants.*.attribute_value_ids' => ['nullable', 'string'], // Ensure basic string validation
            
            // Validate từng dòng biến thể
            'variants.*.variant_name'  => ['required_if:has_variants,1', 'nullable', 'string', 'max:255'],
            // SKU nên unique, nhưng unique với logic phức tạp hơn (ignore current ID), tạm thời check string max
            'variants.*.sku'           => ['required_if:has_variants,1', 'nullable', 'string', 'max:50', 'distinct'], 
            'variants.*.price'         => ['required_if:has_variants,1', 'nullable', 'numeric', 'min:0'],
            'variants.*.stock'         => ['required_if:has_variants,1', 'nullable', 'integer', 'min:0'],
            'variants.*.compare_at_price' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'             => 'Tên sản phẩm không được để trống.',
            'code.required'             => 'Mã sản phẩm không được để trống.',
            'code.unique'               => 'Mã sản phẩm này đã tồn tại.',
            'category_id.required'      => 'Vui lòng chọn danh mục sản phẩm.',
            
            'price_discount.lt'         => 'Giá khuyến mãi phải nhỏ hơn giá niêm yết.',
            'price.numeric'             => 'Giá bán phải là số.',
            
            // Thông báo lỗi cho biến thể
            'variants.*.variant_name.required_if' => 'Tên phiên bản không được để trống.',
            'variants.*.price.required_if'        => 'Giá phiên bản không được để trống.',
            'variants.*.stock.required_if'        => 'Tồn kho phiên bản không được để trống.',
        ];
    }
    
    // Tùy chỉnh tên attribute hiển thị trong lỗi (VD: variants.0.price -> Giá phiên bản thứ 1)
    public function attributes()
    {
        return [
            'category_id' => 'danh mục',
            'variants.*.variant_name' => 'tên phiên bản',
            'variants.*.sku' => 'mã SKU',
            'variants.*.price' => 'giá phiên bản',
            'variants.*.stock' => 'tồn kho',
        ];
    }
}