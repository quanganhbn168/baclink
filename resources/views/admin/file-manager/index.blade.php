@extends('layouts.admin')

@section('title', 'Quản lý File')

@section('content')

{{-- 1. Classification Stats Card (Top) --}}
<div class="card card-outline card-info collapsed-card shadow-sm mb-3">
    <div class="card-header py-2 pointer" data-card-widget="collapse" style="cursor: pointer">
        <h3 class="card-title text-info"><i class="fas fa-chart-pie mr-1"></i> Thống kê định dạng</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
        </div>
    </div>
    <div class="card-body p-2" style="display: none;">
        <div class="row">
            <div class="col-6 col-md-3 border-right">
                <div class="description-block">
                    <h5 class="description-header text-primary" id="cntImages">0</h5>
                    <span class="description-text">HÌNH ẢNH</span>
                </div>
            </div>
            <div class="col-6 col-md-3 border-right">
                <div class="description-block">
                    <h5 class="description-header text-danger" id="cntVideos">0</h5>
                    <span class="description-text">VIDEO</span>
                </div>
            </div>
            <div class="col-6 col-md-3 border-right">
                <div class="description-block">
                    <h5 class="description-header text-success" id="cntDocs">0</h5>
                    <span class="description-text">TÀI LIỆU</span>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="description-block">
                    <h5 class="description-header text-warning" id="cntOthers">0</h5>
                    <span class="description-text">KHÁC</span>
                </div>
            </div>
        </div>
        <div class="row border-top mt-2 pt-2 text-center">
            <div class="col-12">
                 <span class="text-muted">Tổng dung lượng: <strong id="totalSizeStorage" class="text-dark">0 MB</strong></span>
            </div>
        </div>
    </div>
</div>

{{-- 2. Main Media Manager --}}
<div class="container-fluid pl-0 pr-0" style="height: calc(100vh - 180px); overflow: hidden;">
    <div class="card card-primary card-outline h-100" style="display: flex; flex-direction: column;">
        <div class="card-header py-2">
            <h3 class="card-title">
                <i class="fas fa-folder-open mr-1"></i>
                Thư viện Media
            </h3>

            <div class="card-tools d-flex align-items-center">
                 <!-- Top Controls (Global) -->
                 <button class="btn btn-warning btn-xs mr-2" id="btnSync" title="Đồng bộ lại">
                    <i class="fas fa-sync"></i>
                </button>
            </div>
        </div>
        
        <div class="card-body p-0 d-flex flex-grow-1" style="overflow: hidden;">
            {{-- SIDEBAR: Folder Tree --}}
            <div class="border-right bg-light" style="width: 250px; min-width: 250px; display: flex; flex-direction: column;">
                <div class="p-2 border-bottom">
                     <button class="btn btn-info btn-block btn-sm" id="btnAddFolder">
                        <i class="fas fa-plus"></i> Tạo thư mục
                    </button>
                </div>
                <div class="p-2 overflow-auto flex-grow-1" id="folderTree">
                    <div class="text-center mt-3"><i class="fas fa-spinner fa-spin"></i> Loading...</div>
                    {{-- Tree Injected Here --}}
                </div>
            </div>

            {{-- MAIN CONTENT: Files --}}
            <div class="flex-grow-1 d-flex flex-column" style="min-width: 0;">
                
                {{-- Toolbar --}}
                {{-- Toolbar --}}
                 <div class="p-2 border-bottom bg-white">
                     <div class="d-flex align-items-center justify-content-between mb-2">
                         <div class="d-flex align-items-center flex-grow-1">
                             <div class="input-group input-group-sm mr-2" style="width: 250px;">
                                <input type="text" id="searchInput" class="form-control" placeholder="Tìm kiếm file...">
                                <div class="input-group-append">
                                    <button class="btn btn-default"><i class="fas fa-search"></i></button>
                                </div>
                            </div>
                            
                            {{-- Breadcrumbs --}}
                            <div id="currentPathDisplay" class="text-muted small ml-2 text-truncate d-none d-md-block" style="max-width: 300px;">
                                <i class="fas fa-home"></i> /
                            </div>
                         </div>

                        <div class="d-flex align-items-center">
                            {{-- Bulk Actions --}}
                            <div class="d-none mr-3" id="bulkActions">
                                <span class="mr-2 text-muted small"><strong id="selectedCount">0</strong> chọn</span>
                                <button class="btn btn-light btn-sm mr-1" id="btnBulkMove" title="Di chuyển"><i class="fas fa-share"></i></button>
                                <button class="btn btn-danger btn-sm" id="btnBulkDelete" title="Xóa"><i class="fas fa-trash"></i></button>
                            </div>
                            
                            {{-- Upload --}}
                            <button class="btn btn-success btn-sm" id="btnUploadToggle">
                                <i class="fas fa-upload"></i> Tải lên
                            </button>
                        </div>
                    </div>
                 </div>

                {{-- Grid area --}}
                <div id="gridContainer" class="p-3 overflow-auto flex-grow-1 text-center bg-white position-relative">
                    <div id="fileGrid" class="d-flex flex-wrap align-content-start text-left" style="gap: 15px;">
                         {{-- Items --}}
                    </div>
                    
                    {{-- Load More Button --}}
                    <div class="mt-4 mb-3 d-none" id="loadMoreContainer">
                        <button class="btn btn-default btn-sm" id="btnLoadMore">
                            Xem thêm <i class="fas fa-chevron-down ml-1"></i>
                        </button>
                    </div>

                     {{-- Loader --}}
                    <div id="mainLoader" class="position-absolute w-100 h-100 d-none" style="top:0; left:0; background: rgba(255,255,255,0.7); z-index: 5; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                    </div>
                </div>

                {{-- Footer controls? Maybe Selection All --}}
                <div class="card-footer p-2 bg-light border-top">
                     <div class="icheck-primary d-inline ml-2">
                        <input type="checkbox" id="checkAll">
                        <label for="checkAll" class="small" style="font-weight: normal;">Chọn tất cả</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<template id="itemTemplate">
    <div class="media-item position-relative border rounded shadow-sm bg-white" style="width: 120px; height: 140px; overflow: hidden; cursor: pointer; transition: all 0.2s;">
        <div class="media-thumb d-flex align-items-center justify-content-center bg-light" style="height: 100px;">
            <img src="" class="img-fluid" style="max-height: 100%; max-width: 100%; object-fit: contain;">
            <i class="far fa-file fa-3x text-secondary d-none icon-fallback"></i>
        </div>
        <div class="media-name px-1 text-center text-truncate small border-top bg-white" style="height: 40px; line-height: 40px;">
            Filename
        </div>
        <div class="selection-badge">
            <i class="fas fa-check"></i>
        </div>
    </div>
</template>
<div class="modal fade" id="uploadModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tải lên file</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center p-5 dashed-border" style="border: 2px dashed #ddd; background: #f9f9f9;">
                <form id="uploadFormModal" enctype="multipart/form-data">
                    <i class="fas fa-cloud-upload-alt fa-4x text-muted mb-3"></i>
                    <p class="mb-4">Kéo thả file vào đây hoặc bấm nút bên dưới</p>
                    
                    <div class="form-group">
                         <input type="file" id="fileInputModal" name="files[]" multiple class="d-none" onchange="document.getElementById('uploadFileCount').textContent = this.files.length + ' file đã chọn'">
                         <button type="button" class="btn btn-primary btn-lg" onclick="document.getElementById('fileInputModal').click()">
                             <i class="fas fa-folder-open"></i> Chọn file từ máy tính
                         </button>
                    </div>
                    <div class="mt-2 text-success font-weight-bold" id="uploadFileCount"></div>
                    
                    <div class="mt-4">
                        <button type="submit" class="btn btn-success px-4"><i class="fas fa-upload"></i> Tải lên ngay</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // --- State ---
        let state = {
            currentFolderId: null,
            currentPage: 1,
            lastPage: 1,
            selectedItems: new Set(),
            allItems: [],
            folders: [], // Full flat list of folders
            isTreeViewInitialized: false
        };

        // --- Elements ---
        const ui = {
            tree: document.getElementById('folderTree'),
            grid: document.getElementById('fileGrid'),
            gridContainer: document.getElementById('gridContainer'),
            loadMoreBtn: document.getElementById('btnLoadMore'),
            loadMoreContainer: document.getElementById('loadMoreContainer'),
            pathDisplay: document.getElementById('currentPathDisplay'),
            loader: document.getElementById('mainLoader'),
            checkAll: document.getElementById('checkAll'),
            
            // Toolbar
            search: document.getElementById('searchInput'),
            bulkActions: document.getElementById('bulkActions'),
            selectedCount: document.getElementById('selectedCount'),
            
            // Upload
            btnUpload: document.getElementById('btnUploadToggle'),
            // Removed old uploadArea/uploadForm references
            
            // Actions
            btnAddFolder: document.getElementById('btnAddFolder'),
            btnDelete: document.getElementById('btnBulkDelete'),
            btnMove: document.getElementById('btnBulkMove'),
            btnSync: document.getElementById('btnSync'),
        };

        // --- Init ---
        init();

        function init() {
            loadFolders().then(() => {
                renderFolderTree();
                loadFiles(null, 1); // Load Root
            });
            setupListeners();
            setupDragAndDrop();
        }

        // --- Logic: Folders ---
        function loadFolders() {
             return $.get('{{ route('media.lib.all_folders') }}').then(data => {
                 state.folders = data;
                 return data;
             });
        }
        
        function renderFolderTree() {
            ui.tree.innerHTML = '';
            
            // Build Tree Structure
            const rootUl = document.createElement('ul');
            rootUl.className = 'nav nav-pills nav-sidebar flex-column';
            rootUl.style.width = '100%';
            
            // Add "Root" item
            const rootLi = createFolderEl(null, 'Home (Root)');
            rootLi.classList.add('nav-item');
            rootUl.appendChild(rootLi);

            // Recursive Builder
            const buildLevel = (parentId, container) => {
                const children = state.folders.filter(f => f.parent_id == parentId);
                if (children.length === 0) return;
                
                const ul = document.createElement('ul');
                ul.className = 'nav nav-treeview pl-3';
                ul.style.display = 'block'; // Always expanded for now, or toggle?
                
                children.forEach(folder => {
                    const li = createFolderEl(folder.id, folder.name);
                    li.classList.add('nav-item');
                    ul.appendChild(li);
                    buildLevel(folder.id, li);
                });
                container.appendChild(ul);
            };

            buildLevel(null, rootUl); // Start from root
            ui.tree.appendChild(rootUl);
        }
        
        function createFolderEl(id, name) {
            const li = document.createElement('li');
            const a = document.createElement('a');
            a.href = '#';
            a.className = 'nav-link py-1';
            a.dataset.id = id || '';
            
            if (state.currentFolderId == id) a.classList.add('active');
            
            a.innerHTML = `<i class="nav-icon fas fa-folder text-warning"></i> <p class="d-inline m-0">${name}</p>`;
            
            a.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                
                // Set Active Class
                ui.tree.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
                a.classList.add('active');
                
                state.currentFolderId = id;
                loadFiles(id, 1);
            });
            
            li.appendChild(a);
            return li;
        }

        // --- Logic: Files ---
        function loadFiles(folderId, page, append = false) {
            if (!append) {
                ui.loader.classList.remove('d-none');
                state.allItems = [];
                state.selectedItems.clear();
                updateSelectionUI();
                ui.checkAll.checked = false;
            } else {
                 ui.loadMoreBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang tải...';
            }

            const params = { page: page, folder_id: folderId, s: ui.search.value };
            
            $.get('{{ route('media.lib.index') }}', params).done(res => {
                state.currentPage = res.current_page;
                state.lastPage = res.last_page;
                
                const newFiles = res.files || [];
                
                if(!append) {
                    ui.grid.innerHTML = '';
                    updateBreadcrumb();
                    
                    // Update Stats Breakdown
                    if(res.stats) {
                        document.getElementById('cntImages').textContent = res.stats.images;
                        document.getElementById('cntVideos').textContent = res.stats.videos;
                        document.getElementById('cntDocs').textContent = res.stats.documents;
                        document.getElementById('cntOthers').textContent = res.stats.others;
                        document.getElementById('totalSizeStorage').textContent = formatBytes(res.stats.total_size);
                    }
                }
                
                state.allItems = append ? [...state.allItems, ...newFiles] : newFiles;
                
                if (state.allItems.length === 0 && !append) {
                     ui.grid.innerHTML = '<div class="text-muted w-100 text-center mt-5">Không có file nào</div>';
                }
                
                renderGridItems(newFiles);
                
                // Pagination Logic
                if (state.currentPage < state.lastPage) {
                    ui.loadMoreContainer.classList.remove('d-none');
                    ui.loadMoreBtn.innerHTML = 'Xem thêm <i class="fas fa-chevron-down ml-1"></i>';
                } else {
                    ui.loadMoreContainer.classList.add('d-none');
                }
                
            }).always(() => {
                 ui.loader.classList.add('d-none');
                 if(append) ui.loadMoreBtn.innerHTML = 'Xem thêm <i class="fas fa-chevron-down ml-1"></i>';
            });
        }
        
        function formatBytes(bytes, decimals = 2) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const dm = decimals < 0 ? 0 : decimals;
            const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
        }
        
        let lastSelectedIndex = null; // Track last clicked for Shift

        function renderGridItems(items) {
             const tpl = document.getElementById('itemTemplate');
            
             items.forEach((item, index) => {
                 const clone = tpl.content.cloneNode(true);
                 const itemEl = clone.querySelector('.media-item');
                 const img = clone.querySelector('img');
                 const icon = clone.querySelector('.icon-fallback');
                 const name = clone.querySelector('.media-name');
                 
                 name.textContent = item.name;
                 name.title = item.name;
                 itemEl.dataset.id = item.id;
                 
                 if (item.mime_type && item.mime_type.startsWith('image/')) {
                     img.src = item.url;
                 } else {
                     img.style.display = 'none';
                     icon.classList.remove('d-none');
                 }
                 
                 // Selection Logic calling new handler
                 itemEl.addEventListener('click', (e) => handleItemClick(item, index, e));
                 itemEl.addEventListener('dblclick', () => {
                      if(item.type === 'folder') loadData(item.id);
                 });
                 
                 ui.grid.appendChild(clone);
             });
             updateSelectionUI(); // Ensure UI sync on load
        }

        function handleItemClick(item, index, e) {
            const key = `${item.id}_${item.type}`;
            
            // 1. Shift + Click (Range)
            // Ensure state.allItems exists and is populated
            if (e.shiftKey && lastSelectedIndex !== null && state.allItems.length > 0) {
                const start = Math.min(lastSelectedIndex, index);
                const end = Math.max(lastSelectedIndex, index);
                
                if(!e.ctrlKey) state.selectedItems.clear(); 
                
                for(let i = start; i <= end; i++) {
                    const it = state.allItems[i];
                    if(it) state.selectedItems.add(`${it.id}_${it.type}`);
                }
            } 
            // 2. Ctrl + Click (Toggle)
            else if (e.ctrlKey || e.metaKey) {
                if (state.selectedItems.has(key)) state.selectedItems.delete(key);
                else {
                    state.selectedItems.add(key);
                    lastSelectedIndex = index;
                }
            } 
            // 3. Simple Click
            else {
                state.selectedItems.clear();
                state.selectedItems.add(key);
                lastSelectedIndex = index;
            }
            
            updateSelectionUI();
        }
        
        function updateBreadcrumb() {
             // Simple text based on current folder logic
             if (!state.currentFolderId) {
                 ui.pathDisplay.innerHTML = '<i class="fas fa-home"></i> / Home';
                 return;
             }
             // Find name in folders list
             const folder = state.folders.find(f => f.id == state.currentFolderId);
             ui.pathDisplay.innerHTML = `<i class="fas fa-folder-open"></i> / ${folder ? folder.name : 'Unknown'}`;
        }

        function updateSelectionUI() {
             const els = ui.grid.querySelectorAll('.media-item');
             els.forEach(el => {
                 const id = el.dataset.id;
                 // Grid items are files, so we check for id_file
                 // If the item in grid is a folder (future), this logic covers id_file only?
                 // But handleItemClick adds id_type.
                 // In renderGridItems we only render files currently.
                 // So type is file.
                 const key = `${id}_file`;
                 if (state.selectedItems.has(key)) el.classList.add('selected');
                 else el.classList.remove('selected');
             });
             
             ui.selectedCount.textContent = state.selectedItems.size;
             if(state.selectedItems.size > 0) ui.bulkActions.classList.remove('d-none');
             else ui.bulkActions.classList.add('d-none');
        }

        // --- Listeners ---
        function setupListeners() {
            // Load More
            ui.loadMoreBtn.addEventListener('click', () => {
                loadFiles(state.currentFolderId, state.currentPage + 1, true);
            });
            
            // Search
            let debounce;
            ui.search.addEventListener('input', (e) => {
                clearTimeout(debounce);
                debounce = setTimeout(() => {
                     loadFiles(state.currentFolderId, 1);
                }, 500);
            });
            
            // Check All
            ui.checkAll.addEventListener('change', (e) => {
                state.selectedItems.clear();
                if(e.target.checked) {
                    state.allItems.forEach(i => state.selectedItems.add(`${i.id}_${i.type}`));
                }
                updateSelectionUI();
            });
            
            // Upload Button -> Show Modal
            ui.btnUpload.addEventListener('click', () => {
                $('#uploadModal').modal('show');
            });
            
            // Upload Form Modal
            const uploadFormModal = document.getElementById('uploadFormModal');
            if(uploadFormModal) {
                 uploadFormModal.addEventListener('submit', (e) => {
                    e.preventDefault();
                    const fd = new FormData(uploadFormModal);
                    if(state.currentFolderId) fd.append('folder_id', state.currentFolderId);
                    
                    const btn = uploadFormModal.querySelector('button[type="submit"]');
                    const orgText = btn.innerHTML;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang tải lên...';
                    btn.disabled = true;

                    $.ajax({
                        url: '{{ route('media.lib.upload') }}',
                        type: 'POST',
                        data: fd, processData: false, contentType: false,
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        success: (res) => {
                             if(res.success) {
                                 uploadFormModal.reset();
                                 document.getElementById('uploadFileCount').textContent = '';
                                 $('#uploadModal').modal('hide');
                                 loadFiles(state.currentFolderId, 1);
                                 Toast.fire('Tải lên thành công', '', 'success');
                             }
                        },
                        error: () => Toast.fire('Lỗi tải lên', '', 'error'),
                        complete: () => {
                            btn.innerHTML = orgText;
                            btn.disabled = false;
                        }
                    });
                });
            }
            
            // Add Folder
            ui.btnAddFolder.addEventListener('click', () => {
                Swal.fire({
                    title: 'Tên thư mục mới',
                    input: 'text',
                    showCancelButton: true,
                    confirmButtonText: 'Tạo'
                }).then((res) => {
                    if(res.isConfirmed && res.value) {
                        $.post('{{ route('media.lib.folder') }}', {
                            _token: '{{ csrf_token() }}',
                            name: res.value,
                            parent_id: state.currentFolderId
                        }).done(() => {
                            loadFolders().then(renderFolderTree); 
                            Toast.fire('Đã tạo thư mục', '', 'success');
                        });
                    }
                });
            });
            
            // Move
            ui.btnMove.addEventListener('click', () => {
                 if(state.selectedItems.size === 0) return;
                 let opts = '<option value="">-- Root --</option>';
                 const sorted = [...state.folders].sort((a,b) => a.name.localeCompare(b.name));
                 sorted.forEach(f => {
                      opts += `<option value="${f.id}">${f.name}</option>`;
                 });

                 Swal.fire({
                     title: 'Chuyển đến...',
                     html: `<select id="moveDest" class="form-control">${opts}</select>`,
                     showCancelButton: true,
                     confirmButtonText: 'Di chuyển',
                     preConfirm: () => document.getElementById('moveDest').value
                 }).then(res => {
                     if(res.isConfirmed) {
                         const destId = res.value || null;
                         const items = Array.from(state.selectedItems).map(k => {
                             const p = k.split('_'); return {id: p[0], type: p[1]};
                         });
                         
                         $.post('{{ route('media.lib.move') }}', {
                             _token: '{{ csrf_token() }}', items, destination_folder_id: destId
                         }).done(r => {
                              Toast.fire(r.message, '', 'success');
                              loadFiles(state.currentFolderId, 1);
                         });
                     }
                 });
            });
            
            // Delete
            ui.btnDelete.addEventListener('click', () => {
                 if(state.selectedItems.size === 0) return;
                  Swal.fire({
                    title: 'Xóa vĩnh viễn?',
                    text: `${state.selectedItems.size} mục sẽ bị xóa.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Xóa'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const items = Array.from(state.selectedItems).map(k => {
                             const p = k.split('_'); return {id: p[0], type: p[1]};
                        });
                        $.ajax({
                            url: '{{ route('media.lib.delete') }}',
                            type: 'DELETE',
                            data: { items: items, _token: '{{ csrf_token() }}' },
                            success: (res) => {
                                Toast.fire(res.message, '', 'success');
                                loadFiles(state.currentFolderId, 1);
                            }
                        });
                    }
                });
            });
            
             // Sync
            ui.btnSync.addEventListener('click', () => {
                 ui.loader.classList.remove('d-none');
                 $.post('{{ route('media.lib.sync') }}', { _token: '{{ csrf_token() }}' })
                  .done((res) => {
                      Toast.fire(res.message, '', 'success');
                      loadFolders().then(renderFolderTree);
                      loadFiles(state.currentFolderId, 1);
                  })
                  .always(() => ui.loader.classList.add('d-none'));
            });
            
             // Ctrl + A
             document.addEventListener('keydown', (e) => {
                 if((e.ctrlKey || e.metaKey) && e.key === 'a') {
                      e.preventDefault();
                      state.allItems.forEach(i => state.selectedItems.add(`${i.id}_file`));
                      updateSelectionUI();
                      ui.checkAll.checked = true;
                 }
             });
        }
        
        function setupDragAndDrop() {
            // Target the Modal's Drop Area (The Form)
            const dropArea = document.querySelector('#uploadModal .modal-body');
            const fileInput = document.getElementById('fileInputModal');
            
            if(!dropArea || !fileInput) return;

            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropArea.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) { e.preventDefault(); e.stopPropagation(); }

            // Highlight logic
            ['dragenter', 'dragover'].forEach(eventName => {
                dropArea.addEventListener(eventName, () => {
                    dropArea.style.background = '#e9ecef';
                    dropArea.style.borderColor = '#007bff';
                }, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropArea.addEventListener(eventName, () => {
                    dropArea.style.background = '#f9f9f9';
                    dropArea.style.borderColor = '#ddd';
                }, false);
            });

            // Handle Drop
            dropArea.addEventListener('drop', (e) => {
                const dt = e.dataTransfer;
                const files = dt.files;
                if(files.length > 0) {
                     fileInput.files = files;
                     document.getElementById('uploadFileCount').textContent = files.length + ' file đã chọn';
                }
            });
        }
        
        const Toast = Swal.mixin({
          toast: true, position: 'top-end', showConfirmButton: false, timer: 3000,
          background: '#fff', iconColor: '#28a745'
        });
    });
</script>

<style>
.nav-treeview .nav-link { font-size: 0.9em; }
.nav-sidebar .nav-link.active { background-color: #007bff !important; color: #fff !important; }
.nav-sidebar .nav-link.active i { color: #fff !important; }

/* Selection Styles */
.media-item.selected {
    border: 2px solid #28a745 !important;
    background-color: #f0fff4 !important;
}
.selection-badge {
    position: absolute; top: 5px; right: 5px; width: 24px; height: 24px;
    background: #28a745; color: white; border-radius: 50%;
    text-align: center; line-height: 24px; font-size: 12px;
    display: none; box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}
.media-item.selected .selection-badge { display: block; }
.media-item:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
</style>
@endpush
