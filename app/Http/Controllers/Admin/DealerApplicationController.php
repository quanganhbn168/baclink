<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DealerApplicationService;
use Illuminate\Http\Request;

class DealerApplicationController extends Controller
{
    protected $dealerService;

    public function __construct(DealerApplicationService $dealerService)
    {
        $this->dealerService = $dealerService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['keyword', 'status']);
        $applications = $this->dealerService->getLists($filters);
        return view('admin.dealers.applications.index', compact('applications'));
    }

    public function edit($id)
    {
        $application = $this->dealerService->findById($id);
        return view('admin.dealers.applications.edit', compact('application'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'required|string|max:20',
            'email'   => 'nullable|email|max:255',
            'company' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'message' => 'nullable|string',
        ]);

        $this->dealerService->update($id, $data);

        return redirect()->route('admin.dealer-applications.index')
                         ->with('success', 'Cập nhật thông tin thành công!');
    }

    /**
     * Xử lý Duyệt / Từ chối
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|integer']);

        try {
            $result = $this->dealerService->updateStatus($id, $request->status);

            if ($request->status == 1 && is_string($result)) {
                // Nếu là duyệt và trả về chuỗi (là password)
                return back()->with('success', "Đã duyệt và tạo tài khoản đại lý! Mật khẩu tạm: {$result}");
            }

            return back()->with('success', 'Đã cập nhật trạng thái hồ sơ!');

        } catch (\Exception $e) {
            // Bắt lỗi (ví dụ Email trùng)
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->dealerService->delete($id);
        return back()->with('success', 'Đã xóa hồ sơ đăng ký!');
    }
}