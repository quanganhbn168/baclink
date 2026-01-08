@push('css')
@vite(['resources/css/custom/product.css'])
    <style>
        .project-info ul li {
            padding: 8px 0;
            border-bottom: 1px dashed #eee;
            font-size: 1.1rem;
        }
        .custom-section-title {
            font-size: 2rem;
            font-weight: bold;
            text-align: center;
            margin: 2.5rem 0 1.5rem 0;
            color: #333;
        }
        .project-content {
            margin-top: 2.5rem;
            line-height: 1.8;
        }

        /* --- CSS CHO SLIDER THƯ VIỆN ẢNH --- */
        .gallery-top {
            height: 80%;
            width: 100%;
            border: 1px solid #eee;
            border-radius: 8px;
        }
        .gallery-top .swiper-slide img {
            width: 100%;
            height: 450px;
            object-fit: cover;
        }

        .gallery-thumbs {
            height: 20%;
            box-sizing: border-box;
            padding: 10px 0;
        }
        .gallery-thumbs .swiper-slide {
            width: 25%;
            height: 100px;
            opacity: 0.6;
            cursor: pointer;
            transition: opacity 0.3s ease;
        }
        .gallery-thumbs .swiper-slide-thumb-active {
            opacity: 1;
            border: 2px solid #007bff;
        }
        .gallery-thumbs .swiper-slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .swiper-button-next, .swiper-button-prev {
            color: #007bff;
        }

        /* --- CSS CHO SLIDER DỰ ÁN KHÁC --- */
        .other-projects-slider .project-card {
            border: 1px solid #eee;
            border-radius: 8px;
            overflow: hidden;
            text-align: center;
            transition: box-shadow 0.3s ease;
        }
        .other-projects-slider .project-card:hover {
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .other-projects-slider .project-card img {
            width: 100%;
            height: 220px;
            object-fit: cover;
        }
        .other-projects-slider .project-card .project-name {
            display: block;
            padding: 15px;
            font-weight: bold;
            color: #333;
            text-decoration: none;
        }
    </style>
@endpush

@section('content')
<div id="project-wrapper">
    <div class="project-banner mb-4">
        <img src="{{ optional($project->bannerImage())->url() }}"
             alt="{{ $project->name }}" width="1920" height="300" loading="eager">
        <div class="project-banner_overlay"></div>
    </div>

    <div class="container my-5">
        <div class="row">
            <div class="col-12 col-md-6">
                <div class="project-info">
                    <h1 class="project-name">{{$project->name}}</h1>
                    <div class="project-description">{!! $project->description !!}</div>
                    <ul>
                        <li><strong>Tên dự án:</strong> {{$project->name}}</li>
                        <li><strong>Chủ đầu tư:</strong> {{$project->investor}}</li>
                        <li><strong>Địa chỉ:</strong> {{$project->address}}</li>
                        <li><strong>Năm thực hiện:</strong> {{$project->year}}</li>
                        <li><strong>Giá trị gói thầu:</strong> {{$project->value}}</li>
                    </ul>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="project-image">
                    <img src="{{ optional($project->mainImage())->url() }}" alt="{{$project->name}}">
                </div>
            </div>
        </div>

        {{-- ============ GALLERY (Chuẩn HasImages + Blade thuần) ============ --}}
        @if($project->gallery && $project->gallery->isNotEmpty())
        <section class="project-gallery mt-5" aria-label="Thư viện hình ảnh dự án">
            <h2 class="custom-section-title">Hình ảnh dự án</h2>

            <!-- Slider lớn -->
            <div class="swiper gallery-top">
                <div class="swiper-wrapper">
                    @foreach($project->gallery as $img)
                        <div class="swiper-slide">
                            <img
                                class="swiper-lazy"
                                src="{{ optional($img)->url() }}"
                                data-src="{{ optional($img)->url() }}"
                                alt="{{ $img->alt ?? $project->name }}"
                                loading="lazy"
                            >
                            <div class="swiper-lazy-preloader"></div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Thumbnails -->
            <div class="swiper gallery-thumbs mt-2">
                <div class="swiper-wrapper">
                    @foreach($project->gallery as $img)
                        <div class="swiper-slide">
                            <img
                                src="{{ optional($img)->url() }}"
                                alt="Thumbnail {{ $project->name }}"
                                loading="lazy"
                            >
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
        @endif


        {{-- ====================================================== --}}
        {{-- PHẦN NỘI DUNG CHI TIẾT --}}
        {{-- ====================================================== --}}
        <div class="project-content">
            {!!$project->content!!}
        </div>

        {{-- ====================================================== --}}
        {{-- PHẦN DỰ ÁN TIÊU BIỂU KHÁC --}}
        {{-- ====================================================== --}}
        @if($relatedProjects && $relatedProjects->count() > 0)
        <div class="otherProject">
            <h2 class="custom-section-title">Dự án tiêu biểu khác</h2>
            <div class="swiper other-projects-slider">
                <div class="swiper-wrapper">
                    @foreach($relatedProjects as $other)
                    <div class="swiper-slide">
                        <a href="{{ route('frontend.slug.handle', $other->slug) }}" class="project-card">
                            <img src="{{ optional($other->mainImage())->url() }}" alt="{{ $other->name }}">
                            <span class="project-name">{{ $other->name }}</span>
                        </a>
                    </div>
                    @endforeach
                </div>
                 <div class="swiper-pagination"></div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('js')
    {{-- Link JS của Swiper Slider (BẮT BUỘC) --}}
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // --- KHỞI TẠO SLIDER THƯ VIỆN ẢNH ---
        // Kiểm tra xem element có tồn tại không trước khi khởi tạo
        if (document.querySelector('.gallery-thumbs') && document.querySelector('.gallery-top')) {
            const galleryThumbs = new Swiper('.gallery-thumbs', {
                spaceBetween: 10,
                slidesPerView: 4,
                freeMode: true,
                watchSlidesProgress: true,
            });

            const galleryTop = new Swiper('.gallery-top', {
                spaceBetween: 10,
                lazy: { loadPrevNext: true, loadOnTransitionStart: true },
                lazy: { loadPrevNext: true, loadOnTransitionStart: true },
                preloadImages: false,
                watchSlidesProgress: true,
                thumbs: {
                    swiper: galleryThumbs,
                },
                observer: true,
                observeParents: true,
            });
        }
        
        // --- KHỞI TẠO SLIDER DỰ ÁN KHÁC ---
        // Kiểm tra xem element có tồn tại không trước khi khởi tạo
        if (document.querySelector('.other-projects-slider')) {
            const otherProjectsSlider = new Swiper('.other-projects-slider', {
                loop: true,
                spaceBetween: 30,
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                breakpoints: {
                    576: { slidesPerView: 1 },
                    768: { slidesPerView: 2 },
                    992: { slidesPerView: 3 },
                }
            });
        }
    });
    </script>
@endpush
