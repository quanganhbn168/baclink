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
            ->latest()
            ->take(4)
            ->get();
        $careers = Career::get();
        $brands = Brand::get();
        $testimonials   = Testimonial::where('status', 1)->latest('id')->get();

        return view('frontend.index', compact("slides","intro","homeProducts","homeCategories","homeServices","homeProjectCategories","homeFields","homeProjects","homePosts","careers","testimonials","brands"));
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