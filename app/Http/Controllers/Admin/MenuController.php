<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Intro;
use App\Models\Category; // Nhớ use các model anh cần link tới
use Illuminate\Support\Facades\Cache;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        // 1. Lấy danh sách menu
        $menus = Menu::all(); 
        
        // 2. Menu đang chọn
        $currentMenuId = $request->get('menu_id');
        if ($currentMenuId) {
            $menu = Menu::findOrFail($currentMenuId);
        } else {
            $menu = Menu::firstOrCreate(
                ['location' => 'top_nav'],
                ['name' => 'Menu Chính (Top)']
            );
            Menu::firstOrCreate(
                ['location' => 'footer_main'],
                ['name' => 'Menu Chân trang (Footer)']
            );
            $menus = Menu::all();
        }

        // 3. Load dữ liệu phụ trợ
        $pages = Page::select('id', 'title')->get();
        $categories = Category::select('id', 'name')->get();
        $intros = Intro::select('id', 'title')->get();

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
        $menuItems = MenuItem::where('menu_id', $menu->id)
                    ->whereNull('parent_id')
                    ->orderBy('order')
                    ->with('children')
                    ->get();

        return view('admin.menus.index', compact('menus', 'menu', 'pages', 'categories', 'intros', 'menuData', 'menuItems'));
    }

    // Helper đệ quy lấy cây menu
    private function getTree($menuId) {
        $items = MenuItem::where('menu_id', $menuId)
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
            MenuItem::create([
                'menu_id' => $menuId,
                'title' => $request->title,
                'url' => 'route:' . $request->route,
                'target' => '_self',
                'order' => 999
            ]);
        }
        elseif ($request->type == 'page') {
            foreach ($request->ids as $id) {
                $page = Page::find($id);
                if ($page) {
                    MenuItem::create([
                        'menu_id' => $menuId,
                        'title' => $page->title,
                        'linkable_id' => $page->id,
                        'linkable_type' => Page::class,
                        'order' => 999
                    ]);
                }
            }
        }
        elseif ($request->type == 'category') {
            if ($request->is_all) {
                // Get ALL categories
                $allCats = Category::all();
                foreach ($allCats as $cat) {
                    MenuItem::create([
                        'menu_id' => $menuId,
                        'title' => $cat->name,
                        'linkable_id' => $cat->id,
                        'linkable_type' => Category::class,
                        'order' => 999
                    ]);
                }
            } else {
                foreach ($request->ids as $id) {
                    $cat = Category::find($id);
                    if ($cat) {
                        MenuItem::create([
                            'menu_id' => $menuId,
                            'title' => $cat->name,
                            'linkable_id' => $cat->id,
                            'linkable_type' => Category::class,
                            'order' => 999
                        ]);
                    }
                }
            }
        }
        elseif ($request->type == 'intro') {
            if ($request->is_all) {
                $allIntros = Intro::all();
                foreach ($allIntros as $item) {
                     MenuItem::create([
                        'menu_id' => $menuId,
                        'title' => $item->title,
                        'linkable_id' => $item->id,
                        'linkable_type' => Intro::class,
                        'order' => 999
                    ]);
                }
            } else {
                foreach ($request->ids as $id) {
                    $item = Intro::find($id);
                    if ($item) {
                        MenuItem::create([
                            'menu_id' => $menuId,
                            'title' => $item->title,
                            'linkable_id' => $item->id,
                            'linkable_type' => Intro::class,
                            'order' => 999
                        ]);
                    }
                }
            }
        }
        elseif ($request->type == 'custom') {
            MenuItem::create([
                'menu_id' => $menuId,
                'title' => $request->title,
                'url' => $request->url,
                'target' => $request->target ?? '_self',
                'order' => 999
            ]);
        }

        // Return rendered HTML for frontend update
        $newItems = MenuItem::where('menu_id', $menuId)
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
        foreach (config('translatable.locales', ['vi', 'en']) as $locale) {
            Cache::forget('header_menu_structure_' . $locale);
            Cache::forget('footer_menu_structure_' . $locale);
        }
    }

    // API: Xóa Item
    public function destroyItem($id) {
        $item = MenuItem::findOrFail($id);
        $menuId = $item->menu_id;
        $item->delete();
        $this->clearMenuCache();

        // Return updated HTML
        $newItems = MenuItem::where('menu_id', $menuId)
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

    // API: Get Item translations (for edit popup)
    public function showItem($id) {
        $item = MenuItem::findOrFail($id);
        return response()->json([
            'translations' => $item->getTranslations('title'),
            'url' => $item->url,
            'resolved_url' => $item->link,
        ]);
    }

    // API: Refresh URLs — re-resolve all linkable and route-based URLs
    public function refreshUrls(Request $request) {
        $menuId = $request->input('menu_id');
        $items = MenuItem::where('menu_id', $menuId)->get();
        
        // Known system route mappings (for converting old absolute URLs)
        $routeMap = [
            'home', 'frontend.intro.index', 'frontend.fields.index',
            'frontend.members.index', 'frontend.posts.index',
            'frontend.services.index', 'frontend.projects.index',
            'frontend.careers.index', 'products.index',
            'frontend.dealers.create', 'contact.show',
        ];
        
        $fixed = 0;
        foreach ($items as $item) {
            // Convert old absolute URLs to route: prefix
            if (!empty($item->url) && !str_starts_with($item->url, 'route:') && !str_starts_with($item->url, 'http') && !str_starts_with($item->url, '/')) {
                continue; // skip weird values
            }
            
            if (!empty($item->url) && str_starts_with($item->url, 'http')) {
                // Try to match against known routes
                foreach ($routeMap as $routeName) {
                    try {
                        $path = parse_url(route($routeName), PHP_URL_PATH);
                        $itemPath = parse_url($item->url, PHP_URL_PATH);
                        if ($path === $itemPath) {
                            $item->url = 'route:' . $routeName;
                            $item->save();
                            $fixed++;
                            break;
                        }
                    } catch (\Exception $e) {
                        continue;
                    }
                }
            }
        }
        
        $this->clearMenuCache();
        
        // Return updated HTML
        $newItems = MenuItem::where('menu_id', $menuId)
                    ->whereNull('parent_id')
                    ->orderBy('order')
                    ->with('children')
                    ->get();
        $html = '';
        foreach ($newItems as $i) {
            $html .= view('admin.menus.partials.menu-item', ['item' => $i])->render();
        }

        return response()->json([
            'status' => 'success',
            'fixed' => $fixed,
            'html' => $html
        ]);
    }

    // API: Cập nhật Item (Sửa tên)
    public function updateItem(Request $request, $id) {
        $item = MenuItem::findOrFail($id);

        // Support locale-based title updates: { title_vi: '...', title_en: '...' }
        $locales = config('translatable.locales', ['vi', 'en']);
        $hasLocaleTitle = false;
        foreach ($locales as $locale) {
            if ($request->has('title_' . $locale)) {
                $hasLocaleTitle = true;
                $item->setTranslation('title', $locale, $request->input('title_' . $locale));
            }
        }

        if ($hasLocaleTitle) {
            $item->save();
        } else {
            // Fallback: single title string (sets for current locale)
            $item->setTranslation('title', app()->getLocale(), $request->title);
            $item->save();
        }

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