@push('css')
<style>
    aside.aside-dangky{
        padding: 20px;
        background: linear-gradient(178deg, #04599f, #ebe3e300);
        border-radius: var(--border-radius);
    }
    aside.aside-dangky h2.dangky{
        color: #ffffff;
    }
    aside.aside-dangky p.sub-dangky{
        color: #ffffff;
    }
    aside.aside-dangky form#contact-form{}
    aside.aside-dangky form#contact-form input{
        margin-bottom: 20px;
    }
    aside.aside-dangky form#contact-form textarea{
        margin-bottom: 20px;
    }
    aside.aside-dangky form#contact-form button[type="submit"]{
        display: block;
        width: 100%;
    }
</style>
@endpush
<aside class="aside-dangky">
	<h2 class="dangky">Đăng ký tư vấn</h2>
	<p class="sub-dangky">
		Vui lòng điền thông tin vào form bên dưới để nhận được báo giá sản phẩm sớm nhất.*
	</p>
	<form action="{{route('contact.store')}}" id="contact-form">
		<input type="text" name="name" class="form-control" placeholder="Họ và tên" required>
		<input type="text" name="phone" class="form-control" placeholder="Số điện thoại" required>
		<textarea name="message" id="message" class="form-control" placeholder="Nội dung"></textarea>
		<button type="submit" class="btn btn-primary">Gửi thông tin</button>
	</form>
</aside>
@push('js')
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
        <script>
    // Custom rule kiểm tra số điện thoại Việt Nam
            $.validator.addMethod("phoneVN", function (value, element) {
                return this.optional(element) || /^(0[3|5|7|8|9])[0-9]{8}$|^\+84[3|5|7|8|9][0-9]{8}$/.test(value);
            }, "Số điện thoại không hợp lệ");
            $(document).ready(function () {
                $('#contact-form').validate({
                    rules: {
                        name: {
                            required: true,
                            minlength: 2
                        },
                        phone: {
                            required: true,
                            phoneVN: true
                        },
                        email: {
                            email: true
                        },
                        content: {
                            maxlength: 1000
                        }
                    },
                    messages: {
                        name: {
                            required: "Vui lòng nhập họ và tên",
                            minlength: "Tên quá ngắn"
                        },
                        phone: {
                            required: "Vui lòng nhập số điện thoại",
                            phoneVN: "Số điện thoại không hợp lệ (ví dụ: 098xxxxxxx)"
                        },
                        email: {
                            email: "Email không hợp lệ"
                        },
                        message: {
                            maxlength: "Ý kiến không vượt quá 1000 ký tự"
                        }
                    },
                    errorElement: 'small',
                    errorClass: 'text-danger',
                    highlight: function (element) {
                        $(element).addClass('is-invalid');
                    },
                    unhighlight: function (element) {
                        $(element).removeClass('is-invalid');
                    }
                });
            });
        </script>
@endpush