<?php

namespace App\Http\Controllers;

use App\Models\Slug;
use App\Models\Product;
use App\Models\Service;
use App\Models\Project;
use App\Models\Post;
use App\Models\Category;
use App\Models\ServiceCategory;
use App\Models\PostCategory;
use App\Models\Image;
use App\Models\Branch;

class SlugController extends Controller
{
    public function resolve($slug)
    {   
        $slugModel = Slug::where('slug', $slug)->firstOrFail();
        $model = $slugModel->sluggable;

        return match (get_class($model)) {
            Product::class         => $this->detailProduct($model),
            Service::class         => $this->detailService($model),
            Project::class         => $this->detailProject($model),
            Post::class            => $this->detailPost($model),
            Category::class        => $this->productByCategory($model),
            ServiceCategory::class => $this->serviceByCategory($model),
            PostCategory::class    => $this->postByCategory($model),
            default                            => abort(404),
        };
    }

    protected function detailProduct($product)
    {
        $category = $product->category()->select('id', 'name', 'slug', 'parent_id')->firstOrFail();
        $relatedProducts = Product::whereNot("id",$product->id)->where("category_id",$category->id)->get();
        $posts = Post::where('status', 1)->inRandomOrder()->take(3)->get();

        $images = Image::where('item_type','product')->where('item_id',$product->id)->get();
        return view('frontend.products.detail', compact('product','category','images','relatedProducts','posts'));
    }

    protected function detailService($service)
    {   
        $category = $service->category;
        $services = Service::where('service_category_id', $category->id)->where('id', '!=', $service->id)->get();
        $relatedServices = $services->take(3);
        $serviceCategory = ServiceCategory::where("status", 1)->with("services")->get();
        $posts = Post::where("status",1)->inRandomOrder()->limit(5)->get();
        return view('frontend.services.detail', compact('service', 'category', 'services', 'relatedServices','serviceCategory','posts'));
    }

    protected function detailProject($project)
    {
        return view('frontend.project.detail', compact('project'));
    }

    protected function detailPost($post)
    {
        $relatedPosts = Post::select('title','image','updated_at','slug')->where('id','<>',$post->id)->get();
        return view('frontend.post.detail', compact('post','relatedPosts'));
    }

    protected function productByCategory($category)
    {
        $products = $category->products()->where("status",1)->get();
        $branches = Branch::get();
        return view('frontend.products.productByCate', compact('category','products','branches'));
    }

    protected function serviceByCategory($category)
    {   
        $services =  Service::where("service_category_id",$category->id)->where("status",1)->get();
        $posts = Post::where("status",1)->inRandomOrder()->limit(5)->get();
        $serviceCategory = ServiceCategory::where("status", 1)->with("services")->get();
        return view('frontend.services.serviceByCate', compact('serviceCategory','category',"services","posts"));
    }

    protected function postByCategory($category)
    {
        $serviceCategory = ServiceCategory::where("status", 1)->with("services")->get();
        $postSidebar = Post::where("status",1)->inRandomOrder()->limit(5)->get();

    // Lấy danh sách bài viết thuộc các danh mục đã xác định
        $posts = Post::where('post_category_id', $category->id)
        ->where('status', 1)
        ->latest()
        ->paginate(6);

    // Các bài viết nổi bật: lấy 5 bài mới nhất (tùy chỉnh thêm điều kiện nếu cần)
        $featuredPosts = Post::where('status', 1)
        ->latest('updated_at')
        ->limit(5)
        ->get();

        return view('frontend.post.postByCate', [
            'category' => $category,
            'posts' => $posts,
            'postSidebar' => $postSidebar,
            'serviceCategory' => $serviceCategory,

        ]);
    }

    protected function banggia()
    {
        $postCategory = PostCategory::where("slug", "bang-gia")
            ->select("name", "slug", "description", "content")
            ->firstOrFail();

        $serviceCategory = ServiceCategory::where("status", 1)->with("services")->get();
        $posts = Post::where("status",1)->inRandomOrder()->limit(5)->get();
        return view('frontend.banggia', compact('postCategory', 'serviceCategory', 'posts'));
    }

}
