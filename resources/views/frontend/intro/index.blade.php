@extends('layouts.master')
@section('title', 'Giới thiệu - ' . $setting->name)

@push('css')
    <link rel="stylesheet" href="{{ asset('css/post.css') }}">
    <style>
        .intro-sidebar {
            background: #fff;
            border: 1px solid #eee;
            border-radius: 4px;
            overflow: hidden;
        }
        .intro-sidebar .sidebar-title {
            background: #203e7d;
            color: #fff;
            padding: 15px 20px;
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            text-transform: uppercase;
        }
        .intro-sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .intro-sidebar ul li {
            border-bottom: 1px solid #eee;
        }
        .intro-sidebar ul li:last-child {
            border-bottom: none;
        }
        .intro-sidebar ul li a {
            display: block;
            padding: 12px 20px;
            color: #333;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
        }
        .intro-sidebar ul li a:hover,
        .intro-sidebar ul li a.active {
            background: #f9f9f9;
            color: #e32124;
            padding-left: 25px;
        }
        .intro-sidebar ul li a.active {
            font-weight: 700;
        }
        
        .intro-content {
            background: #fff;
            padding: 30px;
            border: 1px solid #eee;
            border-radius: 4px;
        }
        .intro-content h1 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 20px;
            color: #203e7d;
            text-transform: uppercase;
            padding-bottom: 15px;
            border-bottom: 2px solid #eee;
        }
        .intro-content .content {
            font-size: 16px;
            line-height: 1.6;
            color: #444;
        }
        .intro-content .content img {
            max-width: 100%;
            height: auto;
            margin: 15px 0;
        }
        .intro-content .content p {
            margin-bottom: 15px;
        }
    </style>
@endpush

@section('content')
<main class="py-5 bg-light">
    <div class="container container-custom">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb bg-transparent p-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Giới thiệu</li>
            </ol>
        </nav>

        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3 mb-4">
                <aside class="intro-sidebar sticky-top" style="top: 100px;">
                    <h3 class="sidebar-title">VỀ CHÚNG TÔI</h3>
                    <ul>
                        @foreach($intros as $item)
                            <li>
                                <a href="{{ route('frontend.intro.getBySlug', $item->slug) }}" 
                                   class="{{ $intro->id == $item->id ? 'active' : '' }}">
                                    <i class="fas fa-angle-right mr-2"></i> {{ $item->title }}
                                </a>
                            </li>
                        @endforeach
                        <li>
                            <a href="{{ route('frontend.members.index') }}">
                                <i class="fas fa-angle-right mr-2"></i> HỘI VIÊN BACLINK
                            </a>
                        </li>
                    </ul>
                    
                    <!-- Red CTA Card (Optional reuse) -->
                    <div class="mt-4">
                         <a href="{{ route('register') }}" class="sidebar-cta-card">
                            <div class="card-icon"><i class="fas fa-id-card"></i></div>
                            <div class="card-content">
                                <span class="card-title">Đăng ký hội viên</span>
                                <span class="card-desc">Hội Công nghiệp chủ lực Bắc Ninh</span>
                            </div>
                        </a>
                    </div>
                </aside>
            </div>

            <!-- Main Content -->
            <div class="col-lg-9">
                <article class="intro-content">
                    <h1>{{ $intro->title }}</h1>
                    <div class="content">
                        {!! $intro->content !!}
                    </div>
                </article>
            </div>
        </div>
    </div>
</main>
@endsection
