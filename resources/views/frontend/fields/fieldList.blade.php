@extends('layouts.master')
@section('title', $pageTitle)

@push('css')
<link rel="stylesheet" href="{{ asset('css/product.css') }}">
@endpush

@section('content')

{{-- Phần Banner --}}
<div class="banner">
    <div class="banner">
        <img src="{{ optional($current_category->bannerImage())->url() ?? '' }}" alt="{{$current_category->name}}">
    </div>
    <div class="container">
        <h2 class="custom-section-title">{{$current_category->name}}</h2>
        <div class="fieldList">
            <div class="row">
            @foreach($fields as $field)
                <div class="col-6 col-md-4">
                    <div class="fieldItem">
                        <a href="{{route("frontend.slug.handle",$field->slug)}}">
                            {{$field->name}}
                        </a>
                        <p class="field-meta">
                            <span><i class="fa-solid fa-calendar"></i> {{ $field->updated_at->format('d/m/Y') }}</span>
                        </p>
                        <div class="field-description">
                            {{$field->description}}
                        </div>
                        <div class="field-image">
                            <a href="{{route("frontend.slug.handle",$field->slug)}}">
                                <img src="{{ optional($field->mainImage())->url() }}" alt="{{$field->name}}">
                            </a>
                        </div>
                    </div>     
                </div>
            @endforeach
            </div>
                             
        </div>
    </div>
@endsection