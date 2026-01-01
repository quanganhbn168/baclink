@extends('layouts.master')
@section('title','Đăng ký đại lý Ekokemika')

@push('css')
<style>
    /* ====== BRAND BUTTONS: #16169c ====== */
:root { --brand-btn: #16169c; }

/* CTA riêng của trang */
.btn-cta{
  background: var(--brand-btn) !important;
  border-color: var(--brand-btn) !important;
  color: #fff !important;
}
.btn-cta:hover,
.btn-cta:focus{
  filter: brightness(0.92);
  color:#fff !important;
  box-shadow: 0 0 0 .2rem rgba(22,22,156,.18);
}

/* Ghi đè Bootstrap .btn-primary để nút submit cũng đồng bộ */
.btn-primary{
  background: var(--brand-btn) !important;
  border-color: var(--brand-btn) !important;
  color:#fff !important;
}
.btn-primary:hover,
.btn-primary:focus{
  filter: brightness(0.92);
  color:#fff !important;
  box-shadow: 0 0 0 .2rem rgba(22,22,156,.18);
}

/* Nếu có <a class="btn ..."> đảm bảo chữ luôn trắng */
a.btn { color:#fff !important; }

/* ===== HERO ===== */
.hero-wrap{
  position:relative;
  border-radius:22px;           /* bo góc nền như desktop */
  overflow:hidden;
}
.hero-wrap img{
  width:100%;
  height:420px;
  object-fit:cover;
}
.hero-content{
  position:absolute;
  top:50%;
  left:10%;                     /* đẩy card về trái */
  transform:translateY(-50%);   /* căn giữa theo trục dọc */
  width:100%;
  max-width:720px;              /* bề rộng card ở desktop */
  padding:0 15px;
}
.hero-card{
  background:#fff;
  border-radius:16px;
  box-shadow:0 20px 40px rgba(0,0,0,.18);
  padding:28px 26px;
  text-align:left;              /* desktop: trái */
}
.hero-card h2{
  font-weight:800;
  font-size:2.25rem;
  line-height:1.2;
  margin-bottom:14px;
}
.hero-card p{
  margin-bottom:12px;
  font-size:1.05rem;
}
.btn-cta{
  background:#1e2fae;
  border:none;
  font-weight:700;
  padding:.9rem 1.6rem;
  border-radius:10px;
}

/* ≥ md (tablet trở lên) tăng chiều cao ảnh */
@media (min-width:768px){
  .hero-wrap img{ height:600px; }
}

/* ===== Mobile (≤ 767.98px) ===== */
@media (max-width:767.98px){
  .hero-wrap{ border-radius:0; }            /* full-bleed giống ảnh 2 */
  .hero-wrap img{ height:260px; }

  .hero-content{
    position:static;                         /* bỏ overlay -> card xuống dưới ảnh */
    transform:none;
    left:auto;
    max-width:100%;
    padding:0 15px;
  }
  .hero-card{
    margin-top:20px;                        /* kéo card chạm mép ảnh như mẫu */
    border-radius:16px;
    text-align:center;                       /* mobile: center */
    padding:22px 18px;
  }
  .hero-card h2{
    font-size:1.6rem;
    line-height:1.25;
  }
  .hero-card p{ font-size:1rem; }
}

/* ===== BENEFITS (desktop tabs + mobile accordion) ===== */
.benefit-wrap{background:#f5f7fa;border-radius:20px;padding:20px}
.benefit-pills .nav-link{
  background:#fff;border:2px solid #e8ecf3;border-radius:16px;
  box-shadow:0 8px 20px rgba(0,0,0,.06);
  padding:20px 18px; min-height:110px; text-align:center;
  font-weight:700; color:#111; transition:all .2s;
}
.benefit-pills .nav-link img{height:40px;margin-bottom:6px;display:block;margin-left:auto;margin-right:auto}
.benefit-pills .nav-link.active,
.benefit-pills .nav-link:focus{
  border-color:#2a3dd7; box-shadow:0 10px 24px rgba(42,61,215,.18);
}
.benefit-pane{
  background:#fff;border:2px solid #e8ecf3;border-radius:16px;
  box-shadow:0 10px 24px rgba(0,0,0,.06);
  padding:22px 20px; margin-top:16px;
}
.benefit-pane .title{color:#1f34c5;font-weight:800;font-size:1.6rem}

/* Switch hiển thị: desktop dùng tabs, mobile dùng accordion */
@media (max-width:767.98px){
  .benefit-tabs-only{display:none !important;}
}
@media (min-width:768px){
  .benefit-accordion-only{display:none !important;}
}

/* Mobile accordion card look */
.benefit-accordion .card{
  border-radius:16px; overflow:hidden; border:2px solid #e8ecf3;
  box-shadow:0 8px 20px rgba(0,0,0,.06); margin-bottom:12px;
}
.benefit-accordion .card-header{
  background:#fff; padding:16px 18px; cursor:pointer;
}
.benefit-accordion .card-header .head{
  display:flex; align-items:center; justify-content:space-between; font-weight:700;
}
.benefit-accordion .card-header img{height:34px; margin-right:10px}
.benefit-accordion .card-body{background:#fff}

/* FORM */
.section-muted{background:#f4f6f8;border-radius:16px;padding:18px}
.form-desc{font-size:.95rem;color:#4b5563}
.btn-cta{background:#1e2fae;border:none;font-weight:700}
</style>
@endpush

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-light px-3 py-2">
            <li class="breadcrumb-item">
                <a href="{{ url('/') }}">
                    <i class="fas fa-home"></i> Trang chủ
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Đăng ký làm đại lý</li>
        </ol>
    </nav>
<section class="hero-wrap mb-4 p-0">
    <img src="https://ekokemika.com.vn/upload/images/products/dung-dich-rua-xe/Distributors-map.jpg" alt="Đại lý">
    <div class="hero-content">
        <div class="hero-card">
            <h2>Đăng Ký Đại Lý Ekokemika: Cơ Hội Kinh Doanh Nước Rửa Xe Không Chạm Hàng Đầu Việt Nam</h2>
            <p>Bạn đang tìm kiếm cơ hội hợp tác kinh doanh bền vững trong ngành chăm sóc xe hơi? Bạn muốn trở thành <strong>đại lý nước rửa xe không chạm</strong> của một thương hiệu uy tín, dẫn đầu thị trường Việt Nam? Ekokemika mời bạn gia nhập hệ thống phân phối mạnh mẽ của chúng tôi.</p>
            <p>Chúng tôi không chỉ cung cấp sản phẩm chất lượng cao mà còn mang đến hệ sinh thái hỗ trợ toàn diện, giúp <strong>đại lý Ekokemika</strong> phát triển mạnh mẽ và bền vững.</p>
            <p class="mb-0"><a class="btn btn-cta btn-lg" href="#benefit">Khám phá lợi ích</a></p>
        </div>
    </div>
</section>
<div class="container py-4">
    {{-- HERO --}}

    {{-- BENEFITS --}}
    <section id="benefit" class="mb-4">
  <div class="text-center mb-3">
      <h2 class="mb-2">Lợi Ích Đặc Quyền Khi Trở Thành Đại Lý Ekokemika</h2>
      <p>Trở thành <strong>đối tác Ekokemika</strong> là lựa chọn thông minh với nhiều quyền lợi hấp dẫn:</p>
  </div>

  {{-- DESKTOP / TABLET: Tabs --}}
  <div class="benefit-tabs-only">
    <div class="row">
      <div class="col-12">
        <ul class="nav nav-pills justify-content-between flex-wrap benefit-pills" id="benefit-pills" role="tablist">
          <li class="nav-item mb-3" style="flex:1 1 180px; padding:0 8px">
            <a class="nav-link active" id="b0-tab" data-toggle="pill" href="#b0" role="tab">
              <img src="https://ekokemika.com.vn/upload/images/icon/icon1.png" alt=""> Chiết Khấu 45%
            </a>
          </li>
          <li class="nav-item mb-3" style="flex:1 1 180px; padding:0 8px">
            <a class="nav-link" id="b1-tab" data-toggle="pill" href="#b1" role="tab">
              <img src="https://ekokemika.com.vn/upload/images/icon/icon2.png" alt=""> Hệ Thống Dẫn Đầu
            </a>
          </li>
          <li class="nav-item mb-3" style="flex:1 1 180px; padding:0 8px">
            <a class="nav-link" id="b2-tab" data-toggle="pill" href="#b2" role="tab">
              <img src="https://ekokemika.com.vn/upload/images/icon/icon3.png" alt=""> Đào Tạo Chuyên Sâu
            </a>
          </li>
          <li class="nav-item mb-3" style="flex:1 1 180px; padding:0 8px">
            <a class="nav-link" id="b3-tab" data-toggle="pill" href="#b3" role="tab">
              <img src="https://ekokemika.com.vn/upload/images/icon/icon4.png" alt=""> Chứng Chỉ Chính Thức
            </a>
          </li>
          <li class="nav-item mb-3" style="flex:1 1 180px; padding:0 8px">
            <a class="nav-link" id="b4-tab" data-toggle="pill" href="#b4" role="tab">
              <img src="https://ekokemika.com.vn/upload/images/icon/icon5.png" alt=""> An Tâm Pháp Lý
            </a>
          </li>
        </ul>
      </div>

      <div class="col-12">
        <div class="tab-content">
          <div class="tab-pane fade show active benefit-pane" id="b0" role="tabpanel" aria-labelledby="b0-tab">
            <p class="title">1. Chiết Khấu Đại Lý Hấp Dẫn Đến 45% – Tối Ưu Lợi Nhuận</p>
            <p>Khi tham gia mạng lưới <strong>đại lý Ekokemika</strong>, bạn được hưởng <strong>chiết khấu đến 45%</strong>, tối đa hóa lợi nhuận và đảm bảo nguồn thu ổn định.</p>
          </div>
          <div class="tab-pane fade benefit-pane" id="b1" role="tabpanel" aria-labelledby="b1-tab">
            <p class="title">2. Gia Nhập Hệ Thống Nước Rửa Xe Không Chạm Mạnh Nhất Việt Nam</p>
            <p><strong>Ekokemika</strong> là thương hiệu tiên phong và dẫn đầu thị trường. Đại lý được hưởng lợi từ uy tín thương hiệu, công nghệ sản phẩm và tệp khách hàng lớn.</p>
          </div>
          <div class="tab-pane fade benefit-pane" id="b2" role="tabpanel" aria-labelledby="b2-tab">
            <p class="title">3. Đào Tạo Chuyên Sâu &amp; Phát Triển Kỹ Năng Bán Hàng Định Kỳ</p>
            <p>Định kỳ đào tạo sản phẩm, cập nhật xu hướng và kỹ năng bán hàng/marketing giúp đại lý tư vấn &amp; chốt đơn hiệu quả.</p>
          </div>
          <div class="tab-pane fade benefit-pane" id="b3" role="tabpanel" aria-labelledby="b3-tab">
            <p class="title">4. Chứng Chỉ Đại Lý Chính Thức – Nâng Cao Uy Tín</p>
            <p>Hoàn tất hợp tác, đối tác nhận <strong>Chứng chỉ Đại lý</strong> từ Ekokemika, gia tăng niềm tin và uy tín trên thị trường.</p>
          </div>
          <div class="tab-pane fade benefit-pane" id="b4" role="tabpanel" aria-labelledby="b4-tab">
            <p class="title">5. Nguồn Hàng Chuẩn – Đủ Pháp Lý – An Tâm Kinh Doanh</p>
            <p>Sản phẩm chính hãng, đủ hóa đơn/chứng từ, kiểm định theo quy định → đại lý an tâm tuyệt đối khi kinh doanh.</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- MOBILE: Accordion --}}
  <div class="benefit-accordion-only">
    <div id="benefitAcc" class="benefit-accordion">
      {{-- item 0 --}}
      <div class="card">
        <div class="card-header" id="h0" data-toggle="collapse" data-target="#c0" aria-expanded="true" aria-controls="c0">
          <div class="head">
            <span><img src="https://ekokemika.com.vn/upload/images/icon/icon1.png" alt=""> Chiết Khấu 45%</span>
            <i class="fa fa-chevron-down"></i>
          </div>
        </div>
        <div id="c0" class="collapse show" aria-labelledby="h0" data-parent="#benefitAcc">
          <div class="card-body">
            <p class="title mb-2" style="color:#1f34c5;font-weight:800">1. Chiết Khấu Đại Lý Hấp Dẫn Đến 45% – Tối Ưu Lợi Nhuận</p>
            <p>Khi tham gia mạng lưới <strong>đại lý Ekokemika</strong>, bạn được hưởng <strong>chiết khấu đến 45%</strong>, tối đa hóa lợi nhuận và đảm bảo nguồn thu ổn định.</p>
          </div>
        </div>
      </div>
      {{-- item 1 --}}
      <div class="card">
        <div class="card-header" id="h1" data-toggle="collapse" data-target="#c1" aria-expanded="false" aria-controls="c1">
          <div class="head">
            <span><img src="https://ekokemika.com.vn/upload/images/icon/icon2.png" alt=""> Hệ Thống Dẫn Đầu</span>
            <i class="fa fa-chevron-down"></i>
          </div>
        </div>
        <div id="c1" class="collapse" aria-labelledby="h1" data-parent="#benefitAcc">
          <div class="card-body">
            <p class="title mb-2" style="color:#1f34c5;font-weight:800">2. Gia Nhập Hệ Thống Nước Rửa Xe Không Chạm Mạnh Nhất Việt Nam</p>
            <p><strong>Ekokemika</strong> là thương hiệu tiên phong và dẫn đầu thị trường. Đại lý được hưởng lợi từ uy tín thương hiệu, công nghệ sản phẩm và tệp khách hàng lớn.</p>
          </div>
        </div>
      </div>
      {{-- item 2 --}}
      <div class="card">
        <div class="card-header" id="h2" data-toggle="collapse" data-target="#c2" aria-expanded="false" aria-controls="c2">
          <div class="head">
            <span><img src="https://ekokemika.com.vn/upload/images/icon/icon3.png" alt=""> Đào Tạo Chuyên Sâu</span>
            <i class="fa fa-chevron-down"></i>
          </div>
        </div>
        <div id="c2" class="collapse" aria-labelledby="h2" data-parent="#benefitAcc">
          <div class="card-body">
            <p class="title mb-2" style="color:#1f34c5;font-weight:800">3. Đào Tạo Chuyên Sâu &amp; Phát Triển Kỹ Năng Bán Hàng Định Kỳ</p>
            <p>Định kỳ đào tạo sản phẩm, cập nhật xu hướng và kỹ năng bán hàng/marketing giúp đại lý tư vấn &amp; chốt đơn hiệu quả.</p>
          </div>
        </div>
      </div>
      {{-- item 3 --}}
      <div class="card">
        <div class="card-header" id="h3" data-toggle="collapse" data-target="#c3" aria-expanded="false" aria-controls="c3">
          <div class="head">
            <span><img src="https://ekokemika.com.vn/upload/images/icon/icon4.png" alt=""> Chứng Chỉ Chính Thức</span>
            <i class="fa fa-chevron-down"></i>
          </div>
        </div>
        <div id="c3" class="collapse" aria-labelledby="h3" data-parent="#benefitAcc">
          <div class="card-body">
            <p class="title mb-2" style="color:#1f34c5;font-weight:800">4. Chứng Chỉ Đại Lý Chính Thức – Nâng Cao Uy Tín</p>
            <p>Hoàn tất hợp tác, đối tác nhận <strong>Chứng chỉ Đại lý</strong> từ Ekokemika, gia tăng niềm tin và uy tín trên thị trường.</p>
          </div>
        </div>
      </div>
      {{-- item 4 --}}
      <div class="card">
        <div class="card-header" id="h4" data-toggle="collapse" data-target="#c4" aria-expanded="false" aria-controls="c4">
          <div class="head">
            <span><img src="https://ekokemika.com.vn/upload/images/icon/icon5.png" alt=""> An Tâm Pháp Lý</span>
            <i class="fa fa-chevron-down"></i>
          </div>
        </div>
        <div id="c4" class="collapse" aria-labelledby="h4" data-parent="#benefitAcc">
          <div class="card-body">
            <p class="title mb-2" style="color:#1f34c5;font-weight:800">5. Nguồn Hàng Chuẩn – Đủ Pháp Lý – An Tâm Kinh Doanh</p>
            <p>Sản phẩm chính hãng, đủ hóa đơn/chứng từ, kiểm định theo quy định → đại lý an tâm tuyệt đối khi kinh doanh.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>


    {{-- CTA + FORM --}}
    <section class="section-muted mb-4">
        <div class="text-center mb-3">
            <h2 class="mb-2">Đăng Ký Làm Đại Lý Ekokemika Ngay Hôm Nay Để Nhận Ưu Đãi!</h2>
            <p>Đừng bỏ lỡ cơ hội kinh doanh tiềm năng. Hãy <strong>đăng ký</strong> để trở thành một phần của mạng lưới thành công.</p>
            <a class="btn btn-cta btn-lg" href="#register">ĐĂNG KÝ LÀM ĐẠI LÝ NGAY</a>
        </div>

        @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
        @if(session('error'))   <div class="alert alert-danger">{{ session('error') }}</div> @endif

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <p class="form-desc">Vui lòng điền thông tin chi tiết vào biểu mẫu dưới đây để chúng tôi có thể liên hệ và hỗ trợ bạn một cách tốt nhất:</p>

                <form id="register" action="{{ route('frontend.dealers.store') }}" method="POST" class="mt-3">
                    @csrf
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Họ và tên <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label>Số điện thoại <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" required>
                            @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label>Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label>Tên công ty/Cửa hàng</label>
                            <input type="text" name="company" class="form-control" value="{{ old('company') }}">
                            @error('company') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label>Địa chỉ kinh doanh /Tỉnh /Thành phố <span class="text-danger">*</span></label>
                            <input type="text" name="address" class="form-control" value="{{ old('address') }}" required>
                            @error('address') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label>Bạn biết đến Ekokemika qua đâu? <span class="text-danger">*</span></label>
                            <select name="source" class="form-control" required>
                                @php
                                    $sources = ['Google'=>'Tìm kiếm Google','Social'=>'Mạng xã hội','Friend'=>'Giới thiệu từ bạn bè','Ads'=>'Quảng cáo','Other'=>'Khác...'];
                                @endphp
                                <option value="" disabled {{ old('source') ? '' : 'selected' }}>Chọn nguồn</option>
                                @foreach($sources as $v=>$label)
                                    <option value="{{ $v }}" {{ old('source')===$v ? 'selected':'' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('source') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="form-group col-12">
                            <label>Thông điệp/Câu hỏi khác</label>
                            <textarea name="message" rows="5" class="form-control">{{ old('message') }}</textarea>
                            @error('message') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <button class="btn btn-primary btn-lg">
                        <i class="fa-solid fa-paper-plane mr-1"></i> Gửi đăng ký
                    </button>
                </form>
            </div>
        </div>
    </section>

</div>
@endsection

@push('js')

@endpush

