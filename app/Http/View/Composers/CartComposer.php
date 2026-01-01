<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class CartComposer
{
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        // Chỉ thực hiện logic nếu người dùng đã đăng nhập vào guard 'web'
        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();

            // Lấy các mục trong giỏ hàng cùng với thông tin sản phẩm (eager loading)
            // Dựa trên quan hệ 'cartItems' đã định nghĩa trong User model
            $cartItems = $user->cartItems()->with('product')->get();

            // Tính tổng số lượng sản phẩm
            $cartTotalQuantity = $cartItems->sum('quantity');

            // Tính tổng giá trị giỏ hàng
            $cartTotal = $cartItems->sum(function ($item) {
                // Đảm bảo rằng product và price tồn tại để tránh lỗi
                if ($item->product) {
                    return $item->quantity * $item->product->price;
                }
                return 0;
            });

            // Gửi các biến này tới view
            $view->with([
                'cartItems' => $cartItems,
                'cartTotalQuantity' => $cartTotalQuantity,
                'cartTotal' => $cartTotal,
            ]);

        } else {
            // Nếu là khách, vẫn tạo các biến rỗng để tránh lỗi "Undefined variable"
            $view->with([
                'cartItems' => collect([]), // trả về một collection rỗng
                'cartTotalQuantity' => 0,
                'cartTotal' => 0,
            ]);
        }
    }
}