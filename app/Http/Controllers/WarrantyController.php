<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class WarrantyController extends Controller
{
    /**
     * Hiển thị trang tra cứu bảo hành (chỉ có form).
     */
    public function index()
    {
        return view('admin.warranty.index');
    }

    /**
     * Xử lý tìm kiếm đơn hàng dựa trên mã code.
     */
    public function search(Request $request)
    {
        $request->validate(['order_code' => 'required|string']);

        $order = Order::where('code', $request->order_code)
                      ->with(['details.product', 'user', 'technician']) // Tải sẵn các quan hệ
                      ->first();

        if (!$order) {
            return redirect()->route('admin.warranty.index')
                             ->with('error', 'Không tìm thấy đơn hàng với mã: ' . $request->order_code);
        }

        // Nếu tìm thấy, trả về view cùng với dữ liệu đơn hàng
        return view('admin.warranty.index', compact('order'));
    }
}