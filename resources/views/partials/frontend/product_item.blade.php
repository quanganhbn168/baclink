<div class="product-item">
    <div class="product-image">
        <a href="{{ route('frontend.slug.handle', $product->slug) }}">
            <img src="{{ optional($product->mainImage())->url() ?? asset('images/no-image.png') }}" 
                 alt="{{ $product->name }}" 
                 loading="lazy">
        </a>
    </div>
    <div class="product-info">
        <h3 class="product-name">
            <a href="{{ route('frontend.slug.handle', $product->slug) }}">
                {{ $product->name }}
            </a>
        </h3>
        <div class="product-price">
            @if($product->price > 0)
                {{ number_format($product->price, 0, ',', '.') }}đ
            @else
                Liên hệ
            @endif
        </div>
    </div>
</div>