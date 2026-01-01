<div class="modal fade" id="mediaModal" tabindex="-1" role="dialog" aria-labelledby="mediaModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      {{-- HEADER --}}
      <div class="modal-header">
        <h5 class="modal-title font-weight-bold" id="mediaModalLabel">
            <i class="far fa-images mr-1 text-primary"></i> Media Library
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      {{-- BODY (Flex Container) --}}
      <div class="modal-body">
          
          {{-- TABS --}}
          <ul class="nav nav-tabs px-3 pt-2" id="mediaTabs" role="tablist">
              <li class="nav-item">
                  <a class="nav-link active font-weight-bold" id="tab-library-link" data-toggle="tab" href="#tabLibrary" role="tab" aria-selected="true">
                      Thư viện ảnh
                  </a>
              </li>
              <li class="nav-item">
                  <a class="nav-link font-weight-bold" id="tab-upload-link" data-toggle="tab" href="#tabUpload" role="tab" aria-selected="false">
                      Tải lên
                  </a>
              </li>
              <li class="ml-auto pb-1 d-flex align-items-center">
                  <div class="input-group input-group-sm" style="width: 220px;">
                      <input type="text" class="form-control" id="mediaSearch" placeholder="Tìm kiếm file...">
                      <div class="input-group-append">
                          <span class="input-group-text"><i class="fas fa-search"></i></span>
                      </div>
                  </div>
              </li>
          </ul>

          {{-- TAB CONTENTS --}}
          <div class="tab-content">
              
              {{-- 1. LIBRARY TAB --}}
              <div class="tab-pane fade show active" id="tabLibrary" role="tabpanel">
                  <div class="media-library-layout">
                      {{-- LEFT: Grid --}}
                      <div class="media-grid-container">
                          {{-- Breadcrumbs --}}
                          <div id="mediaBreadcrumbs" class="px-3 pt-2">
                              <nav aria-label="breadcrumb">
                                  <ol class="breadcrumb mb-0 p-0 bg-transparent"></ol>
                              </nav>
                          </div>
                          {{-- Scrollable Grid --}}
                          <div id="mediaGridScroll" class="media-grid-scroll-area custom-scrollbar">
                              <div id="mediaGrid" class="row"></div>
                              <div class="text-center mt-3 pb-3">
                                  <button id="mediaLoadMore" class="btn btn-sm btn-outline-primary d-none">Tải thêm</button>
                              </div>
                          </div>
                      </div>

                      {{-- RIGHT: Sidebar --}}
                      <div class="media-sidebar-container" id="mediaDetailSidebar">
                          {{-- Header --}}
                          <div class="media-sidebar-header border-bottom p-3 bg-white">
                              <h6 class="font-weight-bold mb-0 text-dark">Chi tiết file</h6>
                          </div>

                          {{-- Body (Scrollable) --}}
                          <div class="media-sidebar-scroll p-3">
                              {{-- Placeholder --}}
                              <div id="mediaDetailPlaceholder" class="text-center text-muted py-5">
                                  <i class="far fa-image fa-3x mb-2" style="opacity: 0.3;"></i>
                                  <p class="small">Chọn file để xem</p>
                              </div>

                              {{-- Info --}}
                              <div id="mediaDetailContent" style="display: none;">
                                  <div class="text-center mb-3 bg-light rounded border p-1">
                                      <img id="mediaDetailPreviewImg" src="" class="img-fluid" style="max-height: 180px; object-fit: contain;">
                                  </div>
                                  <div class="form-group mb-2">
                                      <label class="small text-muted mb-0 font-weight-bold">Tên file:</label>
                                      <div id="mediaDetailName" class="small text-break text-dark font-weight-600"></div>
                                  </div>
                                  <div class="form-group mb-0">
                                      <label class="small text-muted mb-0 font-weight-bold">Đường dẫn:</label>
                                      <div class="input-group input-group-sm">
                                          <input type="text" id="mediaDetailPath" class="form-control bg-white" readonly onclick="this.select()">
                                          <div class="input-group-append">
                                              <button class="btn btn-outline-secondary" type="button" onclick="navigator.clipboard.writeText(document.getElementById('mediaDetailPath').value)"><i class="far fa-copy"></i></button>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                          </div>

                          {{-- Footer (Fixed Bottom) --}}
                          <div class="media-sidebar-footer border-top p-3 bg-white" id="mediaDetailFooter" style="display: none;">
                              <button class="btn btn-outline-danger btn-sm btn-block shadow-sm" id="mediaDetailDeleteBtn">
                                  <i class="fas fa-trash-alt mr-1"></i> Xóa file này
                              </button>
                          </div>
                      </div>
                  </div>
              </div>

              {{-- 2. UPLOAD TAB --}}
              <div class="tab-pane fade" id="tabUpload" role="tabpanel">
                  <div class="media-upload-layout">
                      <form id="mediaUploadForm" class="w-100 d-flex justify-content-center">
                          <div class="media-upload-zone" onclick="document.getElementById('mediaUploadInput').click()">
                              <div class="upload-icon-wrapper">
                                  <i class="fas fa-cloud-upload-alt"></i>
                              </div>
                              <h4 class="upload-title">Kéo thả file vào đây</h4>
                              <p class="upload-desc">Hoặc nhấn để chọn file từ máy tính của bạn</p>
                              
                              <button type="button" class="btn btn-primary px-4 py-2 rounded-pill font-weight-bold shadow-sm upload-btn">
                                  <i class="fas fa-folder-open mr-2"></i> Duyệt File
                              </button>
                              
                              <input type="file" id="mediaUploadInput" name="files[]" multiple class="d-none" onchange="$('#mediaUploadForm').submit()">
                              
                              <div id="mediaUploadStatus" class="mt-4 font-weight-bold text-primary animate__animated animate__fadeIn"></div>
                          </div>
                      </form>
                      <div class="mt-3 text-muted small">
                          <i class="fas fa-info-circle mr-1"></i> Hỗ trợ: JPG, PNG, GIF, PDF, DOCX, XLSX (Max: 10MB)
                      </div>
                  </div>
              </div>

          </div>
      </div>
      
      {{-- Scripts Update for footer visibility --}}
      <script>
        document.addEventListener('DOMContentLoaded', function(){
            const detailContent = document.getElementById('mediaDetailContent');
            const detailFooter = document.getElementById('mediaDetailFooter');
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if(detailContent.style.display !== 'none'){
                        detailFooter.style.display = 'block';
                    } else {
                        detailFooter.style.display = 'none';
                    }
                });
            });
            observer.observe(detailContent, { attributes: true, attributeFilter: ['style'] });
        });
      </script>

      {{-- FOOTER --}}
      <div class="modal-footer justify-content-between bg-light">
        <div class="small"><span id="mediaSelectedCount" class="font-weight-bold">0</span> file đã chọn</div>
        <div>
          <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Đóng</button>
          <button type="button" id="mediaChooseBtn" class="btn btn-primary btn-sm" disabled>
              <i class="fas fa-check mr-1"></i> Chọn File
          </button>
        </div>
      </div>

    </div>
  </div>
</div>

@once
@push('css')
<link rel="stylesheet" href="{{ asset('css/modal.css') }}">
@endpush
@endonce