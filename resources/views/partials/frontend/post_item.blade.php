<article class="news-card">
    <a href="{{ route('frontend.slug.handle', $post->slug) }}" class="news-card__thumb">
        <img src="{{ optional($post->mainImage())->url() ?? asset('images/setting/no-image.png') }}"
        alt="{{ $post->title }}">
    </a>

    <div class="news-card__info">
        <h3 class="news-card__title">
            <a href="{{ route('frontend.slug.handle', $post->slug) }}">
                {{ $post->title }}
            </a>
        </h3>

        <p class="news-card__desc">
            {{ $post->description }}
        </p>

        <div class="news-card__cta">
            <a href="{{ route('frontend.slug.handle', $post->slug) }}">
                <i class="fa-solid fa-right-long"></i> Xem thêm
            </a>
        </div>
    </div>
</article>
@once
@push('css')
<style>
/* ===== News Cards ===== */
.news-card { position: relative; }

.news-card__thumb {
    display: block;
    position: relative;
    height: 0;
    padding-top: 56.25%; /* 16:9 */
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 10px 24px rgba(0,0,0,.12);
}
.news-card__thumb img{
    position: absolute; inset: 0;
    width: 100%; height: 100%;
    object-fit: cover;
    transition: transform .35s ease;
}
.news-card:hover .news-card__thumb img{ transform: scale(1.04); }

/* Hộp thông tin chồng lên ảnh */
.news-card__info{
    position: relative;
    width: 88%;
    margin: -40px auto 0;        /* đẩy chồng lên ảnh */
    background: #fff;
    border-radius: 12px;
    padding: 20px 22px;
    box-shadow: 0 14px 30px rgba(0,0,0,.14);
}

/* Tiêu đề in đậm, uppercase, cắt dòng */
.news-card__title{
    margin: 0 0 8px 0;
    font-weight: 700;
    text-transform: uppercase;
    line-height: 1.2;
    font-size: 1rem;
}
.news-card__title a{ color: #111; }
.news-card__title a:hover{ text-decoration: none; }

/* Cắt 2-3 dòng tuỳ viewport */
.news-card__title,
.news-card__desc{
    display: -webkit-box;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.news-card__title{ -webkit-line-clamp: 2; }
.news-card__desc{
    color: #556070;
    font-size: .95rem;
    margin: 0 0 14px 0;
    -webkit-line-clamp: 3;
}

/* CTA */
.news-card__cta{
    padding-top: 12px;
    border-top: 1px solid #eee;
    text-align: right;
}
.news-card__cta a{
    color: #111;
    font-weight: 600;
}
.news-card__cta i{ margin-right: 6px; }

/* Responsive tinh chỉnh một chút */
@media (min-width: 992px){
    .news-card__info{ width: 84%; margin-top: -48px; }
}

</style>
@endpush
@endonce