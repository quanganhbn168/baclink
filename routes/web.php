<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\Frontend\IntroController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PostCategoryController;
use App\Http\Controllers\Frontend\PostController;
use App\Http\Controllers\Frontend\ProductController;
use App\Http\Controllers\Frontend\ProjectController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\Frontend\ServiceController;
use App\Http\Controllers\Frontend\FieldController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\Frontend\SlugController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\PageContentController;
use App\Http\Controllers\Frontend\CareerController;
use Illuminate\Support\Facades\Mail;
use App\Mail\CustomerWelcomeEmail;
use App\Http\Controllers\MediaLibraryController;
use App\Http\Controllers\CrawlController;
use App\Http\Controllers\Frontend\DealerRegistrationController;


Route::get('/crawl', [CrawlController::class, 'index'])->name('crawl.index');
Route::post('/crawl/run', [CrawlController::class, 'runFullCrawl'])->name('crawl.run');

Route::middleware(['web'])->group(function () {
    Route::get('/media-lib', [MediaLibraryController::class, 'index'])->name('media.lib.index');
    Route::post('/media-lib/upload', [MediaLibraryController::class, 'upload'])->name('media.lib.upload');
    Route::post('/media-lib/folder', [MediaLibraryController::class, 'createFolder'])->name('media.lib.folder');
    Route::post('/media-lib/move', [MediaLibraryController::class, 'move'])->name('media.lib.move');
    Route::get('/media-lib/folders', [MediaLibraryController::class, 'getAllFolders'])->name('media.lib.all_folders');
    Route::post('/media-lib/sync', [MediaLibraryController::class, 'sync'])->name('media.lib.sync');
    Route::delete('/media-lib/delete', [MediaLibraryController::class, 'destroy'])->name('media.lib.delete');
});
Route::any('/ckfinder/connector', [\CKSource\CKFinderBridge\Controller\CKFinderController::class,'requestAction'])
    ->name('ckfinder_connector');
Route::any('/ckfinder/browser', [\CKSource\CKFinderBridge\Controller\CKFinderController::class,'browserAction'])->name('ckfinder_browser');
Route::get("/", [HomeController::class,"index"])->name("home");

// Sitemap
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

Route::group(['prefix'=>'san-pham'], function(){
    Route::get('/', [ProductController::class, 'index'])->name('products.index');
    Route::get('/category/{category:slug}', [ProductController::class, 'byCategory'])->name('products.byCategory');
    Route::get('/{product:slug}', [ProductController::class, 'show'])->name('frontend.product.show');
});

Route::get('/tin-tuc', [PostController::class, 'index'])->name('frontend.posts.index');
Route::get('/tin-tuc/{post:slug}', [PostController::class, 'detail'])->name('frontend.posts.detail');
Route::get('/dich-vu', [ServiceController::class, 'index'])->name('frontend.services.index');
Route::get('/linh-vuc', [FieldController::class, 'index'])->name('frontend.fields.index');
Route::get('/du-an', [ProjectController::class, 'index'])->name('frontend.projects.index');

Route::get('/dang-ky-dai-ly', [DealerRegistrationController::class, 'create'])->name('frontend.dealers.create');
Route::post('/dang-ky-dai-ly', [DealerRegistrationController::class, 'store'])->name('frontend.dealers.store');

Route::get('/search', [HomeController::class, 'search'])->name('frontend.search');

Route::get('/gioi-thieu', [IntroController::class,'index'])->name('frontend.intro.index');
Route::get('/gioi-thieu/{intro:slug}', [IntroController::class,'getBySlug'])->name('frontend.intro.getBySlug');
Route::get('lien-he',[ContactController::class,'show'])->name('contact.show');
Route::post('lien-he',[ContactController::class,'store'])->name('contact.store');
Route::post('/newsletter/subscribe', [ContactController::class, 'subscribeEmail'])->name('newsletter.subscribe');
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/json', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::put('/update/{index}', [CartController::class, 'update'])->name('update');
    Route::delete('/remove/{index}', [CartController::class, 'remove'])->name('remove');
    Route::post('/clear', [CartController::class, 'clear'])->name('clear');
});
// routes/web.php
Route::post('/cart/clear-all', [CartController::class, 'clearAll'])->name('cart.clear-all');

Route::get('/cart', [CartController::class, 'showCartPage'])->name('cart.page');

Route::post('/cart/buy-now', [CartController::class, 'buyNow'])->name('cart.buy-now');
Route::post('/cart/merge', [CartController::class, 'merge']);
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'placeOrder'])->name('checkout.place');
Route::get('/order-success', [CheckoutController::class, 'success'])->name('checkout.success');


Route::get('/tuyen-dung', [CareerController::class, 'index'])->name('frontend.careers.index');
Route::get('/tuyen-dung/{career:slug}', [CareerController::class, 'show'])->name('frontend.careers.show');

Route::get('thank-you',function(){
    return view('page/thank-you');
})->name('thank-you');

Route::middleware(['auth:web'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::post('/profile', [UserController::class, 'updateProfile'])->name('profile.update');

    Route::get('/orders', [UserController::class, 'orderHistory'])->name('orders');
    Route::get('/orders/{orderId}', [UserController::class, 'orderDetail'])->name('order.detail');

    Route::get('/wishlist', [UserController::class, 'wishlist'])->name('wishlist');
});

Route::middleware(['auth'])->group(function () {
    Route::post('/page-content/update', [PageContentController::class, 'update'])->name('page-content.update');
});
// Route cho hành động thêm/xóa wishlist (có thể đặt ngoài group trên)
Route::post('/wishlist/add/{product}', [WishlistController::class, 'add'])->name('wishlist.add');
Route::post('/wishlist/remove/{product}', [WishlistController::class, 'remove'])->name('wishlist.remove');

require __DIR__.'/admin.php';
require __DIR__.'/auth.php';
Route::get('/fix-cache', function() {
    \Illuminate\Support\Facades\Cache::forget('header_menu_structure');
    \Illuminate\Support\Facades\Cache::forget('footer_menu_structure');
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    \Illuminate\Support\Facades\Artisan::call('view:clear');
    return "Cache cleared! <br>Header & Footer Menu Cache: Forgot. <br>System Cache: Cleared. <br>Config: Cleared. <br>View: Cleared. <br><a href='/'>Go Home</a>";
});

Route::get('/{slug}', [SlugController::class, 'handle'])->where('slug', '.*')->name('frontend.slug.handle');
