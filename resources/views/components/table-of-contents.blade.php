@if (!empty($headings))
    {{-- PHẦN NÀY DÀNH CHO DESKTOP - KHÔNG THAY ĐỔI --}}
    {{-- Nó sẽ được hiển thị ở sidebar-sticky như cũ --}}
    <nav class="table-of-contents d-none d-lg-block">
        <h3 class="toc-title">☰ Mục lục</h3>
        <ol>
            @foreach ($headings as $heading)
                <li>
                    <a href="#{{ $heading['slug'] }}">{{ $heading['text'] }}</a>
                </li>
            @endforeach
        </ol>
    </nav>

    {{-- ========================================================= --}}
    {{-- PHẦN MỚI DÀNH RIÊNG CHO MOBILE --}}
    {{-- ========================================================= --}}
    <div class="toc-mobile-container d-lg-none">
        {{-- 1. NÚT BẤM TRÔI NỔI (FAB) --}}
        <button type="button" class="toc-fab" id="toc-fab-button" aria-label="Mở mục lục">
            <i class="fa-solid fa-list-ul"></i>
        </button>

        {{-- 2. MODAL OVERLAY CHỨA MỤC LỤC --}}
        <div class="toc-modal-overlay" id="toc-modal-overlay">
            <div class="toc-modal-content">
                {{-- Tiêu đề và nút đóng --}}
                <div class="toc-header">
                    <h3 class="toc-title">☰ Mục lục</h3>
                    <button type="button" class="toc-close-button" id="toc-close-button" aria-label="Đóng mục lục">&times;</button>
                </div>
                {{-- Danh sách mục lục --}}
                <div class="toc-list">
                    <ol>
                        @foreach ($headings as $heading)
                            <li>
                                {{-- Thêm class 'toc-link' để JS xử lý việc đóng modal sau khi click --}}
                                <a class="toc-link" href="#{{ $heading['slug'] }}">{{ $heading['text'] }}</a>
                            </li>
                        @endforeach
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endif


@push('css')
<style>
    /* --- CSS CƠ BẢN CHO MỤC LỤC (DESKTOP) --- */
    .table-of-contents {
        border: 1px solid #e0e0e0; padding: 15px; background: #f9f9f9;
        border-radius: 5px; margin-bottom: 25px;
    }
    .table-of-contents .toc-title { font-weight: bold; margin: 0; font-size: 1.2em; }
    .table-of-contents ol { padding-left: 20px; margin-top: 10px; margin-bottom: 0; }
    .table-of-contents ol li { margin-bottom: 8px; }
    .table-of-contents ol li a { text-decoration: none; color: #333; }
    .table-of-contents ol li a:hover { color: #007bff; }

    /* ========================================================= */
    /* --- CSS NÂNG CẤP CHO TRẢI NGHIỆM MOBILE --- */
    /* ========================================================= */
    @media (max-width: 991px) {
        /* --- Nút bấm trôi nổi (FAB) --- */
        .toc-fab {
            position: fixed;
            left: 20px; /* Đổi từ right sang left */
            top: 50%;
            width: 50px;
            height: 50px;
            background-color: #007bff;
            color: white;
            border-radius: 50%;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            z-index: 1050; /* Phải cao hơn các element khác */
            cursor: pointer;
        }

        /* --- Lớp Overlay toàn màn hình --- */
        .toc-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            z-index: 1051;
            display: none; /* Mặc định ẩn */
            align-items: center;
            justify-content: center;
        }
        /* Class để hiện modal */
        .toc-modal-overlay.is-visible {
            display: flex;
        }

        /* --- Khung nội dung của Modal --- */
        .toc-modal-content {
            background: white;
            padding: 20px;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
            max-height: 80vh;
            display: flex;
            flex-direction: column;
        }
        .toc-modal-content .toc-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        /* --- Danh sách bên trong modal --- */
        .toc-modal-content .toc-list {
            overflow-y: auto; /* Tự động có thanh cuộn nếu mục lục quá dài */
        }
        
        /* --- Nút đóng (X) --- */
        .toc-close-button {
            background: none;
            border: none;
            font-size: 28px;
            font-weight: bold;
            color: #666;
            cursor: pointer;
        }
    }
</style>
@endpush

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fabButton = document.getElementById('toc-fab-button');
    const modalOverlay = document.getElementById('toc-modal-overlay');
    const closeButton = document.getElementById('toc-close-button');
    const tocLinks = document.querySelectorAll('.toc-link');

    // Chỉ thực thi JS nếu các element tồn tại (tránh lỗi trên desktop)
    if (fabButton && modalOverlay && closeButton) {
        
        // Hàm để mở Modal
        function openModal() {
            modalOverlay.classList.add('is-visible');
            document.body.style.overflow = 'hidden'; // Ngăn cuộn trang nền
        }

        // Hàm để đóng Modal
        function closeModal() {
            modalOverlay.classList.remove('is-visible');
            document.body.style.overflow = ''; // Cho phép cuộn trang lại
        }

        // 1. Bấm nút FAB để mở Modal
        fabButton.addEventListener('click', openModal);

        // 2. Bấm nút (X) để đóng Modal
        closeButton.addEventListener('click', closeModal);

        // 3. Bấm ra ngoài vùng nội dung để đóng Modal
        modalOverlay.addEventListener('click', function(event) {
            if (event.target === modalOverlay) {
                closeModal();
            }
        });

        // 4. Bấm vào một link trong mục lục -> nhảy đến vị trí đó VÀ đóng Modal
        tocLinks.forEach(function(link) {
            link.addEventListener('click', closeModal);
        });
    }
});
</script>
@endpush