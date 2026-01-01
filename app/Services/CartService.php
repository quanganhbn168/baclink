<?php
namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Session;

class CartService
{
    protected string $sessionKey = 'cart';

    public function getItems(): array
    {
        return Session::get($this->sessionKey, []);
    }

    public function add(int $productId, int $quantity = 1): void
    {
        $cart = $this->getItems();
        $product = Product::findOrFail($productId);

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $cart[$productId] = [
                'name'     => $product->name,
                'price'    => $product->price,
                'quantity' => $quantity,
                'image'    => $product->image,
            ];
        }

        Session::put($this->sessionKey, $cart);
    }

    public function update(int $productId, int $quantity): void
    {
        $cart = $this->getItems();

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] = max(1, $quantity);
            Session::put($this->sessionKey, $cart);
        }
    }

    public function remove(int $productId): void
    {
        $cart = $this->getItems();

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            Session::put($this->sessionKey, $cart);
        }
    }

    public function clear(): void
    {
        Session::forget($this->sessionKey);
    }

    public function getTotalQuantity(): int
    {
        return collect($this->getItems())->sum('quantity');
    }

    public function getTotalPrice(): float
    {
        return collect($this->getItems())
            ->reduce(fn ($carry, $item) => $carry + $item['price'] * $item['quantity'], 0);
    }
}
