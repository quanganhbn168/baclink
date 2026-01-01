<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\User;
use App\Models\Product;
use App\Models\ProductVariant;

class CheckoutController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Lấy giỏ trong session.
     * Mỗi item: ['product_id'=>int, 'variant_id'=>int|null, 'quantity'=>int]
     */
    protected function getSessionCart(): array
    {
        return session()->get('cart', []);
    }

    /**
     * Build dữ liệu hiển thị checkout từ session cart
     * (name, price thực dùng, image, variant_text, quantity...)
     */
    protected function enrichCartForView(array $cart): array
    {
        if (empty($cart)) return [];

        $productIds = collect($cart)->pluck('product_id')->unique()->all();
        $variantIds = collect($cart)->pluck('variant_id')->filter()->unique()->all();

        $products = Product::with([
            'slugData',
            // nếu anh có quan hệ mainImage():
            'images' // vẫn fallback ảnh nếu chưa có trait
        ])->whereIn('id', $productIds)->get()->keyBy('id');

        $variants = !empty($variantIds)
            ? ProductVariant::with(['attributeValues.attribute'])
                ->whereIn('id', $variantIds)->get()->keyBy('id')
            : collect();

        $items = [];
        foreach ($cart as $idx => $raw) {
            $p  = $products->get((int)($raw['product_id'] ?? 0));
            if (!$p) continue;

            $vId   = $raw['variant_id'] ?? null;
            $v     = $vId ? $variants->get((int)$vId) : null;
            $qty   = max(1, (int)($raw['quantity'] ?? 1));

            // Giá ưu tiên: variant.price -> product.price_discount -> product.price
            $price = $v?->price ?? ($p->price_discount ?: $p->price ?? 0);

            // Tên biến thể
            $variantText = '';
            if ($v && $v->relationLoaded('attributeValues')) {
                $variantText = $v->attributeValues->map(function ($av) {
                    $attr = $av->attribute->name ?? '';
                    return $attr ? ($attr . ': ' . $av->value) : $av->value;
                })->implode(' / ');
            }

            // Ảnh: ưu tiên mainImage()->url()
            $image = method_exists($p, 'mainImage') && $p->mainImage()
                ? optional($p->mainImage())->url()
                : asset($p->image ?? 'images/setting/no-image.png');

            $items[] = [
                'index'        => $idx,                  // index trong session
                'product_id'   => $p->id,
                'variant_id'   => $v?->id,
                'name'         => $p->name,
                'price'        => (float)$price,
                'quantity'     => $qty,
                'variant_text' => $variantText,
                'image'        => $image,
                'slug'         => $p->slugValue ?? null,
                'subtotal'     => $qty * (float)$price,
            ];
        }

        return $items;
    }

    public function index()
    {
        // => luôn đọc từ session cart (không lấy từ CartItem model nữa)
        $sessionCart = $this->getSessionCart();
        $items       = $this->enrichCartForView($sessionCart);

        return view('checkout.index', [
            'cartItems' => $items, // mảng đã đủ name/price/qty/variant_text/image
        ]);
    }

    /**
     * Đặt hàng không bắt đăng nhập:
     * - Tạo/tìm user theo số điện thoại (không auto đăng nhập)
     * - Lấy items từ session cart
     * - Gọi OrderService để tạo đơn
     * - Xoá giỏ trong session
     */
    public function placeOrder(Request $request)
    {
        $customer = $request->validate([
            'customer_name'    => 'required|string|max:255',
            'customer_phone'   => 'required|string|max:15',
            'customer_address' => 'required|string|max:255',
            'payment_method'   => 'required|in:cod,bank_transfer',
            'note'             => 'nullable|string',
            // giữ tham số cart_data nhưng không dùng nữa; vẫn OK nếu phía trước đã nhúng
            'cart_data'        => 'nullable',
        ]);

        // Giỏ từ session
        $sessionCart = $this->getSessionCart();
        if (empty($sessionCart)) {
            return back()->with('error', 'Giỏ hàng trống.')->withInput();
        }

        // Tạo/tìm user theo phone (email có thể null), không ép đăng nhập
        $user = User::where('phone', $customer['customer_phone'])->first();
        if (!$user) {
            $user = User::create([
                'name'     => $customer['customer_name'],
                'phone'    => $customer['customer_phone'],
                'email'    => null,
                'password' => bcrypt('Str::random(16)'),
            ]);
        }

        try {
            // Truyền thêm user_id vào customerData để service gán đơn cho user
            $customerData = array_merge($customer, ['user_id' => $user->id]);

            // Nếu OrderService của anh đang có hàm createFromCheckout($customerData, $cartItemsEloquent, $guestCart)
            // ta truyền guestCart = session cart, còn $cartItemsEloquent = collect() cho đúng “không dùng model”
            $order = $this->orderService->createFromCheckout(
                $customerData,
                collect(),        // KHÔNG dùng CartItem model nữa
                $sessionCart      // dùng session cart
            );

            // Dọn giỏ & badge
            session()->forget('cart');

            return redirect()
                ->route('checkout.success')
                ->with('order_id', $order->id);
        } catch (\Throwable $e) {
            return back()->with('error', 'Đặt hàng thất bại: ' . $e->getMessage())->withInput();
        }
    }

    public function success()
    {
        $orderId = session('order_id');
        
        if (!$orderId) {
            return redirect('/');
        }

        $order = \App\Models\Order::find($orderId);
        
        if (!$order) {
            return redirect('/');
        }

        return view('checkout.success', ['order' => $order]);
    }
}
