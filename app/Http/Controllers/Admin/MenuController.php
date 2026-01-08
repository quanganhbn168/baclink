<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page; 
use App\Models\Category; // Nhớ use các model anh cần link tới
use Illuminate\Support\Facades\Cache;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        // 1. Lấy danh sách menu
        $menus = \App\Models\Menu::all(); 
        
        // 2. Menu đang chọn
        $currentMenuId = $request->get('menu_id');
        if ($currentMenuId) {
            $menu = \App\Models\Menu::findOrFail($currentMenuId);
        } else {
            $menu = \App\Models\Menu::firstOrCreate(
                ['location' => 'top_nav'],
                ['name' => 'Menu Chính (Top)']
            );
            \App\Models\Menu::firstOrCreate(
                ['location' => 'footer_main'],
                ['name' => 'Menu Chân trang (Footer)']
            );
            $menus = \App\Models\Menu::all();
        }

        // 3. Load dữ liệu phụ trợ
        $pages = \App\Models\Page::select('id', 'title')->get();
        $categories = \App\Models\Category::select('id', 'name')->get();
        $intros = \App\Models\Intro::select('id', 'title')->get();

        // 4. Data cho JS (Alpine)
        // Link hệ thống định nghĩa ngay tại đây để dễ quản lý
        $systemLinks = [
            ['title' => 'Trang chủ', 'route' => 'home'],
            ['title' => 'Giới thiệu', 'route' => 'frontend.intro.index'],
            ['title' => 'Lĩnh vực hoạt động', 'route' => 'frontend.fields.index'],
            ['title' => 'Hội viên', 'route' => 'frontend.members.index'],
            ['title' => 'Tin tức', 'route' => 'frontend.posts.index'],
            ['title' => 'Dịch vụ', 'route' => 'frontend.services.index'],
            ['title' => 'Dự án', 'route' => 'frontend.projects.index'],
            ['title' => 'Tuyển dụng', 'route' => 'frontend.careers.index'],
            ['title' => 'Sản phẩm', 'route' => 'products.index'],
            ['title' => 'Đăng ký Đại lý', 'route' => 'frontend.dealers.create'],
            ['title' => 'Liên hệ', 'route' => 'contact.show'],
        ];

        // Create menu data
        $menuData = [
            'id' => $menu->id,
            'name' => $menu->name,
            'items' => $this->getTree($menu->id),
            'systemLinks' => $systemLinks // Pass directly without filtering for now
        ];

        // Lấy items dạng Eloquent Collection cho Blade render ban đầu
        $menuItems = \App\Models\MenuItem::where('menu_id', $menu->id)
                    ->whereNull('parent_id')
                    ->orderBy('order')
                    ->with('children')
                    ->get();

        return view('admin.menus.index', compact('menus', 'menu', 'pages', 'categories', 'intros', 'menuData', 'menuItems'));
    }

    // Helper đệ quy lấy cây menu
    private function getTree($menuId) {
        $items = \App\Models\MenuItem::where('menu_id', $menuId)
                    ->whereNull('parent_id')
                    ->orderBy('order')
                    ->with('children')
                    ->get();
        
        return $this->formatTree($items);
    }

    private function formatTree($items) {
        return $items->map(function($item) {
            return [
                'id' => $item->id,
                'title' => $item->title,
                'linkable_type' => $item->linkable_type ? class_basename($item->linkable_type) : null,
                'linkable_id' => $item->linkable_id,
                'url' => $item->url,
                'children' => $item->children->isNotEmpty() ? $this->formatTree($item->children->sortBy('order')) : []
            ];
        });
    }

    // API: Thêm Item
    public function storeItem(Request $request) {
        $validated = $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'type' => 'required', // system, page, category, intro, custom
        ]);

        $menuId = $request->menu_id;
        $items = [];

        if ($request->type == 'system') {
            $this->clearMenuCache();
            \App\Models\MenuItem::create([
                'menu_id' => $menuId,
                'title' => $request->title,
                'url' => route($request->route),
                'target' => '_self',
                'order' => 999
            ]);
        }
        elseif ($request->type == 'page') {
            foreach ($request->ids as $id) {
                $page = \App\Models\Page::find($id);
                if ($page) {
                    \App\Models\MenuItem::create([
                        'menu_id' => $menuId,
                        'title' => $page->title,
                        'linkable_id' => $page->id,
                        'linkable_type' => \App\Models\Page::class,
                        'order' => 999
                    ]);
                }
            }
        }
        elseif ($request->type == 'category') {
            if ($request->is_all) {
                // Get ALL categories
                $allCats = \App\Models\Category::all();
                foreach ($allCats as $cat) {
                    \App\Models\MenuItem::create([
                        'menu_id' => $menuId,
                        'title' => $cat->name,
                        'linkable_id' => $cat->id,
                        'linkable_type' => \App\Models\Category::class,
                        'order' => 999
                    ]);
                }
            } else {
                foreach ($request->ids as $id) {
                    $cat = \App\Models\Category::find($id);
                    if ($cat) {
                        \App\Models\MenuItem::create([
                            'menu_id' => $menuId,
                            'title' => $cat->name,
                            'linkable_id' => $cat->id,
                            'linkable_type' => \App\Models\Category::class,
                            'order' => 999
                        ]);
                    }
                }
            }
        }
        elseif ($request->type == 'intro') {
            if ($request->is_all) {
                $allIntros = \App\Models\Intro::all();
                foreach ($allIntros as $item) {
                     \App\Models\MenuItem::create([
                        'menu_id' => $menuId,
                        'title' => $item->title,
                        'linkable_id' => $item->id,
                        'linkable_type' => \App\Models\Intro::class,
                        'order' => 999
                    ]);
                }
            } else {
                foreach ($request->ids as $id) {
                    $item = \App\Models\Intro::find($id);
                    if ($item) {
                        \App\Models\MenuItem::create([
                            'menu_id' => $menuId,
                            'title' => $item->title,
                            'linkable_id' => $item->id,
                            'linkable_type' => \App\Models\Intro::class,
                            'order' => 999
                        ]);
                    }
                }
            }
        }
        elseif ($request->type == 'custom') {
            \App\Models\MenuItem::create([
                'menu_id' => $menuId,
                'title' => $request->title,
                'url' => $request->url,
                'target' => $request->target ?? '_self',
                'order' => 999
            ]);
        }

        // Return rendered HTML for frontend update
        $newItems = \App\Models\MenuItem::where('menu_id', $menuId)
                    ->whereNull('parent_id')
                    ->orderBy('order')
                    ->with('children')
                    ->get();

        $html = '';
        foreach ($newItems as $item) {
            $html .= view('admin.menus.partials.menu-item', ['item' => $item])->render();
        }

        return response()->json([
            'status' => 'success',
            'html' => $html
        ]);
    }

    protected function clearMenuCache()
    {
        Cache::forget('header_menu_structure');
        Cache::forget('footer_menu_structure');
    }

    // API: Xóa Item
    public function destroyItem($id) {
        $item = \App\Models\MenuItem::findOrFail($id);
        $menuId = $item->menu_id;
        $item->delete();
        $this->clearMenuCache();

        // Return updated HTML
        $newItems = \App\Models\MenuItem::where('menu_id', $menuId)
                    ->whereNull('parent_id')
                    ->orderBy('order')
                    ->with('children')
                    ->get();
        
        $html = '';
        foreach ($newItems as $item) {
            $html .= view('admin.menus.partials.menu-item', ['item' => $item])->render();
        }

        return response()->json([
            'status' => 'success',
            'html' => $html
        ]);
    }

    // API: Cập nhật Item (Sửa tên)
    public function updateItem(Request $request, $id) {
        $item = \App\Models\MenuItem::findOrFail($id);
        $item->update(['title' => $request->title]);
        $this->clearMenuCache();
        return response()->json(['status' => 'success']);
    }

    // API: Sắp xếp
    public function updateOrder(Request $request) {
        $source = $request->input('menu'); 
        if ($source) {
            $this->saveMenuTree($source, null);
            $this->clearMenuCache();
        }
        return response()->json(['status' => 'success']);
    }

    private function saveMenuTree($items, $parentId) {
        foreach ($items as $index => $item) {
            $menuItem = \App\Models\MenuItem::find($item['id']);
            if ($menuItem) {
                $menuItem->update([
                    'parent_id' => $parentId,
                    'order' => $index + 1
                ]);
                if (isset($item['children'])) {
                    $this->saveMenuTree($item['children'], $menuItem->id);
                }
            }
        }
    }
}