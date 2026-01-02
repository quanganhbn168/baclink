<footer class="footer-new mt-0" style="background-color: var(--bg-dark); color: #fff; border-top: 1px solid rgba(255,255,255,0.1);">
    <div class="main-footer pt-5 pb-4">
        <div class="container container-custom">
            <div class="row">
                <div class="col-12 col-lg-4 mb-4">
                    <img src="{{ asset($setting->logo) }}" alt="{{ $setting->name }}" class="mb-4" style="max-height: 60px; filter: brightness(0) invert(1);">
                    <h2 class="footer-title" style="color: #fff; font-family: var(--font-secondary); font-size: 20px; text-transform: uppercase;">THÔNG TIN LIÊN HỆ</h2>
                    <p class="footer-info mb-1" style="color: #eee;"><b>{{$setting->name}}</b></p>
                    <p class="footer-info mb-1" style="color: #ccc;"><i class="fa-solid fa-location-dot mr-2"></i> {{$setting->address}}</p>
                    <p class="footer-info mb-1" style="color: #ccc;"><i class="fa-solid fa-envelope mr-2"></i> {{$setting->email}} </p>
                    <p class="footer-info mb-1" style="color: #ccc;"><i class="fa-solid fa-phone mr-2"></i> {{$setting->phone}}</p>
                </div>
                <div class="col-12 col-lg-4 mb-4">
                    <h2 class="footer-title" style="color: #fff; font-family: var(--font-secondary); font-size: 20px; text-transform: uppercase;">VỀ CHÚNG TÔI</h2>
                    <ul class="footer-links-theme" style="padding: 0; list-style: none;">
                        @if(isset($footerMenu) && $footerMenu->items->isNotEmpty())
                            @foreach($footerMenu->items as $item)
                                <li class="mb-2"><a href="{{ $item->link }}" target="{{ $item->target }}" style="color: #ccc; transition: var(--transition);">{{ $item->title }}</a></li>
                            @endforeach
                        @else
                           <li class="mb-2"><a href="#" style="color: #ccc;">Giới thiệu</a></li>
                           <li class="mb-2"><a href="#" style="color: #ccc;">Hội viên tiêu biểu</a></li>
                           <li class="mb-2"><a href="#" style="color: #ccc;">Tin tức & Sự kiện</a></li>
                           <li class="mb-2"><a href="#" style="color: #ccc;">Liên hệ</a></li>
                        @endif
                    </ul>
                </div>
                <div class="col-12 col-lg-4 mb-4">
                    <h2 class="footer-title" style="color: #fff; font-family: var(--font-secondary); font-size: 20px; text-transform: uppercase;">KẾT NỐI VỚI CHÚNG TÔI</h2>
                    <div class="footer-social-theme d-flex gap-3 mb-4">
                        <a href="{{$setting->facebook}}" style="width: 40px; height: 40px; border-radius: 50%; background: #fff; color: var(--bg-dark); display: flex; align-items: center; justify-content: center;"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="{{$setting->youtube}}" style="width: 40px; height: 40px; border-radius: 50%; background: #fff; color: var(--bg-dark); display: flex; align-items: center; justify-content: center;"><i class="fa-brands fa-youtube"></i></a>
                        <a href="{{$setting->zalo}}" style="width: 40px; height: 40px; border-radius: 50%; background: #fff; color: var(--bg-dark); display: flex; align-items: center; justify-content: center;"><i class="fa fa-comment"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="copyright py-3" style="background-color: rgba(0,0,0,0.2); border-top: 1px solid rgba(255,255,255,0.05);">
        <div class="container text-center">
            <span style="color: #aaa; font-size: 14px;">© Bản quyền thuộc về <b>{{$setting->name}}</b> | Cung cấp bởi <a href="https://thtmedia.com.vn" rel="nofollow" target="_blank" style="color: #fff; font-weight: 700;">THT MEDIA</a></span>
        </div>
    </div>
</footer>
