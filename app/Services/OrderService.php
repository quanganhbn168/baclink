<?php
namespace App\Services;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\ShippingStatus;

use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
class OrderService
{
    /**
     * ADMIN: tạo đơn hàng thủ công từ form admin.
     * $data tối thiểu cần: user_id (nullable), customer_name, customer_phone, customer_address,
     * payment_method, note (nullable), items: [ ['product_id'=>..., 'quantity'=>..., 'price'=>optional] ... ]
     */
    public function createForAdmin(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            // Nếu chọn user có sẵn thì dùng, không thì tạo/hoặc để null và ghi thông tin customer_* vào đơn
            $userId = $data['user_id'] ?? null;
            $user = null;
            if ($userId) {
                $user = User::find($userId);
            }
            $order = Order::create([
                'user_id'          => $user?->id,
                'technician_id'     => $data['technician_id'] ?? null,
                'status'           => $data['status'] ?? 'pending',
                'note'             => $data['note'] ?? null,
                'payment_method'   => $data['payment_method'] ?? 'cod',
                'customer_name'    => $data['customer_name'],
                'customer_phone'   => $data['customer_phone'],
                'customer_address' => $data['customer_address'],
                'total_price'      => 0, // sẽ cập nhật sau
            ]);
            $items = $this->normalizeItemsFromAdmin(($data['items'] ?? []), null);
            if (empty($items)) {
                throw new \Exception('Đơn hàng phải có ít nhất 1 sản phẩm.');
            }
            $total = $this->syncItems($order, $items);
            $order->update(['total_price' => $total]);
            return $order;
        });
    }
    /**
     * ADMIN: cập nhật đơn hàng thủ công từ form admin.
     * $data cấu trúc tương tự createForAdmin.
     */
    public function updateForAdmin(Order $order, array $data): Order
    {
        return DB::transaction(function () use ($order, $data) {
            $userId = $data['user_id'] ?? null;
            $user = null;
            if ($userId) {
                $user = User::find($userId);
            }
            $order->update([
                'user_id'          => $user?->id,
                'technician_id'    => $data['technician_id'] ?? $order->technician_id,
                'status'           => $data['status'] ?? $order->status,
                'note'             => $data['note'] ?? $order->note,
                'payment_method'   => $data['payment_method'] ?? $order->payment_method,
                'customer_name'    => $data['customer_name'] ?? $order->customer_name,
                'customer_phone'   => $data['customer_phone'] ?? $order->customer_phone,
                'customer_address' => $data['customer_address'] ?? $order->customer_address,
            ]);
            // Đồng bộ lại items
            $items = $this->normalizeItemsFromAdmin(($data['items'] ?? []), $order);
            if (empty($items)) {
                // Cho phép đơn trống hay không? Thường KHÔNG → ném lỗi
                throw new \Exception('Đơn hàng phải có ít nhất 1 sản phẩm.');
            }
            $total = $this->syncItems($order, $items);
            $order->update(['total_price' => $total]);
            return $order;
        });
    }
    /**
     * FRONT: tạo đơn từ trang checkout.
     * - $cartItems: collection cart của user (nên ->with('product') trước ở Controller)
     * - $guestCart: mảng từ localStorage JSON 'guest_cart'
     */
    public function createFromCheckout(array $customerData, Collection $cartItems, array $guestCart): Order
    {
        return DB::transaction(function () use ($customerData, $cartItems, $guestCart) {
            // 1) Xác định user (nếu guest thì tạo/ghép theo phone)
            $user = Auth::guard('web')->user();
            if (!$user) {
                $user = User::firstOrCreate(
                    ['phone' => $customerData['customer_phone']],
                    [
                        'name'     => $customerData['customer_name'],
                        'address'  => $customerData['customer_address'] ?? null,
                        'password' => bcrypt(Str::random(16)),
                    ]
                );
            }
            // 2) Tạo order trước
            $order = Order::create([
                'user_id'          => $user->id,
                'status'           => OrderStatus::NEW,            // Enum: Mới
                'payment_status'   => PaymentStatus::UNPAID,       // Enum: Chưa thanh toán
                'shipping_status'  => ShippingStatus::NOT_SHIPPED, // Enum: Chưa vận chuyển
                'note'             => $customerData['note'] ?? null,
                'payment_method'   => $customerData['payment_method'],
                'customer_name'    => $customerData['customer_name'],
                'customer_phone'   => $customerData['customer_phone'],
                'customer_address' => $customerData['customer_address'],
                'total_price'      => 0,
            ]);
            // 3) Gom item thô từ 2 nguồn (auth cart / guest cart)
            $raw = [];
            if (Auth::check() && !$cartItems->isEmpty()) {
                foreach ($cartItems as $ci) {
                    $raw[] = [
                        'product_id'   => (int) ($ci->product_id ?? $ci->product?->id),
                        'product_variant_id' => $ci->product_variant_id
    ? (int)$ci->product_variant_id
    : (isset($ci->variant_id) ? (int)$ci->variant_id : null),
                        'quantity'     => (int) $ci->quantity,
                        'variant_text' => $ci->variant_text ?? null,
                    ];
                }
            } else {
                foreach ($guestCart as $gi) {
                    $pid = $gi['product_id'] ?? $gi['productId'] ?? $gi['id'] ?? null;
                    if (!$pid) { continue; }
                    $raw[] = [
                        'product_id'   => (int) $pid,
                        'product_variant_id' => isset($gi['product_variant_id']) ? (int)$gi['product_variant_id']
                      : (isset($gi['variant_id']) ? (int)$gi['variant_id']
                      : (isset($gi['variantId']) ? (int)$gi['variantId'] : null)),
                        'quantity'     => max(1, (int) ($gi['quantity'] ?? 1)),
                        'variant_text' => $gi['variantText'] ?? $gi['variant_text'] ?? null,
                    ];
                }
            }
            if (empty($raw)) {
                throw new \Exception('Giỏ hàng của bạn đang trống.');
            }
            // 4) Nạp Product/Variant một lần
            $pids = array_values(array_unique(array_map(fn($r) => $r['product_id'], $raw)));
            $vids = array_values(array_unique(array_filter(array_map(fn($r) => $r['product_variant_id'] ?? null, $raw))));
            $products = Product::whereIn('id', $pids)->get()->keyBy('id');
            $variants = $vids ? ProductVariant::whereIn('id', $vids)->get()->keyBy('id') : collect();
            // 5) Chuẩn hoá item (tính giá theo DB, không tin giá client)
            $items = [];
            foreach ($raw as $r) {
                $product = $products->get($r['product_id']);
                if (!$product) { continue; }
                $variant = ($r['product_variant_id'] ?? null) ? $variants->get($r['product_variant_id']) : null;
                if ($variant && $variant->product_id != $product->id) {
                    $variant = null; // đề phòng variant không thuộc product
                }
                $unit = $this->resolveUnitPrice($product, $variant);
                $items[] = [
                    'product_id'   => $product->id,
                    'product_variant_id'   => $variant?->id,
                    'product_name' => $product->name,
                    'sku'          => $variant->sku ?? $product->code ?? null,
                    'unit_price'   => $unit,
                    'quantity'     => max(1, (int)$r['quantity']),
                    'variant_text' => $r['variant_text'] ?? null,
                ];
            }
            if (empty($items)) {
                throw new \Exception('Không thể xác định sản phẩm từ giỏ hàng.');
            }
            // 6) Ghi order_items và tính tổng
            $total = $this->syncItems($order, $items);
            $order->update(['total_price' => $total]);
            // 7) Dọn giỏ của user
            if (Auth::check()) {
                try { Auth::user()->cartItems()->delete(); } catch (\Throwable $e) {}
            }
            return $order;
        });
    }
    /**
     * Đồng bộ items: xoá cũ và tạo lại, trả về tổng tiền.
     * $items: mỗi phần tử gồm:
     *  - product_id, variant_id (nullable), product_name, sku (nullable),
     *  - unit_price, quantity, variant_text (nullable)
     */
    private function syncItems(Order $order, array $items): float
    {
        // tuỳ DB của anh: orderItems() là hasMany tới bảng order_items
        $order->orderItems()->delete();
        $total = 0.0;
        foreach ($items as $it) {
            $qty   = max(1, (int)$it['quantity']);
            $price = (float)$it['unit_price'];
            $line  = $price * $qty;
            $total += $line;
            // payload tối thiểu khớp với schema hiện tại của anh
            $payload = [
              'product_id'          => $it['product_id'],
              'product_variant_id'  => $it['product_variant_id'] ?? null,
              'product_name'        => $it['product_name'],
              'product_price'       => $price,
              'quantity'            => $qty,
              'subtotal'            => $line,
              'variant_text'        => $it['variant_text'] ?? null,
              'sku'                 => $it['sku'] ?? null,
              'image'               => $it['image'] ?? null,
          ];
            $order->orderItems()->create($payload);
        }
        return $total;
    }
    /**
     * Chuẩn hoá dữ liệu items từ form admin.
     * Hỗ trợ variant_id, price override (nếu không truyền price thì lấy từ DB).
     * Trả về mảng items đúng chuẩn cho syncItems().
     */
    private function normalizeItemsFromAdmin(array $items): array
    {
        $result = [];
        if (empty($items)) { return $result; }
        $productIds = collect($items)->pluck('product_id')->filter()->unique()->values()->all();
        $variantIds = collect($items)
    ->map(fn($r) => $r['product_variant_id'] ?? $r['variant_id'] ?? null)
    ->filter()->unique()->values()->all();
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');
        $variants = $variantIds ? ProductVariant::whereIn('id', $variantIds)->get()->keyBy('id') : collect();
        foreach ($items as $row) {
            $pid = (int)($row['product_id'] ?? 0);
            $qty = (int)($row['quantity']   ?? 0);
            if ($pid <= 0 || $qty <= 0) continue;
            $product = $products->get($pid);
            if (!$product) continue;
            $variant = !empty($row['variant_id']) ? $variants->get((int)$row['variant_id']) : null;
            if ($variant && $variant->product_id != $product->id) {
                $variant = null;
            }
            // price override nếu admin nhập, ngược lại lấy từ DB (ưu tiên variant price)
            $override = isset($row['price']) && $row['price'] !== '' ? (float)$row['price'] : null;
            $unit     = $this->resolveUnitPrice($product, $variant, $override);
            $result[] = [
                'product_id'   => $product->id,
                'product_variant_id'   => $variant?->id,
                'product_name' => $product->name,
                'sku'          => $variant->sku ?? $product->code ?? null,
                'unit_price'   => $unit,
                'quantity'     => $qty,
                'variant_text' => $row['variant_text'] ?? null,
            ];
        }
        return $result;
    }
    /**
     * Lấy đơn giá tin cậy từ DB: ưu tiên variant->price, rồi product->price_discount, rồi product->price.
     * Có thể truyền $override nếu muốn ép giá (Admin).
     */
    private function resolveUnitPrice(Product $product, ?ProductVariant $variant = null, ?float $override = null): float
    {
        if ($override !== null && $override >= 0) {
            return (float)$override;
        }
        if ($variant && $variant->price !== null && (float)$variant->price > 0) {
            return (float)$variant->price;
        }
        if ($product->price_discount !== null && (float)$product->price_discount > 0) {
            return (float)$product->price_discount;
        }
        return (float)($product->price ?? 0);
    }
}
