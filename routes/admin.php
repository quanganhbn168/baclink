<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SelectController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\PostCategoryController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\ProjectCategoryController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\GlobalBulkActionController;
use App\Http\Controllers\Admin\DealerApplicationController;
use App\Http\Controllers\Admin\AgentController;
use App\Http\Controllers\SlideController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Admin\IntroController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\ServiceCategoryController;
use App\Http\Controllers\Admin\ContentBlockController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\WarrantyController;
use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\AttributeValueController;
use App\Http\Controllers\Admin\DuplicateController;
use App\Http\Controllers\Admin\FieldCategoryController;
use App\Http\Controllers\Admin\FieldController as AdminFieldController;
use App\Http\Controllers\Admin\CareerController;
use App\Http\Controllers\Admin\SlugAjaxController;
use App\Http\Controllers\Admin\MenuController;

Route::middleware(['auth:admin'])->prefix('admin')->as('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::post('/toggle', [DashboardController::class, 'toggleField'])->name('toggle');
    Route::resource('intros', IntroController::class);
    Route::prefix('ajax')->name('ajax.')->group(function () {
        Route::get('slug/check', [SlugAjaxController::class, 'check'])->name('slug.check');
    });
    Route::post('global/bulk-action', [GlobalBulkActionController::class, 'handle'])
         ->name('global.bulk_action');
    Route::resource('categories', CategoryController::class);
    Route::get('products/data', [ProductController::class, 'data'])->name('products.data');
    Route::resource('products', ProductController::class);
    Route::post('products/validate-uniqueness', [ProductController::class, 'validateUniqueness'])->name('products.validate_uniqueness');
    Route::post('/ajax/attributes/{attribute}/values', [AttributeController::class, 'storeValue'])
    ->name('ajax.attributes.values.store');
    Route::resource('services', ServiceController::class);
    Route::resource('project-categories', ProjectCategoryController::class)
     ->names('project-categories')
     ->parameters(['project-categories' => 'project_category']);
    Route::resource('careers', CareerController::class);
    Route::resource('post-categories', PostCategoryController::class);
    Route::resource('posts', PostController::class);
    Route::resource('field-categories', FieldCategoryController::class);
    Route::resource('fields', AdminFieldController::class);
    Route::resource('slides', SlideController::class);
    Route::resource('attributes', AttributeController::class);
    Route::resource('attributes.values', AttributeValueController::class)->shallow()->except(['index', 'show']);
    Route::get('select/attributes', [SelectController::class, 'attributes'])->name('select.attributes');
    Route::get('select/categories-by-type', [SelectController::class, 'categoriesByType'])->name('select.categories-by-type');
    Route::post('/slides/{slide}/toggle-status', [SlideController::class, 'toggleStatus'])
      ->name('slides.toggle-status');
    Route::resource('projects', ProjectController::class);
    Route::post('/projects/{project}/duplicate',[ProjectController::class,'duplicate'])->name('projects.duplicate');
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
    Route::resource('orders', OrderController::class);
    Route::resource('service_categories', ServiceCategoryController::class);
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
    Route::resource('contacts', ContactController::class)->only(['index', 'destroy']);
    Route::resource('teams', TeamController::class);
    Route::resource('testimonials', TestimonialController::class);
    Route::resource('orders', OrderController::class)->only([
        'index', 'show', 'destroy'
    ]);
    Route::resource('content-blocks', ContentBlockController::class);
    Route::resource('pages', PageController::class);
    Route::resource('brands', BrandController::class)->except('show');
    Route::match(['get', 'post'], 'ajax/brands', [BrandController::class, 'ajax'])->name('ajax.brands');
    Route::resource('tags', TagController::class)->except('show');
    Route::get('ajax/tags', [TagController::class, 'ajax'])->name('ajax.tags');
    Route::match(['get', 'post'], 'ajax/attributes', [AttributeController::class, 'ajax'])->name('ajax.attributes');
    Route::match(['get', 'post'], 'ajax/attribute-values', [AttributeValueController::class, 'ajax'])->name('ajax.attribute-values');
    Route::post('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::get('/ajax/products/check-code', [ProductController::class, 'checkCodeUniqueness'])->name('ajax.products.check_code');
    Route::resource('agents', AgentController::class);
    Route::post('agents/{id}/deposit', [AgentController::class, 'deposit'])->name('agents.deposit');
    
    // Route cập nhật chiết khấu (anh cũng sẽ cần cái này)
    Route::post('agents/{id}/discount', [AgentController::class, 'updateDiscount'])->name('agents.discount');
    // Resource route cho index, edit, update, destroy
    Route::resource('dealer-applications', DealerApplicationController::class)
     ->except(['create', 'store', 'show']);

    // Route riêng để đổi trạng thái (Duyệt/Hủy)
    Route::patch('dealer-applications/{id}/status', [DealerApplicationController::class, 'updateStatus'])
         ->name('dealer-applications.status');
    Route::resource('branches', BranchController::class);
    Route::get('/file-manager', [App\Http\Controllers\Admin\FileManagerController::class, 'index'])->name('file-manager.index');

    // --- QUẢN LÝ MENU (AlpineJS Refactor) ---
    Route::prefix('menus')->name('menus.')->group(function () {
        Route::get('/', [MenuController::class, 'index'])->name('index');
        
        // API Routes for Alpine
        Route::post('/store-item', [MenuController::class, 'storeItem'])->name('store-item');
        Route::post('/update-order', [MenuController::class, 'updateOrder'])->name('update-order');
        Route::delete('/destroy-item/{id}', [MenuController::class, 'destroyItem'])->name('destroy-item');
        
        // Legacy/Standard Resource support if needed (optional)
        Route::put('/{menu}', [MenuController::class, 'update'])->name('update'); 
        Route::delete('/{id}', [MenuController::class, 'destroy'])->name('destroy');
        Route::put('/items/{id}', [MenuController::class, 'updateItem'])->name('items.update');
    });
    Route::post('/duplicate', [DuplicateController::class, 'duplicate'])->name('duplicate');
});
