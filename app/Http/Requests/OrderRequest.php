<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
class OrderRequest extends FormRequest
{
    /**
     * Xác thực quyền của người dùng.
     */
    public function authorize(): bool
    {
        return Auth::guard('admin')->check();
    }
    /**
     * Các quy tắc validation.
     */
    public function rules(): array
    {
        return [
            'user_id' => 'nullable|exists:users,id',
            'customer_name' => 'required_without:user_id|nullable|string|max:255',
            'customer_phone' => 'required_without:user_id|nullable|string|max:20',
            'customer_address' => 'required_without:user_id|nullable|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'technician_id' => 'nullable|exists:users,id',
            'note' => 'nullable|string',
            'status' => 'required|string|in:pending,processing,completed,cancelled',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.product_name' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.warranty_months' => 'required|integer|min:0',
        ];
    }
    /**
     * TÙY CHỈNH THÔNG BÁO LỖI
     * Đây là phần sẽ giúp anh biết chính xác lỗi ở đâu.
     */
    public function messages(): array
    {
        return [
            'customer_name.required_without' => 'Vui lòng nhập Tên khách hàng khi không chọn khách hàng có sẵn.',
            'customer_phone.required_without' => 'Vui lòng nhập SĐT khách hàng khi không chọn khách hàng có sẵn.',
            'customer_address.required_without' => 'Vui lòng nhập Địa chỉ khách hàng khi không chọn khách hàng có sẵn.',
            'items.required' => 'Đơn hàng phải có ít nhất một sản phẩm.',
            'items.array' => 'Dữ liệu sản phẩm không hợp lệ.',
            'items.min' => 'Đơn hàng phải có ít nhất :min sản phẩm.',
            'items.*.product_id.required' => 'Có lỗi xảy ra, ID sản phẩm không được để trống.',
            'items.*.quantity.required' => 'Vui lòng nhập số lượng cho tất cả sản phẩm.',
            'items.*.quantity.min' => 'Số lượng của mỗi sản phẩm phải ít nhất là :min.',
            'items.*.price.required' => 'Vui lòng nhập đơn giá cho tất cả sản phẩm.',
            'items.*.price.numeric' => 'Đơn giá của sản phẩm phải là một số.',
        ];
    }
    /**
     * TÙY CHỈNH TÊN THUỘC TÍNH
     * Giúp thông báo lỗi thân thiện hơn.
     */
    public function attributes(): array
    {
        return [
            'user_id' => 'Khách hàng',
            'customer_name' => 'Tên khách vãng lai',
            'customer_phone' => 'SĐT khách vãng lai',
            'customer_address' => 'Địa chỉ khách vãng lai',
            'customer_email' => 'Email khách vãng lai',
            'technician_id' => 'Thợ lắp đặt',
            'note' => 'Ghi chú',
            'status' => 'Trạng thái',
            'items' => 'Sản phẩm',
            'items.*.quantity' => 'Số lượng sản phẩm',
            'items.*.price' => 'Đơn giá sản phẩm',
        ];
    }
}