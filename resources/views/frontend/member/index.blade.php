@extends('layouts.master')
@section('title', 'Danh sách hội viên - ' . $setting->name)

@push('css')
    <link rel="stylesheet" href="{{ asset('css/post.css') }}">
    <link rel="stylesheet" href="{{ asset('css/member.css') }}">
@endpush

@section('content')
<main class="post-detail-wrapper bg-white">
    <div class="container container-custom">
        <!-- Breadcrumb -->
        <nav class="post-breadcrumb">
            <a href="{{ url('/') }}">Trang chủ</a>
            <span>»</span>
            <span class="active">Hội viên</span>
        </nav>

        <h1 class="member-page-title">DANH SÁCH HỘI VIÊN BACLINK</h1>

        <div class="row">
            <!-- Left Column: Member List -->
            <div class="col-lg-9">
                <div class="member-grid">
                    @forelse($members as $member)
                        <div class="member-card">
                            <div class="member-logo-wrap">
                                <img src="{{ $member->avatar ?? asset('images/setting/no-image.png') }}" alt="{{ optional($member->dealerProfile)->company_name }}">
                            </div>
                            <div class="member-info">
                                <div class="company-name">{{ optional($member->dealerProfile)->company_name ?? 'CÔNG TY THÀNH VIÊN' }}</div>
                                <div class="rep-name">{{ $member->name }}</div>
                                <ul class="member-details">
                                    <li>
                                        <i class="fas fa-user-tie"></i>
                                        <span>Chủ tịch HĐQT</span>
                                    </li>
                                    <li>
                                        <i class="fas fa-briefcase"></i>
                                        <span>Lĩnh vực hoạt động: {{ Str::limit(optional($member->dealerProfile)->address ?? 'Đang cập nhật...', 100) }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-info w-100">Hiện chưa có thông tin hội viên.</div>
                    @endforelse
                </div>

                <!-- Pagination -->
                <div class="custom-pagination">
                    {{ $members->links('pagination::bootstrap-4') }}
                </div>

                <!-- Ads Banner -->
                <div class="ads-banner-section mt-5">
                    <div class="ads-banner-wrapper">
                        <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&q=80&w=1200&h=250" alt="Quảng cáo">
                    </div>
                </div>
            </div>

            <!-- Right Column: Sidebar -->
            <div class="col-lg-3">
                <aside class="post-sidebar">
                    <!-- Red CTA Card -->
                    <a href="{{ route('register') }}" class="sidebar-cta-card">
                        <div class="card-icon"><i class="fas fa-id-card"></i></div>
                        <div class="card-content">
                            <span class="card-title">Đăng ký hội viên</span>
                            <span class="card-desc">Hội Công nghiệp chủ lực Thành phố Bắc Ninh</span>
                        </div>
                    </a>

                    <!-- Trending Section -->
                    <div class="sidebar-trending">
                        <h3 class="sidebar-heading">Xem nhiều</h3>
                        @foreach($trendingPosts as $trend)
                            <div class="trending-item">
                                <div class="trending-thumb">
                                    <a href="{{ route('frontend.slug.handle', $trend->slug) }}">
                                        <img src="{{ optional($trend->mainImage())->url() ?? asset('images/setting/no-image.png') }}" alt="{{ $trend->title }}">
                                    </a>
                                </div>
                                <div class="trending-info">
                                    <h4 style="margin-bottom: 5px;"><a href="{{ route('frontend.slug.handle', $trend->slug) }}">{{ Str::limit($trend->title, 60) }}</a></h4>
                                    <div class="trending-date" style="font-size: 11px; color: #999;">{{ $trend->created_at->format('d/m/Y') }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </aside>
            </div>
        </div>
    </div>
</main>
@endsection
