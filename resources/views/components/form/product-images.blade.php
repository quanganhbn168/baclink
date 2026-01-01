@props([
    'name' => 'images',
    'images' => [],
    'limit' => 9,
])

@php
    $defaultImage = asset('images/setting/no-image.png');
    $initialImages = collect($images)
        ->sortBy(fn($img) => $img['is_main'] ? -1 : ($img['order'] ?? 999))
        ->values()
        ->all();
@endphp

     <label for="">Hình ảnh chính</label>
<div class="image-grid-shopee-static"
     x-data="{}"
     x-init="$store.images.init('{{ $name }}', {{ $limit }}, @json($initialImages))">
    <!-- Main Image -->
    <div class="image-container main-image-static"
         @dragover.prevent
         @drop="$store.images.handleDrop($event, 0)"
         :class="{ 'dashed-border': !$store.images.hasImage(0), 'solid-border': $store.images.hasImage(0) }">
        <span class="main-label">Hình ảnh chính</span>
        <div class="image-content"
             :style="`background-image: url('${$store.images.getImageUrl(0)}');`"
             @click="$refs.mainImageInput.click()">
            <template x-if="!$store.images.hasImage(0)">
                <div class="upload-placeholder">
                    <i class="fas fa-upload"></i>
                    <span>Thêm ảnh</span>
                </div>
            </template>
        </div>
        <button type="button"
                class="remove-image-btn"
                x-show="$store.images.hasImage(0)"
                @click="$store.images.removeImage(0)">
            <i class="fas fa-trash"></i>
        </button>
        <input type="file"
               x-ref="mainImageInput"
               class="hidden-input"
               accept="image/*"
               multiple
               @change="$store.images.handleFiles($event, 0)">
    </div>

    <!-- Sub Images -->
    <div class="sub-images-static">
        <div class="sub-image-grid" id="sub-images-grid">
            @for ($index = 1; $index < $limit; $index++)
                <template x-if="$store.images.hasImage({{ $index }})">
                    <div class="image-container small-image-static"
                         x-data="{ isHovered: false }"
                         @mouseover="isHovered = true"
                         @mouseleave="isHovered = false"
                         data-index="{{ $index }}"
                         :class="{ 'solid-border': true }">
                        <div class="image-content"
                             :style="`background-image: url('${$store.images.getImageUrl({{ $index }})}');`"></div>
                        <button type="button"
                                class="remove-image-btn"
                                x-show="isHovered"
                                @click="$store.images.removeImage({{ $index }})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </template>

                <template x-if="!$store.images.hasImage({{ $index }}) && {{ $index }} === $store.images.allImages.length && $store.images.canAddMore">
                    <div class="image-container small-image-static upload-area"
                         @dragover.prevent
                         @drop="$store.images.handleDrop($event, {{ $index }})"
                         @click="$refs.subImageInput.click()"
                         :class="{ 'dashed-border': true }">
                        <div class="upload-placeholder">
                            <i class="fas fa-upload"></i>
                            <span>Thêm ảnh</span>
                        </div>
                    </div>
                </template>

                <template x-if="!$store.images.hasImage({{ $index }}) && !({{ $index }} === $store.images.allImages.length && $store.images.canAddMore)">
                    <div class="image-container small-image-static dashed-border"></div>
                </template>
            @endfor
        </div>

        <input type="file"
               x-ref="subImageInput"
               class="hidden-input"
               accept="image/*"
               multiple
               @change="$store.images.handleFiles($event)">
    </div>
</div>
@push('css')
<style>
.image-grid-shopee-static {
    display: flex;
    gap: 10px;
}

.image-container {
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
}

.main-image-static {
    width: 300px;
    height: 300px;
    background-color: #f5f5f5;
    border-radius: 6px;
}

.main-image-static.dashed-border {
    border: 1px dashed #ddd;
}

.main-image-static.solid-border {
    border: 1px solid #ddd;
}

.image-content {
    width: 100%;
    height: 100%;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}

.main-label {
    position: absolute;
    top: 8px;
    left: 8px;
    background: #ff5722;
    color: #fff;
    font-size: 12px;
    padding: 2px 6px;
    border-radius: 4px;
    z-index: 10;
}

.upload-placeholder {
    color: #888;
    text-align: center;
}

.upload-placeholder i {
    font-size: 40px;
    display: block;
    margin-bottom: 8px;
}

.hidden-input {
    display: none;
}

.remove-image-btn {
    position: absolute;
    top: 3px;
    right: 3px;
    background-color: rgba(0, 0, 0, 0.6);
    color: #fff;
    border: none;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 12px;
    z-index: 10;
}

.sub-images-static {
    flex: 1;
}

.sub-image-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 10px;
}

.small-image-static {
    background-color: #f5f5f5;
    border-radius: 6px;
    cursor: move;
    height: 145px;
}

.small-image-static.dashed-border {
    border: 1px dashed #ddd;
}

.small-image-static.solid-border {
    border: 1px solid #ddd;
}

.small-image-static.upload-area .upload-placeholder i {
    font-size: 30px;
    display: block;
    margin-bottom: 5px;
}
</style>
@endpush
@push('js')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.store('images', {
        name: '',
        limit: 9,
        allImages: [],
        deletedOldImageIds: [],

        init(name, limit, initialImages = []) {
            this.name = name;
            this.limit = limit;

            this.allImages = initialImages.map(img => ({
                type: 'old',
                id: img.id,
                url: img.image
            }));

            this.initSortable();
        },

        initSortable() {
            const grid = document.getElementById('sub-images-grid');
            if (grid) {
                Sortable.create(grid, {
                    animation: 150,
                    handle: '.small-image-static',
                    draggable: '.small-image-static',
                    onEnd: (evt) => {
                        const oldIndex = evt.oldIndex + 1; // +1 vì ảnh phụ bắt đầu từ index = 1
                        const newIndex = evt.newIndex + 1;


                        const [moved] = this.allImages.splice(oldIndex, 1);
                        this.allImages.splice(newIndex, 0, moved);

                        this.resetMainFlag();

                    }
                });
            }

            const mainImageArea = document.querySelector('.main-image-static');
            if (mainImageArea) {
                Sortable.create(mainImageArea, {
                    group: { name: 'shared', pull: 'clone', put: true },
                    sort: false,
                    draggable: false,
                    onAdd: (evt) => {
                        const draggedItem = evt.item;
                        const fromIndex = parseInt(draggedItem.dataset.index);


                        if (!isNaN(fromIndex)) {
                            const [moved] = this.allImages.splice(fromIndex, 1);
                            this.allImages.unshift(moved);
                            this.resetMainFlag();
                        }

                        draggedItem.remove();
                    }
                });
            }
        },
        resetMainFlag() {
        	this.allImages.forEach((img, i) => {
        		img.is_main = i === 0;
        	});
        },
        handleFiles(event, insertIndex = null) {
            const files = Array.from(event.target.files);
            event.target.value = '';
            this.processFiles(files, insertIndex);
        },

        handleDrop(event, insertIndex = null) {
            event.preventDefault();
            const files = Array.from(event.dataTransfer.files);
            this.processFiles(files, insertIndex);
        },

        processFiles(files, insertIndex = null) {
            const maxSize = 5 * 1024 * 1024;
            const allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

            const validFiles = files.filter(file => {
                if (!allowedTypes.includes(file.type)) {
                    showToast(`File ${file.name} không hợp lệ!`, 'error');
                    return false;
                }
                if (file.size > maxSize) {
                    showToast(`File ${file.name} vượt quá 5MB!`, 'error');
                    return false;
                }
                return true;
            });

            const available = this.limit - this.allImages.length;
            if (available <= 0) {
                showToast('Đã đạt giới hạn ảnh!', 'error');
                return;
            }
            if (validFiles.length > available) {
                validFiles.splice(available);
                showToast(`Chỉ được thêm tối đa ${available} ảnh`, 'error');
            }

            validFiles.forEach((file, i) => {
                const url = URL.createObjectURL(file);
                const newImage = {
                    type: 'new',
                    file: file,
                    url: url
                };

                if (insertIndex === null) {
                    this.allImages.push(newImage);
                } else {
                    this.allImages.splice(insertIndex + i, 0, newImage);
                }
            });

        },

        removeImage(index) {
            const image = this.allImages[index];
            if (!image) return;

            if (image.type === 'old' && image.id) {
                this.deletedOldImageIds.push(image.id);
            }

            if (image.type === 'new' && image.url) {
                URL.revokeObjectURL(image.url);
            }

            this.allImages.splice(index, 1);
        },

        getImageUrl(index) {
            return this.allImages[index]?.url ?? '{{ $defaultImage }}';
        },

        hasImage(index) {
            return !!this.allImages[index];
        },

        get canAddMore() {
            return this.allImages.length < this.limit;
        }
    });
});

function showToast(message, type = 'success') {
    Toastify({
        text: message,
        duration: 3000,
        gravity: 'top',
        position: 'right',
        backgroundColor: type === 'success' ? '#4caf50' : '#f44336',
    }).showToast();
}
</script>
@endpush
