<?php
namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    
    public function add(Product $product)
    {
        Auth::user()->wishlist()->syncWithoutDetaching([$product->id]);
        return response()->json(['status' => 'success', 'message' => 'Đã thêm sản phẩm vào danh sách yêu thích!']);
    }

    public function remove(Product $product)
    {
        Auth::user()->wishlist()->detach($product->id);
        return response()->json(['status' => 'success', 'message' => 'Đã xóa sản phẩm khỏi danh sách yêu thích!']);
    }
}