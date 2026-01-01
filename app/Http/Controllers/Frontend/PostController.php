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
        // số bản ghi mỗi trang (mặc định 12, giới hạn an toàn 1..60)
        $perPage = max(1, min((int) $request->query('per_page', 12), 60));
        $posts = Post::query()
            ->where('status', 1)
            ->with(['category', 'images']) // nếu không cần có thể bỏ
            ->latest('created_at')             // = orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();               // giữ lại ?per_page=, ?page=...
        return view('frontend.post.index', compact('posts', 'perPage'));
    }
    public function detail(Post $post)
    {
        $post->load('category');
        $relatedPosts = Post::where('status', 1)
            ->where('post_category_id', $post->post_category_id)
            ->where('id', '!=', $post->id)
            ->latest()
            ->take(6)
            ->get();
        return view('frontend.post.detail', compact('post', 'relatedPosts'));
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
        $featuredPosts = Post::where('status', 1)
        ->latest('updated_at')
        ->limit(5)
        ->get();
        return view('frontend.post.postByCate', [
            'category' => $postCategory,
            'posts' => $posts,
            'allCategories' => $allCategories,
            'featuredPosts' => $featuredPosts,
        ]);
    }
}
