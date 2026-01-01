<div class="footer-top">
    <div class="footer-top_right">
        <div class="top_right-image">
            <img src="{{ asset('images/setting/logo-t.png') }}" alt="Ekokemika Việt Nam">
        </div>
        <p class="top_right-text">
            Dung dịch rửa xe không chạm hàng đầu thế giới
        </p>
    </div>
    <ul class="footer-top-contact">
        <li><a href="{{$setting->facebook}}"><i class="fa-brands fa-facebook-f"></i></a></li>
        <li><a href="{{$setting->tiktok}}"><i class="fa-brands fa-tiktok"></i></a></li>
        <li><a href="{{$setting->instagram}}"><i class="fa-brands fa-instagram"></i></a></li>
        <li><a href="{{$setting->youtube}}"><i class="fa-brands fa-youtube"></i></a></li>
    </ul>
</div>
<footer class="footer-new">
    <div class="main-footer">
        <div class="container">
            <div class="row">
                <div class="col-12 col-lg-3">
                    <h2 class="footer-title">Thông tin liên hệ</h2>
                    <p class="footer-info">{{$setting->address}}</p>
                    <p class="footer-info"><i class="fa-solid fa-envelope"></i> {{$setting->email}} </p>
                    <p class="footer-info"><i class="fa-solid fa-phone"></i> {{$setting->phone}}</p>
                </div>
                <div class="col-12 col-lg-3">
                    <h2 class="footer-title">Chính sách bảo mật</h2>
                    <ul>
        {{-- Kiểm tra có menu và có items không để tránh lỗi --}}
        @if(isset($footerMenu) && $footerMenu->items->isNotEmpty())
            
            @foreach($footerMenu->items as $item)
                <li>
                    {{-- 
                        $item->link   : Tự động lấy URL chuẩn (Logic Accessor trong Model)
                        $item->title  : Tên hiển thị
                        $item->target : _self hoặc _blank 
                    --}}
                    <a href="{{ $item->link }}" target="{{ $item->target }}">
                        {{ $item->title }}
                    </a>
                </li>
            @endforeach

        @endif
    </ul>
                </div>
                <div class="col-12 col-lg-3">
                    <h2 class="footer-title">Bộ công thương</h2>
                </div>
                <div class="col-12 col-lg-3">
                    <h2 class="footer-title">Đăng ký nhận email</h2>
                    <div class="footer-description">
                        Đăng ký bản tin của chúng tôi để nhận được thông tin cập nhật và tin tức mới nhất
                    </div>
                    <div class="footer-form">
                        <form action="{{ route('newsletter.subscribe') }}" method="POST">
                            @csrf
                            <input type="email" required placeholder="Nhập địa chỉ Email của bạn vào">
                            <button type="submit">
                                <i class="fa-solid fa-right-long"></i>
                            </button>
                        </form>
                    </div>      
                </div>
            </div>
        </div>
    </div>
    <div class="copyright">
        <div class="container">
            <span>© Bản quyền thuộc về <b>{{$setting->name}}</b> | Cung cấp bởi <a href="https://webappbacninh.vn/" rel="nofollow" target="_blank">Webapp Bắc Ninh</a></span>
        </div>
    </div>
</footer>
