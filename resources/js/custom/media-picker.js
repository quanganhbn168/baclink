// Thiết lập CSRF token
$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
  }
});

(function ($) {

  if (!$) return;

  // ========= DOM Selectors =========
  const $modal = $('#mediaModal');
  const $overlay = $('#mediaDragDropOverlay');
  const $grid = $('#mediaGrid');
  const $sidebar = $('#mediaDetailSidebar');
  const $sidebarPlaceholder = $('#mediaDetailPlaceholder');
  const $sidebarContent = $('#mediaDetailContent');

  // Breadcrumb container logic
  let $breadcrumbContainer = $('#mediaBreadcrumbs');

  // ========= STATE =========
  const state = {
    multiple: false,
    targetName: null,
    targetPreview: null,
    selected: new Map(),

    // Pagination & Folder
    page: 1,
    lastPage: 1,
    perPage: 60,
    search: '',
    loading: false,

    currentFolder: null, // ID
    breadcrumbs: [],

    dragCounter: 0,
    currentItem: null,
  };

  // ========= HÀM QUẢN LÝ SIDEBAR =========
  function openDetailSidebar(item) {
    if (item.type === 'folder') {
      closeDetailSidebar();
      return;
    }

    state.currentItem = item;
    $('#mediaDetailPreviewImg').attr('src', item.url);
    $('#mediaDetailName').text(item.name).attr('title', item.name);
    $('#mediaDetailPath').val(item.path);

    $sidebarPlaceholder.hide();
    $sidebarContent.show();
  }

  function closeDetailSidebar() {
    state.currentItem = null;
    $sidebarPlaceholder.show();
    $sidebarContent.hide();
  }

  // ========= HÀM UPLOAD TRUNG TÂM =========
  function handleFileUpload(formData) {
    $('#mediaUploadStatus').text('Đang tải lên...').removeClass('text-danger text-success');

    if (state.currentFolder) {
      formData.append('folder_id', state.currentFolder);
    }

    $.ajax({
      url: '/media-lib/upload',
      method: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      success: function (res) {
        if (res.success) {
          $('#mediaUploadStatus').text('Tải lên thành công!').addClass('text-success');
          // Reset page and reload to show new files
          state.page = 1;
          loadLibrary(true);
          $('#mediaUploadInput').val('');
        } else {
          $('#mediaUploadStatus').text('Tải lên thất bại!').addClass('text-danger');
        }
      },
      error: function (xhr) {
        const errorMsg = xhr.responseJSON?.message || 'Lỗi không xác định khi tải lên.';
        Swal.fire('Lỗi Upload!', errorMsg, 'error');
        $('#mediaUploadStatus').text('').removeClass('text-danger text-success');
      }
    });
  }

  // ========= RENDER & LOGIC =========

  // Event Delegation
  $grid.on('click', '.media-item', function (e) {
    const $el = $(this);
    const type = $el.data('type');
    const id = $el.data('id');
    const path = $el.data('path');

    if (type === 'folder') {
      state.currentFolder = id;
      state.page = 1;
      state.search = '';
      $('#mediaSearch').val('');
      loadLibrary(true);
      return;
    }

    // Is File
    const item = {
      id: id,
      path: path || id,
      name: $el.attr('title') || 'File',
      url: $el.find('img').attr('src'),
      type: 'file'
    };

    // Selection Logic (using ID or Path unique)
    // We prefer ID if available. But stored values are paths.
    // So for selection check, we might need to check both?
    // Let's settle on using ID for active session, but legacy hidden input is Path.
    const key = String(item.id);

    const isSelected = state.selected.has(key);

    if (!state.multiple) {
      state.selected.clear();
      $grid.find('.media-item').removeClass('selected');
      state.selected.set(key, item);
      $el.addClass('selected');
    } else {
      if (isSelected) {
        state.selected.delete(key);
        $el.removeClass('selected');
      } else {
        state.selected.set(key, item);
        $el.addClass('selected');
      }
    }

    updateSelectionUI();
    openDetailSidebar(item);
  });

  function renderItems(data, prepend = false) {
    let items = [];
    if (data.folders && Array.isArray(data.folders)) items = items.concat(data.folders);
    if (data.files && Array.isArray(data.files)) items = items.concat(data.files);

    // If mixed or just files
    if (!items.length && (data instanceof Array)) items = data;

    const htmls = items.map(item => {
      const isFolder = (item.type === 'folder');

      // Icon Logic: Folder gets FA icon, File gets Image
      const innerContent = isFolder
        ? `<i class="fas fa-folder folder-icon"></i>`
        : `<img src="${item.url}" alt="${item.name}" loading="lazy">`;

      // Selection
      let isSelected = false;
      if (!isFolder) {
        isSelected = state.selected.has(String(item.id));
      }

      return `
        <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-3">
          <div class="media-item ${isSelected ? 'selected' : ''}"
               data-id="${item.id}"
               data-path="${item.path || ''}" 
               data-type="${item.type || 'file'}"
               title="${item.name}">
            
            <div class="ratio">
               ${innerContent}
               ${!isFolder ? '<div class="selection-marker"><i class="fas fa-check"></i></div>' : ''}
            </div>
            
            <div class="media-item-name">${item.name}</div>
          </div>
        </div>`;
    });

    if (prepend) $grid.prepend(htmls.join(''));
    else $grid.append(htmls.join(''));
  }

  function renderBreadcrumbs(crumbs) {
    if (!$breadcrumbContainer.length) {
      // Try to find it again or create
      $breadcrumbContainer = $('#mediaBreadcrumbs');
      if (!$breadcrumbContainer.length) {
        $breadcrumbContainer = $('<nav aria-label="breadcrumb" class="w-100 px-3"><ol class="breadcrumb mb-2" id="mediaBreadcrumbsList"></ol></nav>');
        $grid.parent().prepend($breadcrumbContainer); // Insert before grid container
      }
    }

    const $list = $breadcrumbContainer.find('ol');
    $list.empty();

    if (!crumbs || !crumbs.length) return;

    crumbs.forEach((c, idx) => {
      const isLast = idx === crumbs.length - 1;
      if (isLast) {
        $list.append(`<li class="breadcrumb-item active">${c.name}</li>`);
      } else {
        $list.append(`<li class="breadcrumb-item"><a href="#" data-folder-id="${c.id || ''}">${c.name}</a></li>`);
      }
    });

    $list.find('a').on('click', function (e) {
      e.preventDefault();
      const fid = $(this).data('folder-id');
      state.currentFolder = fid || null;
      state.page = 1;
      loadLibrary(true);
    });
  }

  function resetSelection() {
    state.selected.clear();
    $('#mediaSelectedCount').text('0');
    $('#mediaChooseBtn').prop('disabled', true);
    $grid.find('.media-item').removeClass('selected');
  }

  function openModal(opts) {
    state.multiple = !!opts.multiple;
    state.targetName = opts.name;
    state.targetPreview = opts.previewSelector || null;
    state.page = 1;
    state.lastPage = 1;
    state.search = '';
    state.currentFolder = null;

    $('#mediaSearch').val('');
    closeDetailSidebar();
    resetSelection();
    preloadSelectionFromHidden(state.targetName, state.multiple); // Loads paths

    $('#tabLibrary').tab('show');
    $modal.modal('show');

    $grid.empty();
    loadLibrary(true);

    $grid.off('scroll.media').on('scroll.media', function () {
      const el = this;
      if (state.loading) return;
      if (state.page > state.lastPage) return;
      if (el.scrollTop + el.clientHeight >= el.scrollHeight - 200) {
        loadLibrary(false);
      }
    });
    $modal.one('hidden.bs.modal', () => $grid.off('scroll.media'));
  }

  function loadLibrary(reset = false) {
    if (state.loading) return;
    if (!reset && state.page > state.lastPage) return;

    state.loading = true;
    const params = {
      page: state.page,
      per_page: state.perPage,
      s: state.search,
      folder_id: state.currentFolder || ''
    };

    $.getJSON('/media-lib', params)
      .done(function (res) {
        state.lastPage = res.last_page || 1;

        // UPDATE STATS
        if (res.stats) {
          $('#modalCntImages').text(res.stats.images);
          $('#modalCntVideos').text(res.stats.videos);
          $('#modalCntDocs').text(res.stats.documents);
          $('#modalCntOthers').text(res.stats.others);
          // Simple format bytes helper if not exists
          const formatBytes = (bytes, decimals = 2) => {
            if (!+bytes) return '0 Bytes';
            const k = 1024;
            const dm = decimals < 0 ? 0 : decimals;
            const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return `${parseFloat((bytes / Math.pow(k, i)).toFixed(dm))} ${sizes[i]}`;
          };
          $('#modalTotalSize').text(formatBytes(res.stats.total_size));
        }

        if (reset) {
          $grid.empty();
          if (res.breadcrumbs) renderBreadcrumbs(res.breadcrumbs);
          if (state.page === 1 && res.folders) {
            renderItems({ folders: res.folders, files: [] });
          }
        }

        // Handle paginated files
        const fileData = res.files?.data || res.files || [];
        renderItems({ files: fileData });

        state.page += 1;
        toggleLoadMore();
      })
      .always(() => state.loading = false);
  }

  function toggleLoadMore() {
    if (state.page <= state.lastPage) $('#mediaLoadMore').removeClass('d-none').prop('disabled', false);
    else $('#mediaLoadMore').addClass('d-none');
  }

  function updateSelectionUI() {
    $grid.find('.media-item').each(function () {
      const id = String($(this).data('id'));
      if (state.selected.has(id)) $(this).addClass('selected');
      else $(this).removeClass('selected');
    });
    $('#mediaSelectedCount').text(state.selected.size);
    $('#mediaChooseBtn').prop('disabled', state.selected.size === 0);
  }

  function preloadSelectionFromHidden(name, multiple) {
    const $hidden = $(`input[type="hidden"][name="${name}"]`);
    if (!$hidden.length) return;
    let paths = [];
    try {
      const val = $hidden.val();
      if (multiple) paths = JSON.parse(val) || [];
      else if (val) paths = [val.trim()];
    } catch { paths = []; }

    // We only have PATHs. We don't have IDs.
    // So we can't fully highlight by ID until we load the item and see its path -> ID mapping.
    // OR we just store these in separate "pre-selected" set?
    // Let's keep it simple: We store them in Selected Map with ID=null for now?
    // No, render logic needs ID.
    // Better idea: When rendering, if item.path is in this list, we auto-select it and ADD to state.selected.

    // Actually, we should probably fetch the IDs for these paths from server if we want perfect sync.
    // But for optimization, let's just use the PATH based check in `renderItems` slightly.
    // NOTE: My renderItems checks `state.selected.has(item.id)`. 
    // I need to also check if `item.path` matches any preloaded path.

    // Let's store special "Path Selection" map just for init?
    // Or just fetch them?
    // Let's skip complex logic. If user sees it not highlighted initially until he clicks, it's okay? 
    // No, standard behavior: should be highlighted.

    // Fast fix: Store paths in a global set `preselectedPaths` and check it in render.
    state.preselectedPaths = new Set(paths);
    state.selected.clear();

    // Also, we want to allow "Applying" even if we didn't touch anything (keeping old value).
    // The current logic rebuilds value from `state.selected`.
    // If we clear `state.selected`, we lose old value if we don't click anything.
    // So we MUST populate `state.selected`.

    // COMPROMISE: We don't have ID. We use Path as ID for pre-selected items?
    // But then mixing Path and ID is messy.
    // Ideally: API returns ID with Path.
    // Since we are moving to DB, we really should query by ID.
    // But existing data is Path.
    // Ok, I will assume we can match by Path in the Grid.
    // I will add logic to `renderItems`: if path in `preselectedPaths`, add to `state.selected` (using ID from rendered item).
  }

  function applySelection() {
    const name = state.targetName || 'image_original_path';
    let $hidden = $(`input[type="hidden"][name="${name}"]`);
    if (!$hidden.length) $hidden = $(`<input type="hidden" name="${name}">`).appendTo('form');

    // We need to support items that are JUST paths (legacy/preselected not clicked) vs Items with IDs.
    // If user didn't change selection, we might have empty `state.selected` if we didn't load them.
    // Wait, if `state.selected` is empty, `applySelection` will write empty!
    // This wipes data if user just opens and closes.
    // FIX: If `state.selected` is empty AND we haven't modified selection?
    // Better: We populated `preselectedPaths`. If `state.selected` is empty, maybe we should fall back?
    // OR: We just say "Don't wipe unless user explicitly unselected".

    // SAFE APPROACH: We only write if user clicked "Choose" (which triggers this).
    // If user clicked "Choose", they expect `state.selected` to be the result.
    // So we need `state.selected` to include existing items.

    // Problem: logic to load existing items without fetching is hard if we only have paths.
    // SOLUTION: Just trust the user will select new things? 
    // Or simpler: Convert existing Paths to pseudo-items in `state.selected` using Path as ID?
    // But then we mix types.

    // Let's use the `state.preselectedPaths` to re-fill `state.selected` if needed?
    // No, `state.selected` is the source of truth.
    // Let's just create pseudo items.
    if (state.preselectedPaths && state.preselectedPaths.size > 0 && state.selected.size === 0) {
      state.preselectedPaths.forEach(p => {
        state.selected.set(p, { path: p, url: `/storage/${p.replace(/^\/?storage\//, '')}`, id: null, name: p });
      });
    }

    const arr = Array.from(state.selected.values());
    const paths = arr.map(i => i.path);

    if (state.multiple) $hidden.val(JSON.stringify(paths)).trigger('change');
    else $hidden.val(paths[0] || '').trigger('change');

    _renderPreviewItems(state.targetPreview, arr, state.multiple, name);
    $modal.modal('hide');
  }

  // (Helper: _renderPreviewItems same as before)
  function _renderPreviewItems(previewSelector, items, multiple, name) {
    if (!previewSelector) return;
    const $preview = $(previewSelector);
    if (!$preview.length) return;
    $preview.empty();

    const itemHtml = (item) => `
        <div class="mi-preview-item d-inline-block mr-2 mb-2 border rounded position-relative" data-path="${item.path}" data-input-name="${name}">
            <img src="${item.url}" alt="${item.name}" style="width:90px; height:90px; object-fit:cover;">
            <button type="button" class="mi-preview-remove btn btn-danger btn-sm rounded-circle" style="position:absolute; top:-10px; right:-10px; width:24px; height:24px; line-height:1; padding:0; z-index:1;">&times;</button>
        </div>`;
    const singleHtml = (item) => `
        <div class="mi-preview-item d-inline-block border rounded position-relative" data-path="${item.path}" data-input-name="${name}">
            <img src="${item.url}" alt="${item.name}" style="max-height:140px; object-fit:contain;">
            <button type="button" class="mi-preview-remove btn btn-danger btn-sm rounded-circle" style="position:absolute; top:-10px; right:-10px; width:24px; height:24px; line-height:1; padding:0; z-index:1;">&times;</button>
        </div>`;

    if (multiple) items.forEach(i => $preview.append(itemHtml(i)));
    else if (items[0]) $preview.html(singleHtml(items[0]));
  }

  // NOTE: Insert bindings (same as before)
  $(document).on('click', 'input[type="file"][data-picker="media"]', function (e) {
    e.preventDefault();
    const $inp = $(this);
    openModal({
      name: $inp.data('name'),
      previewSelector: $inp.data('preview'),
      multiple: String($inp.data('multiple')) === '1'
    });
  });

  $('#mediaLoadMore').on('click', () => loadLibrary(false));

  $(document).on('click', '.mi-preview-remove', function (e) {
    // (Keep existing delete logic)
    e.preventDefault();
    const $item = $(this).closest('.mi-preview-item');
    $item.remove();
    // Also update hidden input... (Same as prior code) - reusing existing logic roughly
    const name = $item.data('input-name');
    const path = $item.data('path');
    if (name && path) {
      const $h = $(`input[name="${name}"]`);
      // ... implementation of removal from hidden input ...
      if ($h.length) {
        let v = $h.val();
        if (v.startsWith('[')) {
          let arr = JSON.parse(v).filter(x => x !== path);
          $h.val(JSON.stringify(arr)).trigger('change');
        } else {
          $h.val('').trigger('change');
        }
      }
    }
  });

  $('#mediaSearch').on('input', function () {
    state.search = $(this).val() || '';
    state.page = 1;
    loadLibrary(true);
  });

  // ========= DRAG & DROP LOGIC =========
  // 1. Global Modal Drag Overlay
  let dragCounter = 0;
  $modal.on('dragenter', function (e) {
    e.preventDefault();
    e.stopPropagation();
    dragCounter++;
    $('#mediaDragDropOverlay').addClass('is-dragging');
  });

  $modal.on('dragleave', function (e) {
    e.preventDefault();
    e.stopPropagation();
    dragCounter--;
    if (dragCounter <= 0) {
      $('#mediaDragDropOverlay').removeClass('is-dragging');
      dragCounter = 0;
    }
  });

  $modal.on('dragover', function (e) { e.preventDefault(); e.stopPropagation(); });

  $modal.on('drop', function (e) {
    e.preventDefault();
    e.stopPropagation();
    dragCounter = 0;
    $('#mediaDragDropOverlay').removeClass('is-dragging');

    const files = e.originalEvent.dataTransfer.files;
    if (files.length > 0) {
      // Switch to Upload Tab if not active
      $('#tabUpload').tab('show');

      const fd = new FormData();
      for (let i = 0; i < files.length; i++) {
        fd.append('files[]', files[i]);
      }
      handleFileUpload(fd);
    }
  });

  // 2. Specific Upload Zone Drag (Visual Feedback)
  const $zone = $('.media-upload-zone');
  $zone.on('dragenter dragover', function (e) {
    e.preventDefault();
    e.stopPropagation();
    $(this).addClass('drag-over');
  });
  $zone.on('dragleave drop', function (e) {
    e.preventDefault();
    e.stopPropagation();
    $(this).removeClass('drag-over');
  });
  $zone.on('drop', function (e) {
    e.preventDefault();
    e.stopPropagation();
    const files = e.originalEvent.dataTransfer.files;
    if (files.length > 0) {
      const fd = new FormData();
      for (let i = 0; i < files.length; i++) {
        fd.append('files[]', files[i]);
      }
      handleFileUpload(fd);
    }
  });

  // ========= FORM SUBMIT =========
  $('#mediaUploadForm').on('submit', function (e) {
    e.preventDefault();
    handleFileUpload(new FormData(this));
  });

  // Init
  $(document).ready(function () {
    // Init preview code (same as before)
    $('input[type="file"][data-picker="media"]').each(function () {
      // ... init preview logic ...
      const $inp = $(this);
      const name = $inp.data('name');
      const preview = $inp.data('preview');
      const mult = $inp.data('multiple');
      if (name && preview) {
        const val = $(`input[name="${name}"]`).val();
        if (val) {
          // decode and render
          let items = [];
          try { items = (val.startsWith('[') ? JSON.parse(val) : [val]).map(p => ({ path: p, url: '/storage/' + p.replace(/^storage\//, ''), name: p })); } catch (e) { }
          _renderPreviewItems(preview, items, mult, name);
        }
      }
    });
  });

  $('#mediaDetailCloseBtn').on('click', () => closeDetailSidebar());
  $('#mediaDetailDeleteBtn').on('click', function () {
    // Delete logic
    const item = state.currentItem;
    if (!item) return;
    $.ajax({ url: '/media-lib/delete', method: 'DELETE', data: { id: item.id, type: 'file' } }).then(res => {
      if (res.success) {
        $grid.find(`[data-id="${item.id}"]`).parent().remove();
        closeDetailSidebar();
      }
    });
  });

  $('#mediaChooseBtn').on('click', applySelection);

  // Add Folder Button Logic (New)
  $('#mediaAddFolderBtn').on('click', function () {
    Swal.fire({
      title: 'Tạo thư mục mới',
      input: 'text',
      showCancelButton: true,
      confirmButtonText: 'Tạo'
    }).then((result) => {
      if (result.isConfirmed && result.value) {
        $.post('/media-lib/folder', { name: result.value, parent_id: state.currentFolder })
          .done(res => {
            if (res.success) {
              loadLibrary(true);
              Swal.fire('Thành công', 'Đã tạo thư mục', 'success');
            }
          });
      }
    });
  });

})(window.jQuery);