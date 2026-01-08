<footer class="footer-new mt-0">
    <div class="main-footer pt-5 pb-4">
        <div class="container container-custom">
            <div class="row">
                <div class="col-12 col-lg-4 mb-4 footer-col-info">
                    <img src="{{ asset($setting->logo) }}" alt="{{ $setting->name }}" class="footer-logo mb-4">
                    <h2 class="footer-title">THÔNG TIN LIÊN HỆ</h2>
                    <p class="footer-info-item mb-1"><b>{{$setting->name}}</b></p>
                    <p class="footer-info-item mb-1"><i class="fa-solid fa-location-dot mr-2"></i> {{$setting->address}}</p>
                    <p class="footer-info-item mb-1"><i class="fa-solid fa-envelope mr-2"></i> {{$setting->email}} </p>
                    <p class="footer-info-item mb-1"><i class="fa-solid fa-phone mr-2"></i> {{$setting->phone}}</p>
                </div>
                <div class="col-12 col-lg-4 mb-4 footer-col-links">
                    <h2 class="footer-title">VỀ CHÚNG TÔI</h2>
                    <ul class="footer-links-list">
                        @if(isset($footerMenu) && $footerMenu->items->isNotEmpty())
                            @foreach($footerMenu->items as $item)
                                <li class="mb-2"><a href="{{ $item->link }}" target="{{ $item->target }}">{{ $item->title }}</a></li>
                            @endforeach
                        @else
                           <li class="mb-2"><a href="#">Giới thiệu</a></li>
                           <li class="mb-2"><a href="#">Hội viên tiêu biểu</a></li>
                           <li class="mb-2"><a href="#">Tin tức & Sự kiện</a></li>
                           <li class="mb-2"><a href="#">Liên hệ</a></li>
                        @endif
                    </ul>
                </div>
                <div class="col-12 col-lg-4 mb-4 footer-col-social">
                    <h2 class="footer-title">KẾT NỐI VỚI CHÚNG TÔI</h2>
                    <div class="footer-social-icons d-flex gap-3 mb-4">
                        <a href="{{$setting->facebook}}" class="social-icon-btn"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="{{$setting->youtube}}" class="social-icon-btn"><i class="fa-brands fa-youtube"></i></a>
                        <a href="{{$setting->zalo}}" class="social-icon-btn"><i class="fa fa-comment"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="copyright py-3">
        <div class="container text-center">
            <span class="copyright-text">© Bản quyền thuộc về <b>{{$setting->name}}</b> | Cung cấp bởi <a href="https://thtmedia.com.vn" rel="nofollow" target="_blank">THT MEDIA</a></span>
        </div>
    </div>
</footer>
