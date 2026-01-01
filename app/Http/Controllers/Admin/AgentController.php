<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// Import các FormRequest đã tạo
use App\Http\Requests\Admin\Agent\StoreAgentRequest;
use App\Http\Requests\Admin\Agent\UpdateAgentRequest;
use App\Http\Requests\Admin\Agent\DepositRequest;

// Import Service xử lý logic
use App\Services\AgentService;

class AgentController extends Controller
{
    protected $agentService;

    /**
     * Inject AgentService vào Controller
     */
    public function __construct(AgentService $agentService)
    {
        $this->agentService = $agentService;
    }

    /**
     * Hiển thị danh sách Đại lý (có phân trang & tìm kiếm)
     */
    public function index(Request $request)
    {
        $pageTitle = 'Quản lý Đại lý';
        
        // Lấy các tham số lọc từ URL (ví dụ ?keyword=...)
        $filters = $request->only(['keyword']);
        
        // Gọi Service để lấy dữ liệu
        $agents = $this->agentService->getList($filters);

        return view('admin.agents.index', compact('agents', 'pageTitle'));
    }

    /**
     * Hiển thị Form thêm mới
     */
    public function create()
    {
        $pageTitle = 'Thêm Đại lý mới';
        return view('admin.agents.create', compact('pageTitle'));
    }

    /**
     * Xử lý lưu Đại lý mới
     * Sử dụng StoreAgentRequest để validate dữ liệu đầu vào
     */
    public function store(StoreAgentRequest $request)
    {
        try {
            // Gọi Service để tạo User + Profile
            $this->agentService->create($request->validated());

            return redirect()->route('admin.agents.index')
                             ->with('success', 'Đã thêm đại lý mới thành công!');
        } catch (\Exception $e) {
            // Log lỗi nếu cần thiết: Log::error($e->getMessage());
            return back()->with('error', 'Lỗi hệ thống: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Hiển thị chi tiết hồ sơ Đại lý (Dashboard CRM)
     */
    public function show($id)
    {
        try {
            $agent = $this->agentService->getDetail($id);
            $pageTitle = 'Hồ sơ đại lý: ' . $agent->name;
            
            return view('admin.agents.show', compact('agent', 'pageTitle'));
        } catch (\Exception $e) {
            return redirect()->route('admin.agents.index')
                             ->with('error', 'Không tìm thấy đại lý hoặc đại lý đã bị xóa.');
        }
    }

    /**
     * Hiển thị Form chỉnh sửa
     */
    public function edit($id)
    {
        try {
            $agent = $this->agentService->getDetail($id);
            $pageTitle = 'Cập nhật thông tin: ' . $agent->name;
            
            return view('admin.agents.edit', compact('agent', 'pageTitle'));
        } catch (\Exception $e) {
            return redirect()->route('admin.agents.index')
                             ->with('error', 'Không tìm thấy đại lý.');
        }
    }

    /**
     * Xử lý cập nhật thông tin
     * Sử dụng UpdateAgentRequest để validate
     */
    public function update(UpdateAgentRequest $request, $id)
    {
        try {
            $this->agentService->update($id, $request->validated());

            return redirect()->route('admin.agents.index')
                             ->with('success', 'Cập nhật thông tin đại lý thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi cập nhật: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Xóa đại lý
     */
    public function destroy($id)
    {
        try {
            $this->agentService->delete($id);
            return back()->with('success', 'Đã xóa đại lý và toàn bộ hồ sơ liên quan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Không thể xóa: ' . $e->getMessage());
        }
    }

    /**
     * Xử lý nạp tiền vào ví
     * Sử dụng DepositRequest để validate số tiền và ghi chú
     */
    public function deposit(DepositRequest $request, $id)
    {
        try {
            $this->agentService->deposit($id, $request->amount, $request->note);
            
            return back()->with('success', 'Đã nạp tiền thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Giao dịch thất bại: ' . $e->getMessage());
        }
    }
}