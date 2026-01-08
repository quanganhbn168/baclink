@extends('layouts.admin')

@section('title', $pageTitle)
@section('content_header_title', $pageTitle)

@section('content')
    <form action="{{ route('admin.agents.store') }}" method="POST">
        @csrf
        {{-- Gọi file form dùng chung --}}
        @include('admin.agents._form')
    </form>
@endsection
