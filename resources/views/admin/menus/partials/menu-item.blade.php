{{-- resources/views/admin/menus/partials/menu-item.blade.php --}}

<li class="dd-item" data-id="{{ $item->id }}">
    
    {{-- Biến dd-handle thành container chứa nội dung --}}
    <div class="dd-handle">
        <div class="dd-content">
            {{-- 1. Bên Trái: Icon + Tiêu đề --}}
            <div class="d-flex align-items-center text-truncate" style="max-width: 70%;">
                <i class="fas fa-arrows-alt text-muted mr-2" style="font-size: 12px; cursor: move;"></i>
                <span class="font-weight-bold text-dark">{{ $item->title }}</span>
                @if($item->url)
                    <small class="text-muted ml-2 font-italic font-weight-normal d-none d-md-inline">({{ Str::limit($item->url, 30) }})</small>
                @endif
            </div>

            {{-- 2. Bên Phải: Badge + Actions --}}
            {{-- QUAN TRỌNG: Thêm class 'dd-nodrag' để khi bấm vào đây không bị tính là kéo thả --}}
            <div class="dd-nodrag d-flex align-items-center">
                
                {{-- Badge --}}
                @php
                    $badgeClass = 'badge-custom';
                    $typeLabel = 'Link';
                    if($item->linkable_type == 'App\Models\Page') { $badgeClass = 'badge-page'; $typeLabel = 'Trang'; }
                    elseif($item->linkable_type == 'App\Models\Category') { $badgeClass = 'badge-category'; $typeLabel = 'Danh mục'; }
                    elseif($item->linkable_type == 'App\Models\Intro') { $badgeClass = 'badge-intro'; $typeLabel = 'Giới thiệu'; }
                    elseif(!$item->linkable_type && $item->url) { $badgeClass = 'badge-system'; $typeLabel = 'Hệ thống'; }
                @endphp
                <span class="badge {{ $badgeClass }} mr-2 shadow-sm">{{ $typeLabel }}</span>

                {{-- Nút Sửa --}}
                <span class="btn-action-menu edit btn-edit-item" 
                        data-id="{{ $item->id }}"
                        data-title="{{ $item->title }}"
                        data-url="{{ route('admin.menus.items.update', $item->id) }}"
                        title="Sửa tên">
                    <i class="fas fa-pencil-alt"></i>
                </span>

                {{-- Nút Xóa --}}
                <span class="btn-action-menu delete btn-delete" data-id="{{ $item->id }}" title="Xóa">
                    <i class="fas fa-trash-alt"></i>
                </span>
            </div>
        </div>
    </div>

    @if($item->children->count() > 0)
        <ol class="dd-list">
            @foreach($item->children as $child)
                @include('admin.menus.partials.menu-item', ['item' => $child])
            @endforeach
        </ol>
    @endif
</li>
