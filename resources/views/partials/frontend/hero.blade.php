<section class="hero">
    <img src="{{ asset('images/herosection/background.jpg') }}" alt="{{ $setting->name }}" class="hero__bg">
    
    <div class="hero__overlay"></div>

    <div class="hero__container">
        <div class="hero__content">
            <h2 class="hero__title">
                Cộng đồng các Doanh nghiệp sản xuất<br>
                sản phẩm công nghiệp chủ lực<br>
                Thành phố Bắc Ninh
            </h2>
            
            <p class="hero__subtitle">Gắn kết – Tiên phong</p>

            <a href="{{ route('register') }}" class="hero__btn">
                <div class="hero__btn-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div class="hero__btn-text">
                    <span class="hero__btn-main">ĐĂNG KÝ HỘI VIÊN</span>
                    <span class="hero__btn-sub">Hội công nghiệp chủ lực Bắc Ninh</span>
                </div>
            </a>
        </div>
    </div>
</section>
@push('css')
<style>
    /* Block chính */
.hero {
    position: relative;
    width: 100%;
    height: 80vh; /* Hoặc chiều cao cố định anh muốn */
    min-height: 500px;
    overflow: hidden;
    display: flex;
    align-items: center;
    font-family: var(--font-secondary);
}

/* Ảnh nền phủ kín */
.hero__bg {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    z-index: -1;
}

/* Lớp phủ để làm sáng phần text bên trái (giống ảnh) */
.hero__overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(to right, rgba(255,255,255,0.9) 20%, rgba(255,255,255,0.2) 100%);
    z-index: 0;
}

.hero__container {
    max-width:1200px;
    margin: 0 auto;
    padding: 0 15px;
    position: relative;
    z-index: 1;
    width: 100%;
}

.hero__content {
    max-width: 650px; /* Giới hạn độ rộng để text tự xuống dòng */
}

.hero__title {
    font-size: 42px;
    font-weight: 800;
    color: #1a2a5a; /* Màu xanh đen đặc trưng */
    line-height: 1.2;
    margin-bottom: 20px;
    text-transform: none;
}

.hero__subtitle {
    font-size: 48px;
    font-weight: 700;
    color: #e32124; /* Màu đỏ */
    margin-bottom: 30px;
    text-transform: uppercase;
}

/* Button theo phong cách trong ảnh */
.hero__btn {
    display: inline-flex;
    align-items: center;
    background-color: #e32124;
    color: #fff;
    text-decoration: none;
    padding: 15px 25px;
    border-radius: 8px;
    transition: 0.3s;
}

.hero__btn:hover {
    background-color: #c41a1d;
    transform: translateY(-2px);
}

.hero__btn-icon {
    font-size: 30px;
    margin-right: 15px;
}

.hero__btn-main {
    display: block;
    font-size: 18px;
    font-weight: bold;
    text-transform: uppercase;
}

.hero__btn-sub {
    display: block;
    font-size: 12px;
    opacity: 0.9;
}
</style>
@endpush