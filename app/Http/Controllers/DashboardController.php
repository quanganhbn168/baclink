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
use App\Enums\OrderStatus;

class DashboardController extends Controller
{
    public function dashboard()
    {
        // 1. Thống kê nhanh (Small Boxes)
        $totalProducts = Product::count();
        $totalPosts = Post::count();
        $totalContacts = Contact::count();
        
        // Đếm đơn hàng mới (Trạng thái NEW = 0)
        $newOrdersCount = Order::where('status', OrderStatus::NEW)->count();
        
        // Đếm đơn đăng ký đại lý mới (ví dụ status = 'pending' hoặc chưa duyệt)
        // Giả sử status mặc định là 'pending' hoặc check theo logic dự án
        $newDealerAppsCount = DealerApplication::where('status', 'pending')->count();


        // 2. Thống kê doanh thu (Ví dụ: Đơn đã hoàn thành)
        // Lưu ý: Cần chắc chắn trường 'total_price' và 'status' tồn tại và đúng logic
        $revenue = Order::where('status', OrderStatus::COMPLETED)->sum('total_price');

        // 3. Các hoạt động gần đây (Recent Activity)
        $recentOrders = Order::with('user')->latest()->take(5)->get();
        $recentContacts = Contact::latest()->take(5)->get();
        $recentDealerApps = DealerApplication::latest()->take(5)->get();

        // 4. Dữ liệu biểu đồ (Charts Data)
        // 4.1 Doanh thu 6 tháng gần nhất
        $revenueData = Order::select(
            DB::raw('sum(total_price) as sum'), 
            DB::raw("DATE_FORMAT(created_at,'%Y-%m') as month")
        )
        ->where('status', OrderStatus::COMPLETED)
        ->where('created_at', '>=', now()->subMonths(6))
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        $chartRevenueLabels = [];
        $chartRevenueData = [];

        // Fill zero for missing months if needed, but for simplicity just map existing
        foreach($revenueData as $data) {
            $chartRevenueLabels[] = "Tháng " . date('m/Y', strtotime($data->month . '-01'));
            $chartRevenueData[] = $data->sum;
        }

        // 4.2 Tỷ lệ trạng thái đơn hàng
        $ordersByStatus = Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();
            
        $chartStatusLabels = [];
        $chartStatusData = [];
        $chartStatusColors = [];

        foreach($ordersByStatus as $item) {
            $chartStatusLabels[] = $item->status->label();
            $chartStatusData[] = $item->count;
            // Map màu từ Enum -> AdminLTE colors (hoặc hex)
            $colorMap = [
                'primary' => '#007bff', 'secondary' => '#6c757d', 'success' => '#28a745',
                'bg-success' => '#28a745', 'warning' => '#ffc107', 'danger' => '#dc3545',
                'info' => '#17a2b8'
            ];
            // Enum return 'primary', 'warning' etc.
            $enumColor = $item->status->color(); 
            // Handle 'bg-success' case if enum returns that, or just simple name
            $chartStatusColors[] = $colorMap[$enumColor] ?? '#cccccc';
        }

        return view('admin.dashboard', [
            'totalProducts' => $totalProducts,
            'totalPosts' => $totalPosts,
            'totalContacts' => $totalContacts,
            'newOrdersCount' => $newOrdersCount,
            'newDealerAppsCount' => $newDealerAppsCount,
            'revenue' => $revenue,
            'recentOrders' => $recentOrders,
            'recentContacts' => $recentContacts,
            'recentDealerApps' => $recentDealerApps,
            'chartRevenueLabels' => $chartRevenueLabels,
            'chartRevenueData' => $chartRevenueData,
            'chartStatusLabels' => $chartStatusLabels,
            'chartStatusData' => $chartStatusData,
            'chartStatusColors' => $chartStatusColors,
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
