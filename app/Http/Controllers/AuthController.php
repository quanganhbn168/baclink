<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Admin;
class AuthController extends Controller
{
    public function showLoginForm(Request $request)
    {
        $isAdmin = $request->is('admin') || $request->is('admin/*');
        return view($isAdmin ? 'auth.admin.login' : 'auth.client.login');
    }
    public function login(Request $request)
    {
        $isAdmin = $request->is('admin/*');
        $guard = $isAdmin ? 'admin' : 'web';
        $remember = $request->boolean('remember');
        if ($isAdmin) {
            $credentials = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required'],
            ]);
            $loginField = 'email'; 
        } else {
            $credentials = $request->validate([
                'phone' => ['required', 'string'],
                'password' => ['required'],
            ]);
            $loginField = 'phone'; 
        }
        if (Auth::guard($guard)->attempt($credentials, $remember)) {
            $request->session()->regenerate();
            return redirect()->intended($isAdmin ? route('admin.dashboard') : route('home'));
        }
        return back()->withErrors([
            $loginField => 'Thông tin đăng nhập không chính xác.',
        ])->withInput($request->only($loginField, 'remember'));
    }
    public function showRegisterForm()
    {
        return view('auth.client.register');
    }
    public function register(Request $request)
    {
        $messages = [
            'required' => ':attribute không được để trống.',
            'string' => ':attribute phải là chuỗi ký tự.',
            'max' => ':attribute không được vượt quá :max ký tự.',
            'unique' => ':attribute đã được sử dụng.',
            'email' => ':attribute không đúng định dạng email.',
            'min' => ':attribute phải có ít nhất :min ký tự.',
            'confirmed' => 'Mật khẩu nhập lại không khớp.',
        ];

        $attributes = [
            'name' => 'Tên chủ doanh nghiệp',
            'phone' => 'Số điện thoại',
            'email' => 'Email',
            'password' => 'Mật khẩu',
            'company_name' => 'Tên công ty',
            'honorific' => 'Danh xưng',
            'position' => 'Chức danh',
            'business_sector' => 'Nhóm ngành sản xuất',
            'company_intro' => 'Giới thiệu công ty',
            'featured_products' => 'Sản phẩm nổi bật',
            'website' => 'Website',
        ];

        $data = $request->validate([
            // User info
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'unique:users,phone'],
            'email' => ['nullable', 'email', 'unique:users,email'], // Nullable as per form implication, but check Auth
            'password' => ['required', 'min:6', 'confirmed'],
            
            // Dealer/Company info
            'company_name' => ['required', 'string', 'max:255'],
            'honorific' => ['required', 'string'],
            'position' => ['required', 'string'],
            'business_sector' => ['required', 'string'],
            'company_intro' => ['required', 'string'],
            'featured_products' => ['required', 'string'],
            'website' => ['required', 'string'],

            // Assistant info (Nullable)
            'assistant_name' => ['nullable', 'string'],
            'assistant_phone' => ['nullable', 'string'],
            'assistant_email' => ['nullable', 'email'],
        ], $messages, $attributes);

        // Handle empty email for User creation if necessary
        // Auth requires email usually? Let's check User model.
        // Assuming we need a unique email, if user leaves it blank, we might need a workaround or just require column nullable.
        // For now, let's assume email is provided or we generate a placeholder. 
        // Changing strategy: If email is empty, use phone@baclink.local
        $email = $data['email'] ?? ($data['phone'] . '@baclink.local');

        $user = User::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $email,
            'password' => Hash::make($data['password']),
        ]);
        $user->assignRole('customer');

        // Create Dealer Profile
        \App\Models\DealerProfile::create([
            'user_id' => $user->id,
            'company_name' => $data['company_name'],
            'representative_name' => $data['name'],
            'honorific' => $data['honorific'],
            'position' => $data['position'],
            'business_sector' => $data['business_sector'],
            'company_intro' => $data['company_intro'],
            'featured_products' => $data['featured_products'],
            'website' => $data['website'],
            'assistant_name' => $data['assistant_name'],
            'assistant_phone' => $data['assistant_phone'],
            'assistant_email' => $data['assistant_email'],
            'address' => 'Đang cập nhật', // Placeholder as form has no address
            'phone' => $data['phone'], // Sync phone to profile
        ]);

        Auth::login($user);
        return redirect()->route('home')->with('success', 'Đăng ký hội viên thành công!');
    }

    public function logout(Request $request)
    {
        $isAdmin = $request->is('admin/*');
        $guard = $isAdmin ? 'admin' : 'web';
        Auth::guard($guard)->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect($isAdmin ? route('admin.login') : route('home'));
    }
}