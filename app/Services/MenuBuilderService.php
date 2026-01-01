<?php
namespace App\Services;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
class MenuBuilderService
{
    public function getHeaderMenu()
    {
        return Cache::rememberForever('header_menu_structure', function () {
            // Lấy Menu có location = 'top_nav'
            $menu = \App\Models\Menu::where('location', 'top_nav')->with(['items.children'])->first();
            
            if (!$menu) {
                // Fallback nếu chưa có trong DB thì lấy config cũ (để hệ thống không chết khi chưa setup)
                $menuConfig = config('menu_top', []);
                return $this->buildMenu($menuConfig);
            }

            return $this->buildMenuFromDB($menu->items);
        });
    }

    protected function buildMenuFromDB($items)
    {
        $builtMenu = [];
        
        foreach ($items as $item) {
            // 1. Chuẩn hóa dữ liệu Item
            $node = [
                'title' => $item->title,
                'url' => $item->link, // Sử dụng accessor getLinkAttribute trong Model
                'target' => $item->target,
            ];

            // 2. Xử lý Dynamic Root (Category/Intro ALL)
            if ($item->linkable_type && $item->linkable_id === null) {
                // Đây là Dynamic Root
                $config = [
                    'model' => $item->linkable_type,
                    'route_name' => 'frontend.slug.handle',
                ];

                // Nếu là Category thì map thêm config
                if ($item->linkable_type == \App\Models\Category::class) {
                   $config['parent_id'] = null; // Root categories
                }

                // Gọi hàm lấy con động
                $dynamicChildren = $this->fetchDynamicChildren($config);
                
                // Gán vào children của node hiện tại
                // Lưu ý: Nếu user muốn "Thay thế" item này bằng list con hay "Nhét" list con vào trong item này?
                // Thường là item này làm cha (VD: "Sản phẩm"), hover vào ra list con.
                $node['children'] = $dynamicChildren;
            } 
            else {
                // 3. Xử lý con đệ quy (Nếu có children trong DB)
                // Lưu ý: $item->children là collection từ DB
                if ($item->children && $item->children->count() > 0) {
                    $node['children'] = $this->buildMenuFromDB($item->children);
                }
            }

            $builtMenu[] = $node;
        }

        return $builtMenu;
    }

    // Giữ lại hàm cũ để fallback hoặc hỗ trợ config
    protected function buildMenu(array $menuItems)
    {
        $builtMenu = [];
        foreach ($menuItems as $item) {
            if (isset($item['route']) && Route::has($item['route'])) {
                $item['url'] = route($item['route']);
            } else {
                $item['url'] = url($item['url'] ?? '#');
            }
            if (isset($item['dynamic_children'])) {
                $item['children'] = array_merge($item['children'] ?? [], $this->fetchDynamicChildren($item['dynamic_children']));
            }
            if (!empty($item['children'])) {
                $item['children'] = $this->buildMenu($item['children']);
            }
            $builtMenu[] = $item;
        }
        return $builtMenu;
    }
    protected function fetchDynamicChildren(array $config)
    {
        $modelClass = $config['model'];
        if (!class_exists($modelClass)) {
            return [];
        }
        $children = collect();
        // Logic Sắp xếp: Mặc định là position asc
        $orderBy = $config['order_by'] ?? 'position';
        $orderDir = $config['order_dir'] ?? 'asc';
        if (isset($config['method']) && method_exists($modelClass, $config['method'])) {
            $method = $config['method'];
            $children = $modelClass::$method();
        } else {
            $standaloneModels = [
                \App\Models\Project::class,
            ];
            if (in_array($modelClass, $standaloneModels)) {
                $children = $modelClass::where('status', 1)->where('is_menu', 1)
                    ->orderBy($orderBy, $orderDir) // Sắp xếp ở đây
                    ->get();
            } else {
                $columnName = 'parent_id';
                $modelColumnMap = [
                    \App\Models\Product::class => 'category_id',
                    \App\Models\Service::class => 'service_category_id',
                ];
                if (array_key_exists($modelClass, $modelColumnMap)) {
                    $columnName = $modelColumnMap[$modelClass];
                }
                $parentId = $config['parent_id'] ?? null;
                
                $children = $modelClass::where($columnName, $parentId)
                    ->where('status', 1)->where('is_menu', 1)
                    ->orderBy($orderBy, $orderDir) // Và sắp xếp ở đây
                    ->get();
            }
        }
        $routeName = $config['route_name'] ?? null;
        return $children->map(function ($child) use ($modelClass, $routeName, $config, $orderBy, $orderDir) {
            $url = '#';
            if ($routeName && Route::has($routeName) && isset($child->slug)) {
                $url = route($routeName, $child->slug);
            } elseif (isset($child->slug)) {
                $url = url($child->slug);
            }
            $grandchildren = [];
            $hierarchicalModels = [
                \App\Models\Category::class,
                \App\Models\ServiceCategory::class,
                \App\Models\PostCategory::class,
                \App\Models\ProjectCategory::class,
            ];
            if (in_array($modelClass, $hierarchicalModels) && !isset($config['method'])) {
                $grandChildrenConfig = [
                    'model' => $modelClass,
                    'parent_id' => $child->id,
                    'route_name' => $routeName,
                    // Truyền cấu hình sắp xếp xuống cấp con
                    'order_by' => $orderBy,
                    'order_dir' => $orderDir
                ];
                $grandchildren = $this->fetchDynamicChildren($grandChildrenConfig);
            }
            return [
                'title' => $child->title ?? $child->name,
                'url' => $url,
                'children' => $grandchildren
            ];
        })->toArray();
    }
    public function getMenuFooter()
    {
        return Cache::rememberForever('footer_menu_structure', function () {
            $menuConfig = config('menu_footer', []);
            $builtMenu = [];
            foreach ($menuConfig as $column) {
                $builtItems = [];
                if (!empty($column['items'])) {
                    foreach ($column['items'] as $item) {
                        if (isset($item['route']) && Route::has($item['route'])) {
                            $item['url'] = route($item['route']);
                        } else {
                            $item['url'] = url($item['url'] ?? '#');
                        }
                        $builtItems[] = $item;
                    }
                }
                $column['items'] = $builtItems;
                $builtMenu[] = $column;
            }
            return $builtMenu;
        });
    }
}