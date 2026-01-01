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
            'name' => 'Họ và tên',
            'phone' => 'Số điện thoại',
            'email' => 'Email',
            'password' => 'Mật khẩu',
        ];

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'unique:users,phone'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:6', 'confirmed'],
        ], $messages, $attributes);

        $user = User::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
        $user->assignRole('customer');
        Auth::login($user);
        return redirect()->route('home');
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