@props([
    'before', // ảnh trước
    'after',  // ảnh sau
    'width' => '100%',
    'height' => '300px',
])

<div class="beerslider-wrapper" style="max-width: {{ $width }}; height: {{ $height }};">
    <div class="beer-slider" data-beer-label="Trước" style="max-height: 100%;">
        <img src="{{ asset($before) }}" alt="Ảnh Trước"/>
        <div class="beer-reveal" data-beer-label="Sau">
            <img src="{{ asset($after) }}" alt="Ảnh Sau"/>
        </div>
    </div>
</div>

@once
    @push('css')
        <link rel="stylesheet" href="{{ asset('css/BeerSlider.css') }}">
        <style>
            .beer-reveal>img:first-child{
                height:100%;
            }
        </style>
    @endpush

    @push('js')
        <script src="{{ asset('js/BeerSlider.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('.beer-slider').forEach(function (el) {
                    new BeerSlider(el);
                });
            });
        </script>
    @endpush
@endonce
