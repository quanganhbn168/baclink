<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use App\Models\Product;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    public function index()
    {
        // Cache sitemap trong 12 tiếng (720 phút)
        $content = Cache::remember('sitemap.xml', 720, function () {
            $sitemap = Sitemap::create();

            // 1. Static Pages
            $sitemap->add(Url::create(route('home'))->setPriority(1.0)->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY));
            // Add other static pages if needed (e.g., About, Contact)
            // $sitemap->add(Url::create('/gioi-thieu')->setPriority(0.8));

            // 2. Categories
            $categories = Category::where('status', 1)->get();
            foreach ($categories as $category) {
                $sitemap->add(Url::create(route('frontend.slug.handle', $category->slug))
                    ->setPriority(0.9)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY));
            }

            // 3. Products
            $products = Product::where('status', 1)->get();
            foreach ($products as $product) {
                $sitemap->add(Url::create(route('frontend.slug.handle', $product->slug ?? $product->id))
                    ->setLastModificationDate($product->updated_at)
                    ->setPriority(0.8)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY));
            }

            // 4. Posts (News)
            $posts = Post::where('status', 1)->get();
            foreach ($posts as $post) {
                 $sitemap->add(Url::create(route('frontend.slug.handle', $post->slug))
                    ->setLastModificationDate($post->updated_at)
                    ->setPriority(0.7)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY));
            }

            return $sitemap->render();
        });

        return response($content, 200, [
            'Content-Type' => 'text/xml'
        ]);
    }
}
