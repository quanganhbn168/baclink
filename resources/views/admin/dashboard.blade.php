@extends('layouts.admin')

@section('title', 'Bảng điều khiển')

@section('content_header')
    <div class="d-flex justify-content-between">
        <h1>Bảng điều khiển</h1>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        {{-- Row 1: Thống kê chính --}}
        <div class="row">
            {{-- Hội viên --}}
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $totalMembers ?? 0 }}</h3>
                        <p>Hội viên</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <a href="{{ route('admin.members.index') }}" class="small-box-footer">
                        Xem chi tiết <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            {{-- Sản phẩm --}}
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $totalProducts ?? 0 }}</h3>
                        <p>Sản phẩm</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-box"></i>
                    </div>
                    <a href="{{ route('admin.products.index') }}" class="small-box-footer">
                        Quản lý kho <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- Row 2: Bảng chi tiết --}}
        <div class="row">
            {{-- Cột trái: Hội viên --}}
            <section class="col-lg-7 connectedSortable">

                {{-- Hội viên mới đăng ký --}}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Hội viên mới đăng ký</h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Cán bộ đại diện</th>
                                    <th>Tên công ty</th>
                                    <th>Ngày đăng ký</th>
                                    <th style="width: 40px"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentMembers as $member)
                                    <tr>
                                        <td>{{ $member->representative_name }}</td>
                                        <td>{{ Str::limit($member->company_name, 30) }}</td>
                                        <td>{{ $member->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            <a href="{{ route('admin.members.show', $member->id) }}" class="btn btn-xs btn-info"><i class="fas fa-eye"></i></a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center">Chưa có hội viên nào</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            {{-- Cột phải: Liên hệ & Bài viết --}}
            <section class="col-lg-5 connectedSortable">
                
                {{-- Liên hệ gần đây --}}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Liên hệ mới</h3>
                    </div>
                    <div class="card-body p-0">
                        <ul class="products-list product-list-in-card pl-2 pr-2">
                            @forelse($recentContacts as $contact)
                                <li class="item">
                                    <div class="product-info ml-0">
                                        <a href="javascript:void(0)" class="product-title">{{ $contact->name }}
                                            <span class="badge badge-info float-right">{{ $contact->created_at->diffForHumans() }}</span>
                                        </a>
                                        <span class="product-description">
                                            {{ Str::limit($contact->message, 50) }}
                                        </span>
                                    </div>
                                </li>
                            @empty
                                <li class="item text-center">Chưa có liên hệ nào</li>
                            @endforelse
                        </ul>
                    </div>
                    <div class="card-footer text-center">
                        <a href="{{ route('admin.contacts.index') }}" class="uppercase">Xem tất cả liên hệ</a>
                    </div>
                </div>

                {{-- Thống kê nhanh --}}
                <div class="info-box mb-3 bg-warning">
                    <span class="info-box-icon"><i class="fas fa-newspaper"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Bài viết</span>
                        <span class="info-box-number">{{ $totalPosts }}</span>
                    </div>
                </div>
                <div class="info-box mb-3 bg-danger">
                    <span class="info-box-icon"><i class="fas fa-envelope"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Liên hệ</span>
                        <span class="info-box-number">{{ $totalContacts }}</span>
                    </div>
                </div>

            </section>
        </div>
    </div>
@stop
