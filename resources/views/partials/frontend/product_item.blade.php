<div class="product_item">
    <div class="product_item-img">
        <a href="{{ route('frontend.slug.handle', $product->slug) }}">
            <img class="main-image" 
            src="{{ optional($product->mainImage())->url() ?? asset('images/no-image.png') }}" 
            alt="{{ $product->name }}" 
            loading="lazy">
        </a>
    </div>
    <div class="product_item-name">
        <a href="{{ route('frontend.slug.handle', $product->slug) }}">
            {{ $product->name }}
        </a>
    </div>
    <div class="product_item-info">
        <div class="product_item-price">
            @if($product->price > 0)
                {{ number_format($product->price, 0, ',', '.') }}đ
            @else
                Giá: Liên hệ
            @endif
        </div>
        <a href="{{ route('frontend.slug.handle', $product->slug) }}" class="product_item-button">
            Xem chi tiết
        </a>
    </div>
</div>
@once
@push('css')
<style>
.product_item {
    background-color: #fff;
    border-radius: 8px; 
    overflow: hidden; 
    display: flex;
    flex-direction: column;
    text-align: center; 
    border: 1px solid #f0f0f0;
    transition: box-shadow 0.3s ease;
}
.product_item:hover {
    box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
}
.product_item-img {
    position: relative;
    aspect-ratio: 1 / 1; 
    overflow: hidden; 
    margin-bottom: 15px;
}
.product_item-img .main-image {
    width: 100%;
    height: 100%;
    object-fit: contain; 
    transition: transform 0.3s ease; 
}
.product_item:hover .product_item-img .main-image {
    transform: scale(1.05); 
}
.product_item-name {
    padding: 0 15px;
    margin-bottom: 15px;
    flex-grow: 1; 
}
.product_item-name a {
    color: #333;
    font-weight: 700;
    text-decoration: none;
    font-size: 1.1rem;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    min-height: calc(1.1rem * 1.5 * 2); 
    line-height: 1.5;
}
.product_item-info {
    padding: 0 15px 20px 15px;
}
.product_item-price {
    color: var(--red);
    font-weight: 700;
    font-size: 1.25rem;
    margin-bottom: 15px;
}
.product_item-button {
    display: inline-block;
    padding: 10px 15px;
    width: 100%;
    background-color: #f3f4f6; 
    color: #1f2937;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
}
.product_item-button:hover {
    background-color: var(--blue); 
    color: #fff;
}
</style>
@endpush
@endonce