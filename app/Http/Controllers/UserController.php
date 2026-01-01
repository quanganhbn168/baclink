<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Requests\UserProfileRequest;
use App\Traits\UploadImageTrait;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Spatie\Permission\Models\Role;
class UserController extends Controller
{
    use UploadImageTrait;
    protected $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    public function index(Request $request)
    {
        $users = $this->userService->getAllUsers($request->only('search'));
        return view('admin.users.index', compact('users'));
    }
    public function create()
    {
        $roles = Role::where('guard_name', 'web')->pluck('name', 'name');
        return view('admin.users.create', compact('roles'));
    }
    public function store(UserRequest $request)
    {
        $userData = Arr::except($request->validated(), 'roles');
        $user = $this->userService->store($userData);
        $user->syncRoles($request->roles ?? []);
        return redirect()->route('admin.users.index')->with('success', 'Tạo người dùng thành công.');
    }
    public function edit(User $user)
    {
        $roles = Role::where('guard_name', 'web')->pluck('name', 'name');
        $userRoles = $user->getRoleNames()->all();
        return view('admin.users.edit', compact('user', 'roles', 'userRoles'));
    }
    public function update(UserRequest $request, User $user)
    {
        $userData = Arr::except($request->validated(), 'roles');
        $this->userService->update($user, $userData);
        $user->syncRoles($request->roles ?? []);
        return redirect()->route('admin.users.index')->with('success', 'Cập nhật người dùng thành công.');
    }
    public function destroy(User $user)
    {
        $this->userService->deleteUser($user);
        return redirect()->route('admin.users.index')->with('success', 'Xóa người dùng thành công.');
    }
    /**
     * Hiển thị trang dashboard chính của người dùng.
     */
    public function dashboard()
    {
        $user = Auth::user();
        $recentOrders = $user->orders()->latest()->take(5)->get();
        return view('frontend.dashboard.index', compact('recentOrders'));
    }
    /**
     * Hiển thị trang thông tin cá nhân và form chỉnh sửa.
     */
    public function profile()
    {
        $user = Auth::user();
        return view('frontend.dashboard.profile', compact('user'));
    }

    /**
     * Xử lý việc cập nhật thông tin cá nhân và avatar.
     */
    public function updateProfile(UserProfileRequest $request)
    {
        $user = Auth::user();
        $validatedData = $request->validated();
        if ($request->hasFile('avatar')) {
            $this->deleteImage($user->avatar);
            $path = $this->uploadImage(
                file: $request->file('avatar'),
                folder: 'avatars',
                resizeWidth: 300,
                keepRatio: true,
                convertToWebp: true
            );
            $user->avatar = $path;
        }
        $user->name = $validatedData['name'];
        $user->address = $validatedData['address'] ?? $user->address;
        if (!empty($validatedData['password'])) {
            $user->password = Hash::make($validatedData['password']);
        }
        $user->save();
        return redirect()->route('user.profile')->with('success', 'Cập nhật thông tin thành công!');
    }
    /**
     * Hiển thị lịch sử mua sắm của người dùng.
     */
    public function orderHistory()
    {
        $orders = Auth::user()->orders()->latest()->paginate(10);
        return view('frontend.dashboard.orders', compact('orders'));
    }
    /**
     * Hiển thị chi tiết một đơn hàng cụ thể.
     */
    public function orderDetail($orderId)
    {
        $order = Order::where('id', $orderId)
                      ->where('user_id', Auth::id())
                      ->firstOrFail();
        return view('frontend.dashboard.order_detail', compact('order'));
    }
    /**
     * Hiển thị danh sách sản phẩm yêu thích của người dùng.
     */
    public function wishlist()
    {
        $products = Auth::user()->wishlist()->paginate(12);
        return view('frontend.dashboard.wishlist', compact('products'));
    }
}