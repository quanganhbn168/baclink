@extends('layouts.admin')

@section('title', $pageTitle)
@section('content_header_title', 'Hồ sơ Đại lý')

@section('content')
<div class="row">
    {{-- Cột Trái: Profile Card --}}
    <div class="col-md-3">
        <div class="card card-primary card-outline">
            <div class="card-body box-profile">
                <div class="text-center">
                    <img class="profile-user-img img-fluid img-circle"
                         src="https://ui-avatars.com/api/?name={{ urlencode($agent->name) }}&background=random"
                         alt="User profile picture">
                </div>
                <h3 class="profile-username text-center">{{ $agent->name }}</h3>
                <p class="text-muted text-center">{{ $agent->dealerProfile->company_name ?? 'Đại lý tự do' }}</p>

                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b>Email</b> <a class="float-right">{{ $agent->email }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Hotline</b> <a class="float-right">{{ $agent->phone }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>MST</b> <a class="float-right">{{ $agent->dealerProfile->tax_id ?? '---' }}</a>
                    </li>
                    <li class="list-group-item text-center mt-2" style="border-bottom: 0;">
                        {{-- Nút Liên hệ nhanh --}}
                        @php
                            $phoneClean = str_replace([' ', '.', '-', '(', ')', '+84'], ['', '', '', '', '', '0'], $agent->phone);
                            if(substr($phoneClean, 0, 2) == '84') $phoneClean = '0' . substr($phoneClean, 2);
                        @endphp
                        
                        <a href="tel:{{ $agent->phone }}" class="btn btn-app bg-default">
                            <i class="fas fa-phone"></i> Gọi điện
                        </a>
                        <a href="https://zalo.me/{{ $phoneClean }}" target="_blank" class="btn btn-app bg-info">
                            <i class="fas fa-comment"></i> Zalo
                        </a>
                    </li>
                </ul>

                <a href="{{ route('admin.agents.edit', $agent->id) }}" class="btn btn-primary btn-block"><b>Chỉnh sửa hồ sơ</b></a>
            </div>
        </div>

        {{-- Box Ghi chú Admin --}}
        @if($agent->dealerProfile->admin_note)
        <div class="card card-warning">
            <div class="card-header">
                <h3 class="card-title">Ghi chú nội bộ</h3>
            </div>
            <div class="card-body">
                <p class="text-muted">{{ $agent->dealerProfile->admin_note }}</p>
            </div>
        </div>
        @endif
    </div>

    {{-- Cột Phải: Thống kê & Lịch sử --}}
    <div class="col-md-9">
        {{-- 3 Box thống kê --}}
        <div class="row">
            <div class="col-md-4 col-sm-6 col-12">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-wallet"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Số dư ví</span>
                        <span class="info-box-number">{{ number_format($agent->dealerProfile->wallet_balance) }} đ</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6 col-12">
                <div class="info-box">
                    <span class="info-box-icon bg-warning"><i class="fas fa-tag"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Chiết khấu</span>
                        <span class="info-box-number">{{ $agent->dealerProfile->discount_rate }}%</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6 col-12">
                <div class="info-box">
                    <span class="info-box-icon bg-danger"><i class="fas fa-shopping-cart"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Đã chi tiêu</span>
                        <span class="info-box-number">{{ number_format($agent->dealerProfile->total_spent ?? 0) }} đ</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Nút hành động chính --}}
        <div class="mb-3">
            <button class="btn btn-success" data-toggle="modal" data-target="#modalDeposit">
                <i class="fas fa-plus-circle"></i> Nạp tiền vào ví
            </button>
        </div>

        {{-- Tabs Lịch sử --}}
        <div class="card card-primary card-outline card-tabs">
            <div class="card-header p-0 pt-1 border-bottom-0">
                <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="tabs-history-tab" data-toggle="pill" href="#tabs-history" role="tab">Lịch sử Giao dịch</a>
                    </li>
                    {{-- Có thể thêm tab Đơn hàng sau này --}}
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="custom-tabs-three-tabContent">
                    <div class="tab-pane fade show active" id="tabs-history" role="tabpanel">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Thời gian</th>
                                    <th>Loại GD</th>
                                    <th>Số tiền</th>
                                    <th>Số dư sau GD</th>
                                    <th>Ghi chú</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($agent->transactions as $trans)
                                <tr>
                                    <td>{{ $trans->created_at->format('H:i d/m/Y') }}</td>
                                    <td>
                                        @if($trans->type == 'deposit')
                                            <span class="badge badge-success">Nạp tiền</span>
                                        @else
                                            <span class="badge badge-danger">Thanh toán</span>
                                        @endif
                                    </td>
                                    <td class="{{ $trans->type == 'deposit' ? 'text-success font-weight-bold' : 'text-danger' }}">
                                        {{ $trans->type == 'deposit' ? '+' : '-' }} 
                                        {{ number_format($trans->amount) }}
                                    </td>
                                    <td>{{ number_format($trans->balance_after) }}</td>
                                    <td>{{ $trans->note }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center text-muted">Chưa có giao dịch nào phát sinh.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Nạp tiền --}}
<div class="modal fade" id="modalDeposit" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <form action="{{ route('admin.agents.deposit', $agent->id) }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header bg-success">
                <h5 class="modal-title text-white"><i class="fas fa-wallet"></i> Nạp tiền cho đại lý</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    Đang nạp tiền cho: <b>{{ $agent->name }}</b> ({{ $agent->email }})
                </div>
                <div class="form-group">
                    <label>Số tiền (VNĐ) <span class="text-danger">*</span></label>
                    <input type="number" name="amount" class="form-control form-control-lg text-success font-weight-bold" 
                           placeholder="VD: 5000000" min="10000" required>
                    <small class="text-muted">Tối thiểu 10.000đ</small>
                </div>
                <div class="form-group">
                    <label>Ghi chú / Mã giao dịch <span class="text-danger">*</span></label>
                    <textarea name="note" class="form-control" rows="3" required placeholder="Ví dụ: Chuyển khoản VCB..."></textarea>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Hủy</button>
                <button type="submit" class="btn btn-success">Xác nhận Nạp</button>
            </div>
        </form>
    </div>
</div>
@endsection