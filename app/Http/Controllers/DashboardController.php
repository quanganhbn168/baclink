<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\Post;
use App\Models\Contact;
use App\Models\Order;
use App\Models\DealerApplication;
use App\Models\DealerProfile;
use App\Enums\OrderStatus;

class DashboardController extends Controller
{
    public function dashboard()
    {
        // 1. Thống kê nhanh (Small Boxes)
        $totalProducts = Product::count();
        $totalPosts = Post::count();
        $totalContacts = Contact::count();
        
        // Thống kê Hội viên mới
        $totalMembers = DealerProfile::count();
        $recentMembers = DealerProfile::with('user')->latest()->take(5)->get();

        // 3. Các hoạt động gần đây (Recent Activity)
        $recentContacts = Contact::latest()->take(5)->get();

        return view('admin.dashboard', [
            'totalProducts' => $totalProducts,
            'totalPosts' => $totalPosts,
            'totalContacts' => $totalContacts,
            'totalMembers' => $totalMembers,
            'recentMembers' => $recentMembers,
            'recentContacts' => $recentContacts,
        ]);
    }

    public function toggleField(Request $request)
    {
        $request->validate([
            'model' => 'required|string',
            'id' => 'required|integer',
            'field' => 'required|string',
        ]);

        $modelClass = $this->resolveModelClass($request->model);
        if (!class_exists($modelClass)) {
            return response()->json(['error' => 'Model không tồn tại.'], 404);
        }

        $record = $modelClass::findOrFail($request->id);

        $field = $request->field;

        if (!array_key_exists($field, $record->getAttributes())) {
            return response()->json(['error' => 'Trường không hợp lệ.'], 422);
        }

        $record->$field = !$record->$field;
        $record->save();

        return response()->json([
            'success' => true,
            'value' => $record->$field,
            'message' => "Đã cập nhật $field thành " . ($record->$field ? '✓' : '✗')
        ]);
    }

    protected function resolveModelClass($model)
    {
        $model = Str::studly($model);
        return "App\\Models\\{$model}";
    }
}
