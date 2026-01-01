<div class="post-item">
    <div class="post-image">
        <a href="{{ route('frontend.slug.handle', $post->slug) }}">
            <img src="{{ optional($post->mainImage())->url() ?? asset('images/setting/no-image.png') }}"
                 alt="{{ $post->title }}"
                 loading="lazy">
        </a>
    </div>
    <div class="post-info">
        <h3 class="post-title">
            <a href="{{ route('frontend.slug.handle', $post->slug) }}">
                {{ $post->title }}
            </a>
        </h3>
        <p class="post-description">
            {{ Str::limit($post->description, 100) }}
        </p>
        <div class="news-card__cta mt-auto">
            <a href="{{ route('frontend.slug.handle', $post->slug) }}" class="text-bold text-red">
                Xem thêm <i class="fa-solid fa-arrow-right ml-1"></i>
            </a>
        </div>
    </div>
</div>