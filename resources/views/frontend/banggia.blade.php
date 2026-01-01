@extends('layouts.master')

@section('title', $postCategory->name)
@push('css')
<link rel="stylesheet" href="{{ asset('css/product.css') }}">
@endpush

@section('content')
<main class="banggia-page bg-light" id="main-content">
    <div class="container">
        <div class="row">
            <!-- MAIN CONTENT -->
            <div class="col-md-9 bg-white">
                <article class="pricing-content">
                    <header class="section-header">
                        <h1 class="section-title">{{ $postCategory->title }}</h1>
                        @if($postCategory->description)
                            <p class="section-description">{{ $postCategory->description }}</p>
                        @endif
                    </header>

                    <section class="content-body">
                        {!! $postCategory->content !!}
                        <h3 class="display-4">Sau đây là bảng chi tiết dịch vụ</h3>
                        @foreach($serviceCategory as $key => $serviceCate)
                        <h3 class="display-4">{{$serviceCate->title}}</h3>
                        <div class="mt-2">
                            {{$serviceCate->description}}
                        </div>
                        <div class="banggia">
                            <table class="table table-bordered banggia-table">
                                <thead class="thead-primary bg-primary text-white">
                                    <tr>
                                        <th scope="col">STT</th>
                                        <th scope="col">Tên dịch vụ</th>
                                        <th scope="col">Đơn vị tính</th>
                                        <th scope="col">Giá tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($serviceCate->services as $key => $child)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>
                                            <a href="{{route('slug.resolve',$child->slug)}}">
                                                {{ $child->name }}
                                            </a>
                                        </td>
                                        <td>{{ $child->unit->name ?? '' }}</td>
                                        <td>{{ number_format($child->price, 0, ',', '.') }}₫</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>          
                        @endforeach
                    </section>
                </article>
            </div>

            @include('partials.frontend.aside')
        </div>
    </div>
</main>
@endsection
