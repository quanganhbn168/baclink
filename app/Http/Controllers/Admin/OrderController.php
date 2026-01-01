<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\ProductVariant;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\ShippingStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Enum;

class OrderController extends Controller
{
    // 1. DANH SÁCH ĐƠN HÀNG
    public function index(Request $request)
    {
        $query = Order::query()->with('user');

        // Tìm kiếm
        $query->when($request->search, function ($q, $search) {
            $q->where(function ($subQ) use ($search) {
                $subQ->where('id', 'like', "%{$search}%")
                     ->orWhere('customer_name', 'like', "%{$search}%")
                     ->orWhere('customer_phone', 'like', "%{$search}%")
                     ->orWhereHas('user', function ($userQ) use ($search) {
                         $userQ->where('name', 'like', "%{$search}%")
                               ->orWhere('phone', 'like', "%{$search}%");
                     });
            });
        });

        // Lọc theo Status
        $query->when($request->filled('status'), fn($q) => $q->where('status', $request->status));
        $query->when($request->filled('payment_status'), fn($q) => $q->where('payment_status', $request->payment_status));
        $query->when($request->filled('shipping_status'), fn($q) => $q->where('shipping_status', $request->shipping_status));

        $orders = $query->latest('id')->paginate(10);

        return view('admin.orders.index', compact('orders'));
    }

    // 2. FORM TẠO MỚI
    public function create()
    {
        // Load User kèm dealerProfile để View lấy được mức chiết khấu
        $users = User::with('dealerProfile')
            ->select('id', 'name', 'phone', 'email')
            ->get();
        
        // Lấy sản phẩm kèm biến thể để JS xử lý
        $products = Product::with('productVariants')
            ->select('id', 'name', 'price', 'code') // hoặc 'sku'
            ->where('status', 1) // Chỉ lấy sp đang hoạt động
            ->get();

        return view('admin.orders.create', compact('users', 'products'));
    }

    // 3. XỬ LÝ LƯU ĐƠN HÀNG MỚI
    public function store(Request $request)
    {
        // 1. Validate dữ liệu đầu vào
        $request->validate([
            'customer_name'  => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'user_id'        => 'nullable|exists:users,id',
            'items'          => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
        ]);

        // 2. Xác định mức chiết khấu (Server-side Logic)
        // Tuyệt đối không lấy discount từ $request gửi lên để tránh hack giá
        $discountRate = 0;
        
        if ($request->user_id) {
            $user = User::with('dealerProfile')->find($request->user_id);
            // Kiểm tra nếu có profile đại lý thì lấy mức chiết khấu
            if ($user && $user->dealerProfile) {
                $discountRate = $user->dealerProfile->discount_rate ?? 0;
            }
        }

        $subtotal = 0;
        $orderItemsData = [];

        // 3. Duyệt qua từng sản phẩm để tính Subtotal
        foreach ($request->items as $item) {
            $product = Product::find($item['product_id']);
            
            // Mặc định lấy giá sản phẩm cha
            $price = $product->price; 
            $productName = $product->name;
            $variantId = null;

            // Xử lý biến thể nếu có
            if (!empty($item['product_variant_id'])) {
                $variant = ProductVariant::find($item['product_variant_id']);
                
                // Chỉ áp dụng nếu variant thuộc đúng sản phẩm này
                if ($variant && $variant->product_id == $product->id) {
                    $variantId = $variant->id;
                    $price = $variant->price ?? $product->price;
                    
                    // Ghép tên biến thể: "Áo Thun (Đỏ - L)"
                    $variantLabel = $variant->variant_name ?? ($variant->size . ' - ' . $variant->color);
                    if ($variantLabel) {
                        $productName .= " ({$variantLabel})";
                    }
                }
            }

            // Tính thành tiền dòng này
            $lineTotal = $price * $item['quantity'];
            $subtotal += $lineTotal;

            // Chuẩn bị dữ liệu insert bảng order_items
            $orderItemsData[] = [
                'product_id'         => $product->id,
                'product_variant_id' => $variantId,
                'product_name'       => $productName, 
                'product_price'      => $price,        
                'quantity'           => $item['quantity'],
                'subtotal'           => $lineTotal,
            ];
        }

        // 4. Tính toán tổng cuối cùng
        // Tiền giảm = Tổng tiền hàng * (% chiết khấu / 100)
        $discountAmount = 0;
        if ($discountRate > 0) {
            $discountAmount = $subtotal * ($discountRate / 100);
        }
        
        $totalPrice = $subtotal - $discountAmount;
        // 5. Lưu vào Database (Sử dụng Transaction để an toàn)
        DB::transaction(function () use ($request, $subtotal, $discountAmount, $discountRate, $totalPrice, $orderItemsData) {
            // Tạo Order
            $order = Order::create([
                'user_id'          => $request->user_id,
                'customer_name'    => $request->customer_name,
                'customer_phone'   => $request->customer_phone,
                'customer_address' => $request->shipping_address,
                'note'             => $request->note,
                
                // Các trường tài chính
                'subtotal'         => $subtotal,        // Tổng tiền hàng
                'discount_rate'    => $discountRate,    // % Giảm
                'discount_amount'  => $discountAmount,  // Số tiền giảm
                'total_price'      => $totalPrice,      // Khách phải trả
                
                // Trạng thái mặc định
                'status'           => OrderStatus::NEW,
                'payment_status'   => PaymentStatus::UNPAID,
                'shipping_status'  => ShippingStatus::NOT_SHIPPED,
            ]);

            // Tạo Order Items (createMany rất tiện)
            $order->orderItems()->createMany($orderItemsData);
        });

        $msg = 'Tạo đơn hàng thành công!';
        if ($discountRate > 0) {
            $msg .= " Đã áp dụng chiết khấu đại lý {$discountRate}%.";
        }

        return redirect()->route('admin.orders.index')->with('success', $msg);
    }

    // 4. CHI TIẾT ĐƠN HÀNG
    public function show(Order $order)
    {
        $order->load([
            'user.dealerProfile', // Load thêm profile để xem thông tin đại lý nếu cần
            'orderItems.product', 
            'orderItems.productVariant' 
        ]);

        return view('admin.orders.show', compact('order'));
    }

    // 5. FORM SỬA ĐƠN HÀNG
    public function edit(Order $order)
    {
        // Kiểm tra quyền sửa (dựa trên Enum)
        // Giả sử Enum có hàm canEdit(), nếu chưa có thì anh check thủ công:
        // if ($order->status === OrderStatus::COMPLETED || $order->status === OrderStatus::CANCELLED) ...
        if (method_exists($order->status, 'canEdit') && !$order->status->canEdit()) {
            return redirect()->route('admin.orders.index')
                ->with('error', 'Đơn hàng này đã hoàn tất hoặc đã hủy, không thể chỉnh sửa.');
        }

        $order->load(['orderItems.product', 'orderItems.productVariant']);
        
        return view('admin.orders.edit', compact('order'));
    }

    // 6. XỬ LÝ CẬP NHẬT (Chỉ cập nhật thông tin chung & Status)
    public function update(Request $request, Order $order)
    {
        if (method_exists($order->status, 'canEdit') && !$order->status->canEdit()) {
            return redirect()->route('admin.orders.index')
                ->with('error', 'Không được phép cập nhật đơn hàng này.');
        }

        $validated = $request->validate([
            'customer_name'    => 'required|string|max:255',
            'customer_phone'   => 'required|string|max:20',
            'customer_address' => 'required|string|max:500',
            'note'             => 'nullable|string',
            'status'           => ['required', new Enum(OrderStatus::class)],
            'payment_status'   => ['required', new Enum(PaymentStatus::class)],
            'shipping_status'  => ['required', new Enum(ShippingStatus::class)],
        ]);

        $order->update($validated);

        return redirect()->route('admin.orders.index')
            ->with('success', 'Cập nhật đơn hàng thành công!');
    }
    
    // 7. XÓA ĐƠN HÀNG
    public function destroy(Order $order)
    {
        if (method_exists($order->status, 'canDelete') && !$order->status->canDelete()) {
            return redirect()->back()->with('error', 'Không thể xóa đơn hàng đang xử lý hoặc đã hoàn thành.');
        }
        
        // Xóa dữ liệu liên quan (lịch sử, chi tiết) đã được chuyển sang Model Event (deleting)
        $order->delete();
        
        return redirect()->route('admin.orders.index')->with('success', 'Đã xóa đơn hàng.');
    }
}