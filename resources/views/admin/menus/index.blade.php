@extends('layouts.admin')

@section('title', 'Quản lý Menu')

@section('content')
<div class="container-fluid" x-data="menuManager">
    
    {{-- Header --}}
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <h5 class="m-0 font-weight-bold text-uppercase">
                            <i class="bi bi-menu-button-wide-fill mr-1"></i> 
                            Menu: <span class="text-primary">{{ $menu->name }}</span>
                        </h5>
                    </div>
                    
                    <form action="{{ route('admin.menus.index') }}" method="GET" class="d-flex align-items-center">
                        <select name="menu_id" class="form-control" onchange="this.form.submit()" style="min-width: 200px;">
                            @foreach($menus as $m)
                                <option value="{{ $m->id }}" {{ $menu->id == $m->id ? 'selected' : '' }}>
                                    {{ $m->name }} ({{ $m->location }})
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- TOOLS COLUMN --}}
        <div class="col-md-4">

            {{-- 1. SYSTEM LINKS --}}
            <div class="card mb-3 shadow-sm">
                <div class="card-header bg-light" data-toggle="collapse" data-target="#collapseSystem" style="cursor: pointer;">
                    <h3 class="card-title text-success font-weight-bold"><i class="fas fa-link mr-1"></i> Link Hệ Thống</h3>
                </div>
                <div class="collapse show" id="collapseSystem">
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            <template x-for="link in systemLinks" :key="link.route">
                                <li class="list-group-item d-flex justify-content-between align-items-center p-2">
                                    <span x-text="link.title"></span>
                                    <button class="btn btn-xs btn-outline-success" @click="addSystemLink(link)">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- 2. PAGES --}}
            <div class="card mb-3 shadow-sm">
                <div class="card-header bg-light" data-toggle="collapse" data-target="#collapsePages" style="cursor: pointer;">
                    <h3 class="card-title text-primary"><i class="fas fa-file-alt mr-1"></i> Trang (Pages)</h3>
                </div>
                <div class="collapse" id="collapsePages">
                    <div class="card-body" style="max-height: 250px; overflow-y: auto;">
                        @foreach($pages as $page)
                            <div class="custom-control custom-checkbox mb-1">
                                <input class="custom-control-input" type="checkbox" id="page_{{ $page->id }}" value="{{ $page->id }}" x-model="selectedPages">
                                <label for="page_{{ $page->id }}" class="custom-control-label">{{ $page->title }}</label>
                            </div>
                        @endforeach
                    </div>
                    <div class="card-footer bg-white text-right">
                        <button type="button" @click="addPages()" class="btn btn-sm btn-primary" :disabled="selectedPages.length === 0">
                            Thêm vào Menu
                        </button>
                    </div>
                </div>
            </div>

            {{-- 3. CATEGORIES --}}
            <div class="card mb-3 shadow-sm">
                <div class="card-header bg-light" data-toggle="collapse" data-target="#collapseCats" style="cursor: pointer;">
                    <h3 class="card-title text-info"><i class="fas fa-boxes mr-1"></i> Danh mục</h3>
                </div>
                <div class="collapse" id="collapseCats">
                    <div class="card-body" style="max-height: 250px; overflow-y: auto;">
                        <div class="custom-control custom-checkbox mb-2 border-bottom pb-2">
                            <input class="custom-control-input" type="checkbox" id="cat_all" x-model="isAllCategories">
                            <label for="cat_all" class="custom-control-label font-weight-bold text-primary">Tất cả danh mục (Tự động)</label>
                        </div>
                        <template x-if="!isAllCategories">
                            <div>
                                @foreach($categories as $cat)
                                    <div class="custom-control custom-checkbox mb-1">
                                        <input class="custom-control-input" type="checkbox" id="cat_{{ $cat->id }}" value="{{ $cat->id }}" x-model="selectedCategories">
                                        <label for="cat_{{ $cat->id }}" class="custom-control-label">{{ $cat->name }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </template>
                    </div>
                    <div class="card-footer bg-white text-right">
                        <button type="button" @click="addCategories()" class="btn btn-sm btn-info">Thêm</button>
                    </div>
                </div>
            </div>

             {{-- 4. VỀ CHÚNG TÔI (INTROS) --}}
             <div class="card mb-3 shadow-sm">
                <div class="card-header bg-light" data-toggle="collapse" data-target="#collapseIntros" style="cursor: pointer;">
                    <h3 class="card-title text-warning"><i class="fas fa-info-circle mr-1"></i> Về chúng tôi</h3>
                </div>
                <div class="collapse" id="collapseIntros">
                    <div class="card-body" style="max-height: 250px; overflow-y: auto;">
                        <div class="custom-control custom-checkbox mb-2 border-bottom pb-2">
                            <input class="custom-control-input" type="checkbox" id="intro_all" x-model="isAllIntros">
                            <label for="intro_all" class="custom-control-label font-weight-bold text-primary">Tất cả bài viết intro (Tự động)</label>
                        </div>
                        <template x-if="!isAllIntros">
                            <div>
                                @foreach($intros as $intro)
                                    <div class="custom-control custom-checkbox mb-1">
                                        <input class="custom-control-input" type="checkbox" id="intro_{{ $intro->id }}" value="{{ $intro->id }}" x-model="selectedIntros">
                                        <label for="intro_{{ $intro->id }}" class="custom-control-label">{{ $intro->title }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </template>
                    </div>
                    <div class="card-footer bg-white text-right">
                        <button type="button" @click="addIntros()" class="btn btn-sm btn-warning">Thêm</button>
                    </div>
                </div>
            </div>

            {{-- 5. CUSTOM LINK --}}
            <div class="card shadow-sm">
                <div class="card-header bg-light" data-toggle="collapse" data-target="#collapseCustom" style="cursor: pointer;">
                    <h3 class="card-title text-secondary"><i class="fas fa-link mr-1"></i> Custom Link</h3>
                </div>
                <div class="collapse" id="collapseCustom">
                    <div class="card-body">
                         <div class="form-group">
                            <label>Tiêu đề</label>
                            <input type="text" class="form-control" x-model="customLink.title" placeholder="Vd: Google">
                        </div>
                        <div class="form-group">
                            <label>URL</label>
                            <input type="text" class="form-control" x-model="customLink.url" placeholder="https://...">
                        </div>
                        <div class="form-group">
                            <label>Target</label>
                            <select class="form-control" x-model="customLink.target">
                                <option value="_self">Tab hiện tại</option>
                                <option value="_blank">Tab mới</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-footer bg-white text-right">
                        <button type="button" @click="addCustomLink()" class="btn btn-sm btn-secondary" :disabled="!customLink.title || !customLink.url">Thêm</button>
                    </div>
                </div>
            </div>

        </div>

        {{-- STRUCTURE COLUMN --}}
        <div class="col-md-8">
            <div class="card card-outline card-primary shadow-sm">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold">Cấu trúc hiển thị</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-success btn-sm" id="btn-save-order" style="display:none;">
                            <i class="fas fa-save mr-1"></i> Lưu vị trí
                        </button>
                    </div>
                </div>
                <div class="card-body bg-light">
                    <div class="dd" id="menu-nestable">
                        <ol class="dd-list">
                            @foreach($menuItems as $item)
                                @include('admin.menus.partials.menu-item', ['item' => $item])
                            @endforeach
                        </ol>
                        @if($menuItems->isEmpty())
                            <div class="alert alert-white border text-center text-muted p-4">
                                <p class="m-0">Menu trống.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nestable2/1.6.0/jquery.nestable.min.css" />
<style>
    /* Nestable Customization */
    .dd { max-width: 100%; }
    .dd-item { margin-bottom: 5px; }
    
    /* Handle (Item Content) Styling */
    .dd-handle { 
        height: auto; 
        min-height: 40px; 
        display: block; 
        margin: 0;
        padding: 8px 15px;
        background: #fff; 
        border: 1px solid #dee2e6; 
        border-radius: 4px; 
        box-shadow: 0 1px 2px rgba(0,0,0,0.05); /* Subtle shadow for card-like feel */
        transition: all 0.2s;
    }
    .dd-handle:hover {
        background: #f8f9fa;
        border-color: #ced4da;
    }

    /* Content Layout within Handle */
    .dd-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    /* Drag Placeholder */
    .dd-placeholder { 
        border: 2px dashed #007bff; 
        background: #eef5ff; 
        min-height: 40px; 
        margin-bottom: 5px; 
        border-radius: 4px;
    }

    /* Indentation Fix */
    .dd-list .dd-list { 
        padding-left: 30px; /* Standard indentation */
    }
    
    /* Collapse/Expand Buttons customization to look better */
    .dd-item > button { 
        font-family: "Font Awesome 5 Free"; 
        font-weight: 900;
        font-size: 14px;
        color: #6c757d;
        margin-left: 3px;
        margin-top: 8px; /* Align with content */
    }
    .dd-item > button[data-action="collapse"]:before { content: "\f068"; /* Minus icon */ }
    .dd-item > button[data-action="expand"]:before { content: "\f067"; /* Plus icon */ }

    /* Action Buttons */
    .btn-action-menu { cursor: pointer; color: #adb5bd; margin-left: 10px; font-size: 14px; transition: color 0.2s; }
    .btn-action-menu.edit:hover { color: #007bff; }
    .btn-action-menu.delete:hover { color: #dc3545; }
    
    /* Type Badges - AdminLTE Colors */
    .badge-system { background-color: #28a745; color: #fff; }
    .badge-page { background-color: #007bff; color: #fff; }
    .badge-category { background-color: #17a2b8; color: #fff; }
    .badge-intro { background-color: #ffc107; color: #1f2d3d; }
    .badge-custom { background-color: #6c757d; color: #fff; }
</style>
@endpush

@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/nestable2/1.6.0/jquery.nestable.min.js"></script>
<script>
    // Pass data to global variable to avoid parsing issues
    window.menuData = @json($menuData);

    document.addEventListener('alpine:init', () => {
        Alpine.data('menuManager', () => ({
            menuId: window.menuData.id,
            systemLinks: window.menuData.systemLinks || [],
            selectedPages: [],
            selectedCategories: [],
            isAllCategories: false,
            selectedIntros: [],
            isAllIntros: false,
            customLink: { title: '', url: '', target: '_self' },

            init() {
                // Initialize Nestable
                $('#menu-nestable').nestable({
                    maxDepth: 3,
                    group: 1
                }).on('change', () => {
                    this.saveOrder();
                });
            },

            addSystemLink(link) {
                this.postItem({ type: 'system', title: link.title, route: link.route });
            },

            addPages() {
                this.postItem({ type: 'page', ids: this.selectedPages });
            },

            addCategories() {
                this.postItem({ type: 'category', ids: this.selectedCategories, is_all: this.isAllCategories });
            },

            addIntros() {
                this.postItem({ type: 'intro', ids: this.selectedIntros, is_all: this.isAllIntros });
            },

            addCustomLink() {
                this.postItem({ type: 'custom', ...this.customLink });
            },

            postItem(data) {
                data.menu_id = this.menuId;
                data._token = '{{ csrf_token() }}';

                $.post("{{ route('admin.menus.store-item') }}", data)
                    .done(response => {
                        if(response.status === 'success') {
                            toastr.success('Đã thêm mục mới!');
                            // Update DOM directly
                            $('#menu-nestable > .dd-list').html(response.html);
                            // Re-init Nestable to bind new items
                             $('#menu-nestable').nestable('destroy');
                             $('#menu-nestable').nestable({
                                maxDepth: 3,
                                group: 1
                            }).on('change', () => {
                                this.saveOrder();
                            });
                        }
                    })
                    .fail(err => toastr.error('Lỗi khi thêm mục!'));
            },

            saveTimeout: null,

            saveOrder() {
                // Debounce 1s
                clearTimeout(this.saveTimeout);
                this.saveTimeout = setTimeout(() => {
                    var serializedData = $('#menu-nestable').nestable('serialize');
                    $.post("{{ route('admin.menus.update-order') }}", {
                        _token: '{{ csrf_token() }}',
                        menu: serializedData
                    }).done(() => {
                        console.log('Saved order'); 
                    });
                }, 1000);
            }
        }));
    });

    // Handle Delete via jQuery (delegated for dynamic items)
    $(document).on('click', '.btn-delete', function(e) {
        e.stopPropagation();
        if(!confirm('Bạn chắc chắn muốn xóa?')) return;
        
        var itemId = $(this).data('id');
        $.ajax({
            url: '/admin/menus/destroy-item/' + itemId,
            type: 'DELETE',
            data: { _token: '{{ csrf_token() }}' },
            success: function(res) {
                 toastr.success('Đã xóa!');
                 // Update DOM directly
                 $('#menu-nestable > .dd-list').html(res.html);
                 // Re-init Nestable
                 $('#menu-nestable').nestable('destroy');
                 $('#menu-nestable').nestable({
                    maxDepth: 3,
                    group: 1
                }).on('change', function() {
                    // Access Alpine component to call saveOrder
                    // But we can just duplicate logic or trigger event
                     var serializedData = $('#menu-nestable').nestable('serialize');
                     $.post("{{ route('admin.menus.update-order') }}", {
                        _token: '{{ csrf_token() }}',
                        menu: serializedData
                    });
                });
            }
        });
    });

    // Handle Edit via jQuery
    $(document).on('click', '.btn-edit-item', function(e) {
        e.stopPropagation();
        var itemId = $(this).data('id');
        var currentTitle = $(this).data('title');
        var url = $(this).data('url');

        Swal.fire({
            title: 'Sửa tên mục menu',
            input: 'text',
            inputValue: currentTitle,
            showCancelButton: true,
            confirmButtonText: 'Lưu',
            cancelButtonText: 'Hủy',
            inputValidator: (value) => {
                if (!value) {
                    return 'Bạn cần viết gì đó!'
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url, 
                    type: 'PUT',
                    data: { 
                        _token: '{{ csrf_token() }}',
                        title: result.value
                    },
                    success: function(res) {
                        toastr.success('Đã cập nhật tên!');
                        setTimeout(() => location.reload(), 300); 
                    },
                    error: function(err) {
                        toastr.error('Lỗi cập nhật!');
                    }
                });
            }
        })
    });
</script>
@endpush
