<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Slide;
use App\Models\Category;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\PostCategory;
use App\Models\Post;
use App\Models\Intro;
use App\Models\Project;
use App\Models\Testimonial;
use App\Models\Team;
use App\Models\Product;
use App\Models\Branch;
use App\Models\FieldCategory;
use App\Models\Career;
use App\Models\ProjectCategory;
use App\Models\Partner;
use App\Models\Brand;
class HomeController extends Controller
{
    public function index()
    {
        $slides         = Slide::where("status",1)->get();
        $intro          = Intro::first() ?? new Intro(['title' => 'Baclink', 'description' => 'Giải pháp liên kết doanh nghiệp']);
        $homeProducts   = Product::where("status",1)->where("is_home",1)->get();
        $homeCategories = Category::where("status",1)->where("is_home",1)->get();
        $homeServices   = Service::where("status",1)->where("is_home",1)->get();
        $homeProjectCategories    = ProjectCategory::where("status",1)->where("is_home",1)->with(["projects" => function($query){
            $query->where("status", 1);
        }])->get();
        $homeFields = FieldCategory::whereNull("parent_id")
                           ->where("status", 1)
                           ->with(['fields' => function ($query) {
                               $query->where('status', 1);
                           }])
                           ->get();
        $homeProjects = $homeProjectCategories->pluck('projects')->flatten();
        $homePosts = Post::where('status', 1)
            ->where('is_home', 1)
            ->with('category')
            ->latest()
            ->take(10)
            ->get();
            
        $members = \App\Models\User::where('email', 'like', 'member%@baclink.test')
            ->with('dealerProfile')
            ->take(8)
            ->get();

        $industries = [
            ['name' => 'Điện, Điện tử', 'icon' => 'fa-bolt'],
            ['name' => 'CNTT, Kinh tế số', 'icon' => 'fa-laptop-code'],
            ['name' => 'Hóa chất, cao su, ...', 'icon' => 'fa-flask'],
            ['name' => 'Cơ khí, Chế tạo', 'icon' => 'fa-cog'],
            ['name' => 'Nông sản, thực phẩm', 'icon' => 'fa-leaf'],
            ['name' => 'Dệt may, da giày ...', 'icon' => 'fa-tshirt'],
            ['name' => 'Vật liệu xây dựng', 'icon' => 'fa-building'],
            ['name' => 'Thủ công mỹ nghệ', 'icon' => 'fa-palette'],
        ];

        $careers = Career::get();
        $brands = Brand::get();
        $testimonials   = Testimonial::where('status', 1)->latest('id')->get();

        $eventPhotos = [
            'https://images.unsplash.com/photo-1540575467063-178a50c2df87?auto=format&fit=crop&q=80&w=1200&h=800',
            'https://images.unsplash.com/photo-1505373877841-8d25f7d46678?auto=format&fit=crop&q=80&w=1200&h=800',
            'https://images.unsplash.com/photo-1511578314322-379afb476865?auto=format&fit=crop&q=80&w=1200&h=800',
            'https://images.unsplash.com/photo-1475721027785-f74dea327912?auto=format&fit=crop&q=80&w=1200&h=800',
            'https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?auto=format&fit=crop&q=80&w=1200&h=800',
        ];

        // Specific Category for "Fields of Activity" Section
        $activityCategory = $homeFields->firstWhere('slug', 'linh-vuc-hoat-dong') ?? $homeFields->first();

        return view('frontend.index', compact("slides","intro","homeProducts","homeCategories","homeServices","homeProjectCategories","homeFields","homeProjects","homePosts","careers","testimonials","brands", "members", "industries", "eventPhotos", "activityCategory"));
    }
    public function search(Request $request)
    {
        $keyword = trim($request->input('q'));

        if (empty($keyword)) {
            return redirect()->back();
        }

        // 1. Search Products
        $products = Product::where('status', 1)
            ->where(function ($query) use ($keyword) {
                $query->where('name', 'LIKE', "%{$keyword}%")
                      ->orWhere('description', 'LIKE', "%{$keyword}%");
            })
            ->latest()
            ->get(); // Get collection

        // 2. Search Posts (Title, Description OR Category Name)
        $posts = Post::where('posts.status', 1)
            ->leftJoin('post_categories', 'posts.post_category_id', '=', 'post_categories.id')
            ->where(function ($query) use ($keyword) {
                $query->where('posts.title', 'LIKE', "%{$keyword}%")
                      ->orWhere('posts.description', 'LIKE', "%{$keyword}%")
                      ->orWhere('post_categories.name', 'LIKE', "%{$keyword}%");
            })
            ->select('posts.*', 'posts.title as name') // Alias title as name
            ->with('category')
            ->latest('posts.created_at')
            ->get();

        // 3. Merge and Sort
        $mergedResults = $products->concat($posts)->sortByDesc('created_at');

        // 4. Manual Pagination
        $page = \Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1;
        $perPage = 12;
        $currentPageItems = $mergedResults->slice(($page - 1) * $perPage, $perPage)->values();

        $paginatedResults = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentPageItems,
            $mergedResults->count(),
            $perPage,
            $page,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        return view('frontend.search_result', [
            'results' => $paginatedResults,
            'keyword' => $keyword
        ]);
    }
}