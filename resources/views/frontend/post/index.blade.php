@extends('layouts.master')

@section('title', 'Tin tức')

@push('css')
<link rel="stylesheet" href="{{ asset('css/post.css') }}">
@endpush

@section('content')

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb bg-light px-3 py-2">
            <li class="breadcrumb-item">
                <a href="{{ url('/') }}">
                    <i class="fas fa-home"></i> Trang chủ
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Tin tức</li>
        </ol>
    </nav>

    <div class="container">
        <h2 class="section-title mb-4">Tin tức</h2>

        <div class="row">
            @forelse($posts as $post)
                <div class="col-12 col-md-6 col-lg-4 mb-4">
                    @include('partials.frontend.post_item', ['post' => $post])
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-light text-center mb-0 py-4">
                        Chưa có bài viết nào.
                    </div>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-center mt-4">
            {{ $posts->links() }}
        </div>

    </div>

@endsection

@push('js')
{{-- Nếu cần thêm JS sau này --}}
@endpush
