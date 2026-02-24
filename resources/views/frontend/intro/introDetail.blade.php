@extends('layouts.master')
@section('title', __('Giới thiệu'))
@section('meta_image',$setting->share_image)
@section('content')
<div class="container mt-3">
    <x-frontend.breadcrumb :items="[
        ['label' => $intro->getTranslation('title'), 'url' => '']
    ]" />
</div>
<section class="section py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h1 class="section-title text-uppercase">{{ __('Giới thiệu chung về') }} {{$setting->name}}</h1>
        </div>
        <div class="content">
            <div class="row">
                <div class="col-12 col-md-9">
                    {!!$intro->content!!}
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
