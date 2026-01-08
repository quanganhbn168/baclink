<div class="post-item">
    <div class="post-image">
        <a href="{{ route('frontend.slug.handle', $post->slug) }}">
            <img src="{{ optional($post->mainImage())->url() ?? asset('images/setting/no-image.png') }}"
                 alt="{{ $post->title }}"
                 loading="lazy">
        </a>
    </div>
    <div class="post-info">
        @if($post->category)
        <div class="post-category mb-2">
            <a href="{{ route('frontend.slug.handle', $post->category->slug) }}" class="text-gold text-bold">
                {{ $post->category->name }}
            </a>
        </div>
        @endif
        <h3 class="post-title">
            <a href="{{ route('frontend.slug.handle', $post->slug) }}">
                {{ $post->title }}
            </a>
        </h3>
        <p class="post-description">
            {{ Str::limit($post->description, 100) }}
        </p>
        <div class="news-card__cta mt-auto">
            <a href="{{ route('frontend.slug.handle', $post->slug) }}" class="text-bold text-gold">
                Xem thêm <i class="fa-solid fa-arrow-right ml-1"></i>
            </a>
            @if($post->category)
            <div class="mt-2">
                <a href="{{ route('frontend.slug.handle', $post->category->slug) }}" class="small text-muted">
                    Xem thêm về {{ $post->category->name }} <i class="fa-solid fa-chevron-right ml-1" style="font-size: 0.7rem;"></i>
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
