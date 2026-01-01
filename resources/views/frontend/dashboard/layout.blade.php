@extends('layouts.master')

@section('title', 'Bảng điều khiển')

@push('css')
<style>
    /* Custom style cho trang dashboard */
    .dashboard-sidebar {
        background-color: #f8f9fa;
        border-radius: 5px;
        padding: 15px;
    }
    .dashboard-sidebar .list-group-item.active {
        background-color: #343a40; /* Dark color for active item */
        border-color: #343a40;
    }
    .dashboard-sidebar .list-group-item i {
        margin-right: 10px;
        width: 20px;
    }
    .dashboard-content {
        background-color: #fff;
        border-radius: 5px;
        padding: 20px;
        border: 1px solid #dee2e6;
    }
    .profile-avatar {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border-radius: 50%;
        border: 3px solid #eee;
        display: inline-block;
    }
</style>
@endpush

@section('content')
<div id="wrapper" class="py-5">
    <div class="container">
        <div class="row">
            {{-- Cột Sidebar --}}
            <div class="col-md-3">
                <aside class="dashboard-sidebar">
                    <div class="text-center mb-3">
                        <img src="{{ asset(Auth::user()->avatar ?? 'https://placehold.co/120x120/EFEFEF/AAAAAA&text=avatar') }}" 
                             alt="Avatar" class="profile-avatar">
                        <h5 class="mt-2 mb-0">{{ Auth::user()->name }}</h5>
                    </div>
                    <div class="list-group">
                        <a href="{{ route('user.dashboard') }}" 
                           class="list-group-item list-group-item-action {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                           <i class="fa-solid fa-gauge-high"></i> Bảng điều khiển
                        </a>
                        <a href="{{ route('user.profile') }}" 
                           class="list-group-item list-group-item-action {{ request()->routeIs('user.profile') ? 'active' : '' }}">
                           <i class="fa-solid fa-user-pen"></i> Thông tin cá nhân
                        </a>
                        <a href="{{ route('user.orders') }}" 
                           class="list-group-item list-group-item-action {{ request()->routeIs('user.orders') || request()->routeIs('user.order.detail') ? 'active' : '' }}">
                           <i class="fa-solid fa-box-archive"></i> Lịch sử đơn hàng
                        </a>
                        <a href="{{ route('user.wishlist') }}" 
                           class="list-group-item list-group-item-action {{ request()->routeIs('user.wishlist') ? 'active' : '' }}">
                           <i class="fa-solid fa-heart"></i> Sản phẩm yêu thích
                        </a>
                        <a href="{{ route('logout') }}"
                           class="list-group-item list-group-item-action"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                           <i class="fa-solid fa-right-from-bracket"></i> Đăng xuất
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                    </div>
                </aside>
            </div>

            {{-- Cột Nội dung --}}
            <div class="col-md-9">
                <main class="dashboard-content">
                    @yield('dashboard_content')
                </main>
            </div>
        </div>
    </div>
</div>
@endsection