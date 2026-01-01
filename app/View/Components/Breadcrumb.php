<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Request;

class Breadcrumb extends Component
{
    public array $crumbs = [];

    /**
     * @param string $pageTitle Tiêu đề của trang hiện tại (VD: tên sản phẩm, tên danh mục)
     */
    public function __construct(public ?string $pageTitle = null)
    {
        $this->generateCrumbs();
    }

    protected function generateCrumbs(): void
    {
        $segments = Request::segments();
        $url = '';

        // Bỏ qua segment 'admin' đầu tiên
        if (isset($segments[0]) && $segments[0] === 'admin') {
            array_shift($segments);
        }

        // Thêm crumb "Trang chủ"
        $this->crumbs[] = ['label' => 'Trang chủ', 'url' => route('admin.dashboard')];

        // Xử lý các segment còn lại
        foreach ($segments as $index => $segment) {
            $url .= '/' . $segment;
            $isLast = $index === count($segments) - 1;

            // Bỏ qua các action như 'edit', 'create' hoặc các segment là số (ID)
            if (is_numeric($segment) || in_array($segment, ['edit', 'create'])) {
                continue;
            }

            // Đây là segment của resource (ví dụ: 'categories', 'products')
            // Link sẽ trỏ đến trang index của resource đó
            $this->crumbs[] = [
                'label' => $this->translateSegment($segment),
                'url' => url('admin' . $url)
            ];
        }

        // Xử lý crumb cuối cùng (trang hiện tại)
        $lastSegment = end($segments);
        if ($lastSegment) {
            if ($this->pageTitle) {
                // Nếu có pageTitle truyền vào (VD: tên sản phẩm), hiển thị nó
                $finalCrumbLabel = $this->pageTitle;
            } else {
                // Nếu không, dịch action ra (edit -> Chỉnh sửa)
                $finalCrumbLabel = $this->translateSegment($lastSegment);
            }
            // Ghi đè label của crumb cuối cùng nếu đó là trang con (không phải index)
            if (count($this->crumbs) > 1 && $lastSegment !== 'index') {
                 $this->crumbs[] = ['label' => $finalCrumbLabel, 'url' => null];
            }
        }
    }

    /**
     * Hàm helper để dịch các segment sang Tiếng Việt cho đẹp
     */
    protected function translateSegment(string $segment): string
    {
        $translations = [
            'categories' => 'Danh mục',
            'products' => 'Sản phẩm',
            'attributes' => 'Thuộc tính',
            'orders' => 'Đơn hàng',
            'edit' => 'Chỉnh sửa',
            'create' => 'Tạo mới',
        ];
        return $translations[$segment] ?? ucfirst(str_replace('-', ' ', $segment));
    }

    public function render()
    {
        return view('components.breadcrumb');
    }
}