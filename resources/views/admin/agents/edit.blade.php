@extends('layouts.admin')

@section('title', $pageTitle)
@section('content_header_title', $pageTitle)

@section('content')
    <form action="{{ route('admin.agents.update', $agent->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        {{-- Gọi file form dùng chung, truyền biến $agent vào --}}
        @include('admin.agents._form', ['agent' => $agent])
    </form>
@endsection