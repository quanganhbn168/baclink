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
            ->take(3)
            ->get();
            
        $members = \App\Models\User::where('email', 'like', 'member%@baclink.test')
            ->with('dealerProfile')
            ->take(5)
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

        return view('frontend.index', compact("slides","intro","homeProducts","homeCategories","homeServices","homeProjectCategories","homeFields","homeProjects","homePosts","careers","testimonials","brands", "members", "industries", "eventPhotos"));
    }
    public function search(Request $request)
    {
        $keyword = trim($request->input('q'));

        if (empty($keyword)) {
            return redirect()->back();
        }

        // Tìm kiếm và phân trang trực tiếp từ database
        $products = Product::where('status', 1)
            ->where(function ($query) use ($keyword) {
                $query->where('name', 'LIKE', "%{$keyword}%")
                      ->orWhere('description', 'LIKE', "%{$keyword}%");
            })
            ->latest() // Sắp xếp theo ngày tạo mới nhất (tùy chọn)
            ->paginate(10); // Lấy 10 sản phẩm mỗi trang

        return view('frontend.search_result', [
            'results' => $products,
            'keyword' => $keyword
        ]);
    }
}