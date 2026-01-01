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
            {{-- Đơn hàng mới --}}
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $newOrdersCount ?? 0 }}</h3>
                        <p>Đơn hàng mới</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <a href="{{ route('admin.orders.index') }}" class="small-box-footer">
                        Xem chi tiết <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            {{-- Đăng ký đại lý mới --}}
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $newDealerAppsCount ?? 0 }}</h3>
                        <p>Đăng ký Đại lý mới</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <a href="{{ route('admin.dealer-applications.index') }}" class="small-box-footer">
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

            {{-- Doanh thu (ví dụ) --}}
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ number_format($revenue ?? 0, 0, ',', '.') }}<sup style="font-size: 20px">đ</sup></h3>
                        <p>Tổng doanh thu</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <a href="{{ route('admin.orders.index') }}" class="small-box-footer">
                        Báo cáo <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- Row 2: Biểu đồ --}}
        <div class="row">
            <div class="col-md-8">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Doanh thu 6 tháng gần nhất</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart">
                            <canvas id="revenueChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-warning card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Trạng thái đơn hàng</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="orderStatusChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Row 3: Bảng chi tiết --}}
        <div class="row">
            {{-- Cột trái: Đơn hàng & Đại lý --}}
            <section class="col-lg-7 connectedSortable">
                
                {{-- Đơn hàng gần đây --}}
                <div class="card">
                    <div class="card-header border-transparent">
                        <h3 class="card-title">Đơn hàng gần đây</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table m-0">
                                <thead>
                                    <tr>
                                        <th>Mã đơn</th>
                                        <th>Khách hàng</th>
                                        <th>Trạng thái</th>
                                        <th>Tổng tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentOrders as $order)
                                        <tr>
                                            <td><a href="{{ route('admin.orders.show', $order->id) }}">{{ $order->code }}</a></td>
                                            <td>{{ $order->customer_name }}</td>
                                            <td>
                                                <span class="badge badge-{{ $order->status->color() }}">
                                                    {{ $order->status->label() }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="sparkbar" data-color="#00a65a" data-height="20">
                                                    {{ number_format($order->total_price, 0, ',', '.') }}đ
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center">Chưa có đơn hàng nào</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer clearfix">
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-secondary float-right">Xem tất cả đơn hàng</a>
                    </div>
                </div>

                {{-- Đăng ký đại lý gần đây --}}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Đăng ký Đại lý mới</h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Tên</th>
                                    <th>Công ty</th>
                                    <th>Trạng thái</th>
                                    <th style="width: 40px"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentDealerApps as $app)
                                    <tr>
                                        <td>{{ $app->name }}</td>
                                        <td>{{ $app->company }}</td>
                                        <td>
                                            @if($app->status == 'pending')
                                                <span class="badge badge-warning">Chờ duyệt</span>
                                            @elseif($app->status == 'approved')
                                                <span class="badge badge-success">Đã duyệt</span>
                                            @else
                                                <span class="badge badge-danger">{{ $app->status }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.dealer-applications.index') }}" class="btn btn-xs btn-info"><i class="fas fa-eye"></i></a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center">Chưa có đăng ký nào</td></tr>
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

@push('js')
    {{-- ChartJS --}}
    <script src="{{ asset('vendor/adminlte/plugins/chart.js/Chart.min.js') }}"></script>

    <script>
        $(function () {
            // ---------------------
            // - REVENUE CHART -
            // ---------------------
            var revenueChartCanvas = $('#revenueChart').get(0).getContext('2d')
            var revenueChartData = {
                labels: @json($chartRevenueLabels),
                datasets: [
                    {
                        label: 'Doanh thu',
                        backgroundColor: 'rgba(60,141,188,0.9)',
                        borderColor: 'rgba(60,141,188,0.8)',
                        pointRadius: false,
                        pointColor: '#3b8bba',
                        pointStrokeColor: 'rgba(60,141,188,1)',
                        pointHighlightFill: '#fff',
                        pointHighlightStroke: 'rgba(60,141,188,1)',
                        data: @json($chartRevenueData)
                    }
                ]
            }

            var revenueChartOptions = {
                maintainAspectRatio: false,
                responsive: true,
                legend: {
                    display: false
                },
                scales: {
                    xAxes: [{
                        gridLines: {
                            display: false,
                        }
                    }],
                    yAxes: [{
                        gridLines: {
                            display: false,
                        },
                         ticks: {
                            beginAtZero: true,
                            callback: function(value, index, values) {
                                return new Intl.NumberFormat('vi-VN').format(value) + 'đ';
                            }
                        }
                    }]
                },
                tooltips: {
                     callbacks: {
                        label: function(tooltipItem, data) {
                            return new Intl.NumberFormat('vi-VN').format(tooltipItem.yLabel) + 'đ';
                        }
                    }
                }
            }

            // This will get the first returned node in the jQuery collection.
            new Chart(revenueChartCanvas, {
                type: 'bar', // or 'line'
                data: revenueChartData,
                options: revenueChartOptions
            })

            // ---------------------------
            // - ORDER STATUS CHART -
            // ---------------------------
            var donutChartCanvas = $('#orderStatusChart').get(0).getContext('2d')
            var donutData = {
                labels: @json($chartStatusLabels),
                datasets: [
                    {
                        data: @json($chartStatusData),
                        backgroundColor : @json($chartStatusColors),
                    }
                ]
            }
            var donutOptions = {
                maintainAspectRatio: false,
                responsive: true,
            }
            new Chart(donutChartCanvas, {
                type: 'doughnut',
                data: donutData,
                options: donutOptions
            })
        })
    </script>
@endpush