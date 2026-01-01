<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;
class CartController extends Controller
{
    /* ===== Session helpers ===== */
    private function getSessionCart(): array
    {
        return session()->get('cart', []);
    }
    private function saveSessionCart(array $cart): void
    {
        session()->put('cart', array_values($cart));
    }
    /* ===== Enrich session cart => items + totals ===== */
    public function enrichSessionCart(): array
    {
        $raw = $this->getSessionCart();
        $items = [];
        $totalPrice = 0;
        $totalQty   = 0;
        foreach ($raw as $idx => $row) {
            // Eager load hình ảnh để tránh N+1 query
            $product = Product::with(['slugData', 'images'])->find($row['product_id']);
            // Nếu sản phẩm bị xóa khỏi DB thì bỏ qua
            if (!$product) continue;
            $variant = null;
            $price   = $product->price_discount ?: $product->price ?: 0;
            $variantText = ''; // Mặc định không có biến thể
            // Nếu item có variant_id
            if (!empty($row['variant_id'])) {
                $variant = ProductVariant::find($row['variant_id']);
                if ($variant) {
                    $price = $variant->price;
                    // LẤY TRỰC TIẾP TỪ CỘT variant_name
                    $variantText = $variant->variant_name; 
                }
            }
            // Ưu tiên ảnh chính (hoặc ảnh biến thể nếu anh có làm logic ảnh biến thể)
            $imageUrl = optional($product->mainImage())->url() 
                     ?: asset('images/no-image.png');
            $qty = max(1, (int)($row['quantity'] ?? 1));
            $totalQty   += $qty;
            $totalPrice += $price * $qty;
            $items[] = [
                'index'        => $idx,
                'product_id'   => $product->id,
                'variant_id'   => $variant->id ?? null,
                'quantity'     => $qty,
                'name'         => $product->name,
                'variant_text' => $variantText, // Đã có tên cụ thể
                'price'        => (float) $price,
                'image'        => $imageUrl,
                'url'          => route('frontend.slug.handle', $product->slugValue ?? $product->id),
            ];
        }
        return [
            'items'          => $items,
            'total_quantity' => $totalQty,
            'total_price'    => $totalPrice,
        ];
    }
    /* ===== API: GET /cart (JSON) ===== */
    public function index()
    {
        return response()->json($this->enrichSessionCart());
    }
    /* ===== POST /cart/add ===== */
    public function add(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required','exists:products,id'],
            'variant_id' => ['nullable','exists:product_variants,id'],
            'quantity'   => ['required','integer','min:1'],
        ]);
        $cart = $this->getSessionCart();
        // Tìm xem sản phẩm + biến thể đã có trong giỏ chưa
        $found = false;
        foreach ($cart as &$it) {
            if (($it['product_id'] == $data['product_id']) &&
                (($it['variant_id'] ?? null) == ($data['variant_id'] ?? null))) {
                $it['quantity'] += $data['quantity'];
                $found = true;
                break;
            }
        }
        if (!$found) $cart[] = $data;
        $this->saveSessionCart($cart);
        $payload = $this->enrichSessionCart();
        return response()->json([
            'success' => true,
            'message' => 'Đã thêm vào giỏ hàng!',
            'total_quantity' => $payload['total_quantity'],
            'cart' => $payload,
        ]);
    }
    /* ===== PUT /cart/update/{index} ===== */
    public function update(Request $request, $index)
    {
        $cart = $this->getSessionCart();
        if (!isset($cart[$index])) {
            return response()->json(['success' => false, 'message' => 'Item not found'], 404);
        }
        $qty = max(0, (int)$request->input('quantity', 1));
        if ($qty === 0) {
            unset($cart[$index]);
        } else {
            $cart[$index]['quantity'] = $qty;
        }
        $this->saveSessionCart($cart);
        $payload = $this->enrichSessionCart();
        return response()->json([
            'success' => true,
            'total_quantity' => $payload['total_quantity'],
            'cart' => $payload,
        ]);
    }
    /* ===== DELETE /cart/remove/{index} ===== */
    public function remove($index)
    {
        $cart = $this->getSessionCart();
        if (isset($cart[$index])) {
            unset($cart[$index]);
            $this->saveSessionCart($cart);
        }
        $payload = $this->enrichSessionCart();
        return response()->json([
            'success' => true,
            'total_quantity' => $payload['total_quantity'],
            'cart' => $payload,
        ]);
    }
    /* ===== POST /cart/clear-all ===== */
    public function clearAll()
    {
        session()->forget('cart');
        return response()->json(['success' => true]);
    }
    /* ===== Trang giỏ hàng (Blade) ===== */
    public function showCartPage()
    {
        $data = $this->enrichSessionCart();
        return view('cart.index', [
            'items'     => $data['items'],
            'cartCount' => $data['total_quantity'],
        ]);
    }

    /* ===== POST /cart/buy-now ===== */
    public function buyNow(Request $request)
    {
        // 1. Validate input
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'variant_id' => ['nullable', 'exists:product_variants,id'],
            'quantity'   => ['required', 'integer', 'min:1'],
        ]);

        // 2. Logic thêm vào giỏ hàng (Giống hệt hàm add)
        $cart = $this->getSessionCart();
        $found = false;

        foreach ($cart as &$it) {
            if (($it['product_id'] == $data['product_id']) &&
                (($it['variant_id'] ?? null) == ($data['variant_id'] ?? null))) {
                $it['quantity'] += $data['quantity'];
                $found = true;
                break;
            }
        }

        if (!$found) {
            $cart[] = $data;
        }

        $this->saveSessionCart($cart);

        // 3. Thay vì redirect ngay, trả về JSON chứa URL đích
        return response()->json([
            'success' => true,
            'redirect_url' => route('checkout.index') // Frontend sẽ location.href tới đây
        ]);
    }
}