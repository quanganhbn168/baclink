@extends('layouts.master')
@section('title', __('Liên hệ') . ' - ' . $setting->name)
@section('meta_description', __('Liên hệ') . ' - ' . $setting->address . ' - ' . $setting->phone)
@section('meta_image', $setting->share_image)

@push('css')
<style>
/* ===== CONTACT PAGE - PREMIUM DESIGN ===== */
.contact-hero {
    background: linear-gradient(135deg, var(--blue, #003d7a) 0%, #001f3f 100%);
    padding: 60px 0 40px;
    color: #fff;
    position: relative;
    overflow: hidden;
}
.contact-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 600px;
    height: 600px;
    background: rgba(255,255,255,0.03);
    border-radius: 50%;
}
.contact-hero::after {
    content: '';
    position: absolute;
    bottom: -40%;
    left: -10%;
    width: 400px;
    height: 400px;
    background: rgba(255,255,255,0.02);
    border-radius: 50%;
}
.contact-hero h1 {
    font-size: 2.2rem;
    font-weight: 800;
    letter-spacing: -0.5px;
    margin-bottom: 10px;
}
.contact-hero p {
    font-size: 1.1rem;
    opacity: 0.85;
    max-width: 600px;
}

/* Info cards */
.contact-info-section {
    margin-top: -40px;
    position: relative;
    z-index: 2;
}
.info-card {
    background: #fff;
    border-radius: 14px;
    padding: 28px 24px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.08);
    text-align: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    height: 100%;
    border: 1px solid rgba(0,0,0,0.04);
}
.info-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 16px 40px rgba(0,0,0,0.12);
}
.info-card .icon-wrap {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--blue, #003d7a), #0065c1);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 16px;
    color: #fff;
    font-size: 22px;
}
.info-card h5 {
    font-weight: 700;
    font-size: 1rem;
    color: #333;
    margin-bottom: 8px;
}
.info-card p,
.info-card a {
    color: #666;
    font-size: 0.95rem;
    margin: 0;
    text-decoration: none;
    word-break: break-word;
}
.info-card a:hover {
    color: var(--blue, #003d7a);
}

/* Form section */
.contact-form-section {
    padding: 60px 0;
}
.form-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.08);
    overflow: hidden;
    border: 1px solid rgba(0,0,0,0.04);
}
.form-card .form-header {
    background: linear-gradient(135deg, var(--blue, #003d7a) 0%, #001f3f 100%);
    color: #fff;
    padding: 30px 36px;
}
.form-card .form-header h3 {
    font-weight: 700;
    font-size: 1.4rem;
    margin-bottom: 6px;
}
.form-card .form-header p {
    opacity: 0.8;
    font-size: 0.95rem;
    margin: 0;
}
.form-card .form-body {
    padding: 36px;
}
.form-card .form-group label {
    font-weight: 600;
    color: #333;
    margin-bottom: 6px;
    font-size: 0.9rem;
}
.form-card .form-control {
    border-radius: 10px;
    border: 1.5px solid #e0e0e0;
    padding: 12px 16px;
    font-size: 0.95rem;
    transition: border-color 0.3s, box-shadow 0.3s;
}
.form-card .form-control:focus {
    border-color: var(--blue, #003d7a);
    box-shadow: 0 0 0 3px rgba(0,61,122,0.1);
}
.form-card textarea.form-control {
    min-height: 120px;
    resize: vertical;
}
.btn-contact-submit {
    background: linear-gradient(135deg, var(--blue, #003d7a), #0065c1);
    color: #fff;
    border: none;
    border-radius: 10px;
    padding: 14px 32px;
    font-weight: 700;
    font-size: 1rem;
    letter-spacing: 0.3px;
    transition: all 0.3s ease;
    width: 100%;
}
.btn-contact-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,61,122,0.3);
    color: #fff;
}
.btn-contact-submit:active {
    transform: translateY(0);
}

/* Map */
.contact-map-section {
    padding: 0 0 0;
}
.map-wrapper {
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 8px 30px rgba(0,0,0,0.08);
    border: 1px solid rgba(0,0,0,0.04);
}
.map-wrapper iframe {
    width: 100% !important;
    height: 400px;
    display: block;
}

/* Sidebar info */
.contact-sidebar-card {
    background: #f8fafc;
    border-radius: 14px;
    padding: 28px;
    border: 1px solid #eef2f7;
    height: 100%;
}
.contact-sidebar-card h4 {
    font-weight: 700;
    color: var(--blue, #003d7a);
    font-size: 1.15rem;
    margin-bottom: 20px;
    padding-bottom: 14px;
    border-bottom: 2px solid #eef2f7;
}
.sidebar-info-item {
    display: flex;
    align-items: flex-start;
    margin-bottom: 18px;
}
.sidebar-info-item:last-child {
    margin-bottom: 0;
}
.sidebar-info-item .si-icon {
    width: 40px;
    height: 40px;
    min-width: 40px;
    border-radius: 10px;
    background: linear-gradient(135deg, var(--blue, #003d7a), #0065c1);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 16px;
    margin-right: 14px;
}
.sidebar-info-item .si-text strong {
    display: block;
    font-size: 0.85rem;
    color: #888;
    font-weight: 600;
    margin-bottom: 3px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.sidebar-info-item .si-text span,
.sidebar-info-item .si-text a {
    color: #333;
    font-size: 0.95rem;
    text-decoration: none;
}
.sidebar-info-item .si-text a:hover {
    color: var(--blue, #003d7a);
}

/* Branches */
.branch-list {
    margin-top: 24px;
}
.branch-item {
    background: #fff;
    border-radius: 10px;
    padding: 14px 16px;
    margin-bottom: 10px;
    border: 1px solid #eef2f7;
    transition: box-shadow 0.2s;
}
.branch-item:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
}
.branch-item strong {
    color: var(--blue, #003d7a);
    font-size: 0.9rem;
    display: block;
    margin-bottom: 4px;
}
.branch-item span {
    color: #666;
    font-size: 0.88rem;
}

/* Mobile */
@media (max-width: 767px) {
    .contact-hero { padding: 40px 0 30px; }
    .contact-hero h1 { font-size: 1.6rem; }
    .contact-info-section { margin-top: -25px; }
    .info-card { padding: 20px 16px; }
    .form-card .form-body { padding: 24px; }
    .form-card .form-header { padding: 24px; }
}
</style>
@endpush

@section('content')

{{-- HERO SECTION --}}
<section class="contact-hero">
    <div class="container">
        <x-frontend.breadcrumb :items="[['label' => __('Liên hệ')]]"/>
        <h1>{{ __('Liên hệ') }}</h1>
        <p>{{ __('Chúng tôi luôn sẵn sàng lắng nghe và hỗ trợ bạn. Hãy liên hệ với chúng tôi ngay hôm nay.') }}</p>
    </div>
</section>

{{-- INFO CARDS --}}
<section class="contact-info-section">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="info-card">
                    <div class="icon-wrap"><i class="fas fa-map-marker-alt"></i></div>
                    <h5>{{ __('Địa chỉ') }}</h5>
                    <p>{{ $setting->address }}</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="info-card">
                    <div class="icon-wrap"><i class="fas fa-phone-alt"></i></div>
                    <h5>{{ __('Điện thoại') }}</h5>
                    <p><a href="tel:{{ preg_replace('/\s+/', '', $setting->phone) }}">{{ $setting->phone }}</a></p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="info-card">
                    <div class="icon-wrap"><i class="fas fa-envelope"></i></div>
                    <h5>Email</h5>
                    <p><a href="mailto:{{ trim($setting->email) }}">{{ $setting->email }}</a></p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- FORM + SIDEBAR --}}
<section class="contact-form-section">
    <div class="container">
        <div class="row">
            {{-- Form --}}
            <div class="col-lg-8 mb-4">
                <div class="form-card">
                    <div class="form-header">
                        <h3><i class="fas fa-paper-plane mr-2"></i>{{ __('Liên hệ ngay với chúng tôi') }}</h3>
                        <p>{{ __('Vui lòng điền thông tin bên dưới, chúng tôi sẽ phản hồi sớm nhất có thể.') }}</p>
                    </div>
                    <div class="form-body">
                        <form id="contact-form" action="{{ route('contact.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">{{ __('Họ và tên') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="name" name="name" placeholder="{{ __('Nhập họ và tên') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone">{{ __('Số điện thoại') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="phone" name="phone" placeholder="{{ __('Nhập số điện thoại') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" placeholder="{{ __('Nhập email của bạn') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="address">{{ __('Địa chỉ') }}</label>
                                        <input type="text" class="form-control" id="address" name="address" placeholder="{{ __('Nhập địa chỉ') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="message">{{ __('Ý kiến') }} <span class="text-danger">*</span></label>
                                <textarea name="message" id="message" class="form-control" placeholder="{{ __('Nhập nội dung tin nhắn của bạn...') }}"></textarea>
                            </div>
                            <button type="submit" class="btn btn-contact-submit mt-3">
                                <i class="fas fa-paper-plane mr-2"></i>{{ __('Gửi ý kiến') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="col-lg-4 mb-4">
                <div class="contact-sidebar-card">
                    <h4><i class="fas fa-building mr-2"></i>{{ $setting->name }}</h4>
                    
                    <div class="sidebar-info-item">
                        <div class="si-icon"><i class="fas fa-map-marker-alt"></i></div>
                        <div class="si-text">
                            <strong>{{ __('Địa chỉ') }}</strong>
                            <span>{{ $setting->address }}</span>
                        </div>
                    </div>

                    <div class="sidebar-info-item">
                        <div class="si-icon"><i class="fas fa-phone-alt"></i></div>
                        <div class="si-text">
                            <strong>{{ __('Điện thoại') }}</strong>
                            <a href="tel:{{ preg_replace('/\s+/', '', $setting->phone) }}">{{ $setting->phone }}</a>
                        </div>
                    </div>

                    <div class="sidebar-info-item">
                        <div class="si-icon"><i class="fas fa-envelope"></i></div>
                        <div class="si-text">
                            <strong>Email</strong>
                            <a href="mailto:{{ trim($setting->email) }}">{{ $setting->email }}</a>
                        </div>
                    </div>

                    @if($branches->count() > 0)
                    <div class="branch-list">
                        <h5 style="font-weight: 700; font-size: 0.95rem; color: #555; margin-bottom: 12px;">
                            <i class="fas fa-code-branch mr-1"></i> {{ __('Chi nhánh') }}
                        </h5>
                        @foreach($branches as $branch)
                        <div class="branch-item">
                            <strong>{{ $branch->name }}</strong>
                            <span>{{ $branch->address }}</span>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

{{-- MAP --}}
@if($setting->map)
<section class="contact-map-section pb-5">
    <div class="container">
        <div class="map-wrapper">
            {!! $setting->map !!}
        </div>
    </div>
</section>
@endif

@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
<script>
    $.validator.addMethod("phoneVN", function (value, element) {
        return this.optional(element) || /^(0[3|5|7|8|9])[0-9]{8}$|^\+84[3|5|7|8|9][0-9]{8}$/.test(value);
    }, "Số điện thoại không hợp lệ");
    $(document).ready(function () {
        $('#contact-form').validate({
            rules: {
                name: { required: true, minlength: 2 },
                phone: { required: true, phoneVN: true },
                email: { email: true },
                message: { required: true, maxlength: 1000 }
            },
            messages: {
                name: { required: "Vui lòng nhập họ và tên", minlength: "Tên quá ngắn" },
                phone: { required: "Vui lòng nhập số điện thoại", phoneVN: "Số điện thoại không hợp lệ" },
                email: { email: "Email không hợp lệ" },
                message: { required: "Vui lòng nhập nội dung", maxlength: "Tối đa 1000 ký tự" }
            },
            errorElement: 'small',
            errorClass: 'text-danger',
            highlight: function (element) { $(element).addClass('is-invalid'); },
            unhighlight: function (element) { $(element).removeClass('is-invalid'); }
        });
    });
</script>
@endpush
