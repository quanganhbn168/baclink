<?php
namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Field;
use App\Models\FieldCategory;
use Illuminate\View\View;
class FieldController extends Controller
{
    public function index()
    {
        $field_categories = FieldCategory::where("status", 1)
            ->whereNull("parent_id")
            ->with(['fields' => function($q) {
                $q->where('status', 1);
            }])
            ->orderBy('order', 'asc')
            ->get();
            
        $pageTitle = "Lĩnh vực hoạt động";
        $breadcrumbs = []; // Root level
        
        return view("frontend.fields.index", compact("field_categories", "pageTitle", "breadcrumbs"));
    }
    public function byCategory(FieldCategory $fieldCategory): View
    {
        $pageTitle = $fieldCategory->name;
        $current_category = $fieldCategory;
        $childCategories = $fieldCategory->children()->where('status', 1)->get();        
        if ($childCategories->isNotEmpty()) {
            return view("frontend.fields.fieldByCate", [
                "field_categories" => $childCategories,
                "pageTitle" => $pageTitle,
                "current_category" => $current_category 
            ]);
        }        
        $fields = $fieldCategory->fields()->where('status', 1)->paginate(10);
        return view("frontend.fields.fieldList", compact("fields", "pageTitle", "current_category"));
    }
    public function detail(Field $field): View
    {
        $pageTitle = $field->name;
        $breadcrumbs = [];

        $currentCategory = $field->category;

        while ($currentCategory) {
            array_unshift($breadcrumbs, $currentCategory);
            
            $currentCategory = $currentCategory->parent;
        }

        // Fetch data for Sidebar (Trending Posts) and Related Section (using recent posts as related)
        $trendingPosts = \App\Models\Post::where('status', 1)->latest('updated_at')->take(5)->get();
        $relatedPosts = \App\Models\Post::where('status', 1)->latest()->take(3)->get();

        return view("frontend.fields.detail", compact("field", "pageTitle", "breadcrumbs", "trendingPosts", "relatedPosts"));
    }
}