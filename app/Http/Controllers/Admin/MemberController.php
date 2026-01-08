<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\DealerProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class MemberController extends Controller
{
    protected $mediaService;

    public function __construct(\App\Contracts\MediaServiceContract $mediaService)
    {
        $this->mediaService = $mediaService;
    }

    private const AVATAR_CONFIG = [
        'main' => ['width' => 400, 'height' => 400, 'fit' => true],
        'variants' => ['thumbnail' => ['width' => 100, 'height' => 100, 'fit' => true]],
        'quality' => 85,
        'format' => 'webp'
    ];

    private const LOGO_CONFIG = [
        'main' => ['width' => 600],
        'variants' => ['thumbnail' => ['width' => 200]],
        'quality' => 85,
        'format' => 'webp'
    ];

    public function index(Request $request)
    {
        $keyword = $request->input('keyword');
        
        $members = User::whereHas('dealerProfile')
            ->when($keyword, function ($query) use ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('name', 'LIKE', "%{$keyword}%")
                      ->orWhere('email', 'LIKE', "%{$keyword}%")
                      ->orWhere('phone', 'LIKE', "%{$keyword}%")
                      ->orWhereHas('dealerProfile', function ($qp) use ($keyword) {
                          $qp->where('company_name', 'LIKE', "%{$keyword}%");
                      });
                });
            })
            ->with('dealerProfile')
            ->latest()
            ->paginate(20);

        return view('admin.members.index', compact('members'));
    }

    public function create()
    {
        return view('admin.members.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'company_name' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
            ]);

            // Avatar for User
            if ($request->filled('avatar_original_path')) {
                $this->mediaService->updateMedia(
                    $user,
                    $request->avatar_original_path,
                    'avatars',
                    self::AVATAR_CONFIG,
                    fn($imgData) => $user->setMainImage($imgData)
                );
            }

            $profile = $user->dealerProfile()->create($request->only([
                'company_name', 'representative_name', 'tax_id', 'address', 
                'position', 'business_sector', 'website'
            ]));

            // Logo for Profile
            if ($request->filled('logo_original_path')) {
                $this->mediaService->updateMedia(
                    $profile,
                    $request->logo_original_path,
                    'logos',
                    self::LOGO_CONFIG,
                    fn($imgData) => $profile->setMainImage($imgData)
                );
            }

            DB::commit();
            return redirect()->route('admin.members.index')->with('success', 'Hội viên đã được tạo thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(User $member)
    {
        $member->load('dealerProfile');
        return view('admin.members.edit', compact('member'));
    }

    public function update(Request $request, User $member)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $member->id,
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        DB::beginTransaction();
        try {
            $member->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
            ]);

            if ($request->filled('password')) {
                $member->update(['password' => Hash::make($request->password)]);
            }

            // Avatar for User
            if ($request->filled('avatar_original_path')) {
                $this->mediaService->updateMedia(
                    $member,
                    $request->avatar_original_path,
                    'avatars',
                    self::AVATAR_CONFIG,
                    fn($imgData) => $member->setMainImage($imgData),
                    fn() => $member->mainImage()
                );
            }

            $profile = $member->dealerProfile;
            $profile->update($request->only([
                'company_name', 'representative_name', 'tax_id', 'address', 
                'position', 'business_sector', 'website'
            ]));

            // Logo for Profile
            if ($request->filled('logo_original_path')) {
                $this->mediaService->updateMedia(
                    $profile,
                    $request->logo_original_path,
                    'logos',
                    self::LOGO_CONFIG,
                    fn($imgData) => $profile->setMainImage($imgData),
                    fn() => $profile->mainImage()
                );
            }

            DB::commit();
            return redirect()->route('admin.members.index')->with('success', 'Hội viên đã được cập nhật.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(User $member)
    {
        DB::beginTransaction();
        try {
            $member->dealerProfile()->delete();
            $member->delete();
            DB::commit();
            return redirect()->route('admin.members.index')->with('success', 'Hội viên đã được xóa.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
