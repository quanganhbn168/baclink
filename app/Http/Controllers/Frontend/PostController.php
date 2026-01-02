<?php
namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Services\PostService;
use App\Models\PostCategory;
class PostController extends Controller
{
    public function __construct(
        protected PostService $postService
    ) {}
    public function index(Request $request)
    {
        // Fetch only top-level categories or categories chosen to be displayed on home/news index
        $categories = PostCategory::where('status', 1)
            ->where(function($query) {
                $query->whereNull('parent_id')->orWhere('parent_id', 0);
            })
            ->with(['posts' => function($query) {
                $query->where('status', 1)->latest()->take(4);
            }])
            ->get()
            ->filter(function($cat) {
                return $cat->posts->count() > 0;
            });
            
        $trendingPosts = Post::where('status', 1)
            ->latest('updated_at')
            ->take(5)
            ->get();

        return view('frontend.post.index', compact('categories', 'trendingPosts'));
    }
    public function detail(Post $post)
    {
        $post->load('category');
        $relatedPosts = Post::where('status', 1)
            ->where('post_category_id', $post->post_category_id)
            ->where('id', '!=', $post->id)
            ->latest()
            ->take(3)
            ->get();
            
        $trendingPosts = Post::where('status', 1)
            ->where('id', '!=', $post->id)
            ->latest('updated_at')
            ->take(5)
            ->get();

        return view('frontend.post.detail', compact('post', 'relatedPosts', 'trendingPosts'));
    }
    public function postByCate(PostCategory $postCategory)
    {
        $allCategories = PostCategory::where('status', 1)->get();
        if ($postCategory->parent_id === 0) {
            $categoryIds = PostCategory::where('parent_id', $postCategory->id)->pluck('id')->toArray();
            $categoryIds[] = $postCategory->id;
        } else {
            $categoryIds = [$postCategory->id];
        }
        
        $posts = Post::whereIn('post_category_id', $categoryIds)
            ->where('status', 1)
            ->latest()
            ->paginate(10);

        $trendingPosts = Post::where('status', 1)
            ->latest('updated_at')
            ->take(5)
            ->get();

        return view('frontend.post.postByCate', [
            'category' => $postCategory,
            'posts' => $posts,
            'allCategories' => $allCategories,
            'trendingPosts' => $trendingPosts,
        ]);
    }
}
