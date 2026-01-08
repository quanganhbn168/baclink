@extends('layouts.admin')
@section('title', 'Tạo đơn hàng mới')
@push('css')
<style>
    /* 1. Cố định layout bảng để các ô tuân thủ đúng % width đã set */
    #productTable {
        table-layout: fixed; 
        width: 100%;
    }

    /* 2. Fix chiều rộng Select2 luôn là 100% của ô chứa nó */
    .select2-container { 
        width: 100% !important; 
    }

    /* 3. QUAN TRỌNG: Cho phép chữ trong Select2 xuống dòng thay vì đẩy rộng ra */
    .select2-container .select2-selection--single {
        height: auto !important; /* Chiều cao tự động dãn theo nội dung */
        min-height: 31px; /* Chiều cao tối thiểu giống input sm */
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        white-space: normal !important; /* Cho phép xuống dòng */
        word-wrap: break-word !important; /* Ngắt từ nếu quá dài */
        line-height: 1.5 !important;
        padding-top: 3px;
        padding-bottom: 3px;
        text-align: left;
    }

    /* Căn chỉnh lại mũi tên dropdown cho đẹp khi ô cao lên */
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        top: 50% !important;
        transform: translateY(-50%);
    }
</style>
@endpush
@section('content')
<div class="container-fluid">
    <form action="{{ route('admin.orders.store') }}" method="POST" id="orderForm">
        @csrf
        <div class="row">
            {{-- CỘT TRÁI: THÔNG TIN KHÁCH --}}
            <div class="col-md-4">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Thông tin khách hàng</h3>
                    </div>
                    <div class="card-body">
                        {{-- Chọn khách hàng --}}
                        <div class="form-group">
                            <label>Chọn khách hàng (Nếu có)</label>
                            <select name="user_id" class="form-control select2-user" id="userSelect" style="width: 100%;">
                                <option value="" data-discount="0">-- Khách lẻ / Vãng lai --</option>
                                @foreach($users as $user)
                                    @php
                                        // Logic lấy chiết khấu an toàn
                                        $discount = 0;
                                        $label = '';
                                        // Kiểm tra quan hệ dealerProfile đã được load hoặc tồn tại
                                        if ($user->relationLoaded('dealerProfile') ? $user->dealerProfile : $user->dealerProfile()->exists()) {
                                             $discount = $user->dealerProfile->discount_rate ?? 0;
                                             if ($discount > 0) {
                                                 $label = " (Đại lý -{$discount}%)";
                                             }
                                        }
                                    @endphp
                                    <option value="{{ $user->id }}" 
                                            data-name="{{ $user->name }}" 
                                            data-phone="{{ $user->phone }}"
                                            data-address="{{ $user->address ?? '' }}"
                                            data-discount="{{ $discount }}">
                                        {{ $user->phone }} - {{ $user->name }}{{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <hr>
                        
                        <div class="form-group">
                            <label>Họ tên <span class="text-danger">*</span></label>
                            <input type="text" name="customer_name" id="c_name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Số điện thoại <span class="text-danger">*</span></label>
                            <input type="text" name="customer_phone" id="c_phone" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Địa chỉ giao hàng</label>
                            <input type="text" name="shipping_address" id="c_address" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Ghi chú</label>
                            <textarea name="note" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- CỘT PHẢI: CHỌN SẢN PHẨM --}}
            <div class="col-md-8">
                <div class="card card-success card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Danh sách sản phẩm</h3>
                    </div>
                    <div class="card-body p-2">
                        <table class="table table-bordered text-center" id="productTable">
                            <thead class="bg-light">
                                <tr>
                                    <th width="35%">Sản phẩm</th>
                                    <th width="20%">Phân loại</th>
                                    <th width="15%">Đơn giá</th>
                                    <th width="10%">SL</th>
                                    <th width="15%">Thành tiền</th>
                                    <th width="5%"><i class="fas fa-trash"></i></th>
                                </tr>
                            </thead>
                            <tbody id="productTableBody">
                                {{-- Dòng mẫu sẽ được JS thêm vào đây --}}
                            </tbody>
                            
                            {{-- FOOTER TÍNH TOÁN --}}
                            <tfoot>
                                <tr>
                                    <td colspan="6" class="p-0">
                                        <button type="button" class="btn btn-sm btn-info m-2 float-left" id="btnAddRow">
                                            <i class="fas fa-plus"></i> Thêm sản phẩm
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-right font-weight-bold align-middle">Tạm tính:</td>
                                    <td class="text-right font-weight-bold">
                                        <span id="subtotalDisplay">0 ₫</span>
                                        <input type="hidden" name="subtotal" id="subtotalValue" value="0">
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-right font-weight-bold align-middle text-info">
                                        Chiết khấu đại lý (<span id="discountRateDisplay">0</span>%):
                                    </td>
                                    <td class="text-right font-weight-bold text-info">
                                        -<span id="discountAmountDisplay">0 ₫</span>
                                        {{-- Input hidden để gửi lên server nếu cần tham chiếu, nhưng server nên tính lại --}}
                                        <input type="hidden" id="discountRateValue" value="0">
                                        <input type="hidden" id="discountAmountValue" value="0">
                                    </td>
                                    <td></td>
                                </tr>
                                <tr class="bg-light">
                                    <td colspan="4" class="text-right font-weight-bold align-middle text-danger" style="font-size: 1.2rem;">
                                        TỔNG THANH TOÁN:
                                    </td>
                                    <td class="text-right font-weight-bold text-danger" style="font-size: 1.2rem;">
                                        <span id="totalDisplay">0 ₫</span>
                                        {{-- Đây là số tiền cuối cùng khách phải trả --}}
                                        <input type="hidden" name="total_price" id="totalValue" value="0">
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="card-footer text-right">
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-default">Hủy bỏ</a>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-check"></i> Tạo đơn hàng</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('css')
<style>
    /* Fix chiều rộng select2 trong bảng */
    .select2-container { width: 100% !important; }
</style>
@endpush

@push('js')
<script>
    // 1. CHUẨN BỊ DỮ LIỆU TỪ PHP SANG JS
    const products = @json($products); 
    
    // Hàm format tiền tệ VNĐ
    function formatCurrency(amount) {
        return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
    }

    $(document).ready(function() {
        // Init Select2 cho User
        $('.select2-user').select2({
            theme: 'bootstrap4',
            placeholder: "-- Chọn khách hàng --",
            allowClear: true
        });

        // --- LOGIC 1: CHỌN KHÁCH HÀNG & TÍNH CHIẾT KHẤU ---
        $('#userSelect').on('select2:select', function (e) {
            let selected = $(this).find(':selected');
            
            // Điền thông tin
            $('#c_name').val(selected.data('name') || '');
            $('#c_phone').val(selected.data('phone') || '');
            $('#c_address').val(selected.data('address') || '');
            
            // Lấy mức chiết khấu từ data-attribute
            let discountRate = parseInt(selected.data('discount')) || 0;
            
            // Cập nhật UI và Input hidden
            $('#discountRateDisplay').text(discountRate);
            $('#discountRateValue').val(discountRate);
            
            // Tính lại toàn bộ tiền
            updateTotalBill();

            // Thông báo nhỏ (Optional)
            if(discountRate > 0) {
                if(typeof toastr !== 'undefined') {
                    toastr.info(`Đã áp dụng mức chiết khấu đại lý: ${discountRate}%`);
                }
            }
        });
        
        // Khi xóa chọn khách hàng -> Reset chiết khấu về 0
        $('#userSelect').on('select2:unselecting', function (e) {
             $('#discountRateDisplay').text(0);
             $('#discountRateValue').val(0);
             // Timeout để đợi sự kiện clear xong mới tính lại
             setTimeout(updateTotalBill, 100);
        });

        // --- LOGIC 2: THÊM DÒNG SẢN PHẨM ---
        let rowCount = 0;

        function addRow() {
            rowCount++;
            let html = `
                <tr id="row_${rowCount}">
                    <td>
                        <select class="form-control form-control-sm product-select select2-product" name="items[${rowCount}][product_id]" data-row="${rowCount}" required>
                            <option value="">-- Tìm sản phẩm --</option>
                            ${products.map(p => `<option value="${p.id}">${p.code ? p.code + ' - ' : ''}${p.name}</option>`).join('')}
                        </select>
                    </td>
                    <td>
                        <select class="form-control form-control-sm variant-select" name="items[${rowCount}][product_variant_id]" id="variant_${rowCount}" data-row="${rowCount}" disabled>
                            <option value="">Mặc định</option>
                        </select>
                    </td>
                    <td>
                        <input type="text" class="form-control form-control-sm text-right bg-white" id="price_display_${rowCount}" value="0" readonly>
                        <input type="hidden" class="price-input" id="price_${rowCount}" value="0">
                    </td>
                    <td>
                        <input type="number" class="form-control form-control-sm text-center qty-input" name="items[${rowCount}][quantity]" id="qty_${rowCount}" value="1" min="1" data-row="${rowCount}">
                    </td>
                    <td class="text-right align-middle font-weight-bold">
                        <span id="subtotal_display_${rowCount}">0 ₫</span>
                        <input type="hidden" class="subtotal-item-input" id="subtotal_${rowCount}" value="0">
                    </td>
                    <td>
                        <button type="button" class="btn btn-xs btn-danger btn-remove" data-row="${rowCount}"><i class="fas fa-times"></i></button>
                    </td>
                </tr>
            `;
            $('#productTableBody').append(html);

            // Init Select2 cho dòng mới thêm
            $(`#row_${rowCount} .select2-product`).select2({
                theme: 'bootstrap4',
                width: '100%'
            });
        }

        // Gọi 1 dòng mặc định
        addRow();

        $('#btnAddRow').click(function() {
            addRow();
        });

        // --- LOGIC 3: XỬ LÝ KHI CHỌN SẢN PHẨM ---
        $(document).on('change', '.product-select', function() {
            let rowId = $(this).data('row');
            let productId = $(this).val();
            let variantSelect = $(`#variant_${rowId}`);
            let priceInput = $(`#price_${rowId}`);
            let priceDisplay = $(`#price_display_${rowId}`);

            let product = products.find(p => p.id == productId);

            if (product) {
                variantSelect.empty();
                
                // Kiểm tra biến thể
                if (product.product_variants && product.product_variants.length > 0) {
                    variantSelect.prop('disabled', false);
                    variantSelect.append('<option value="">-- Chọn loại --</option>');
                    product.product_variants.forEach(v => {
                        // Logic hiển thị tên biến thể
                        let vName = v.variant_name || (v.size + ' - ' + v.color); 
                        let vPrice = v.price || product.price;
                        variantSelect.append(`<option value="${v.id}" data-price="${vPrice}">${vName}</option>`);
                    });
                    // Reset giá về 0 để bắt buộc chọn variant
                    priceInput.val(0);
                    priceDisplay.val(0);
                } else {
                    // Sản phẩm thường
                    variantSelect.prop('disabled', true);
                    variantSelect.append('<option value="">Mặc định</option>');
                    priceInput.val(product.price);
                    // Format giá hiển thị
                    priceDisplay.val(formatCurrency(product.price).replace('₫', '').trim());
                }
            } else {
                priceInput.val(0);
                priceDisplay.val(0);
                variantSelect.empty().prop('disabled', true);
            }
            calculateRow(rowId);
        });

        // --- LOGIC 4: XỬ LÝ KHI CHỌN BIẾN THỂ ---
        $(document).on('change', '.variant-select', function() {
            let rowId = $(this).data('row');
            let selectedOption = $(this).find(':selected');
            let price = selectedOption.data('price');

            // Nếu người dùng chọn placeholder "-- Chọn loại --"
            if (price === undefined) {
                 let productId = $(`select[name="items[${rowId}][product_id]"]`).val();
                 let product = products.find(p => p.id == productId);
                 price = 0; 
            }

            $(`#price_${rowId}`).val(price);
            $(`#price_display_${rowId}`).val(formatCurrency(price).replace('₫', '').trim());
            calculateRow(rowId);
        });

        // --- LOGIC 5: TÍNH TOÁN KHI ĐỔI SỐ LƯỢNG ---
        $(document).on('input', '.qty-input', function() {
            let rowId = $(this).data('row');
            calculateRow(rowId);
        });

        // --- LOGIC 6: XÓA DÒNG ---
        $(document).on('click', '.btn-remove', function() {
            let rowId = $(this).data('row');
            $(`#row_${rowId}`).remove();
            updateTotalBill();
        });

        // HÀM TÍNH TIỀN 1 DÒNG
        function calculateRow(rowId) {
            let price = parseFloat($(`#price_${rowId}`).val()) || 0;
            let qty = parseInt($(`#qty_${rowId}`).val()) || 1;
            let total = price * qty;
            
            $(`#subtotal_${rowId}`).val(total);
            $(`#subtotal_display_${rowId}`).text(formatCurrency(total));
            
            updateTotalBill();
        }

        // HÀM TÍNH TỔNG BILL (QUAN TRỌNG NHẤT)
        function updateTotalBill() {
            // 1. Tính tổng tiền hàng (Subtotal)
            let subtotal = 0;
            $('.subtotal-item-input').each(function() {
                let val = parseFloat($(this).val()) || 0;
                subtotal += val;
            });

            // 2. Tính tiền giảm giá (Discount)
            let discountRate = parseInt($('#discountRateValue').val()) || 0;
            let discountAmount = 0;
            if (discountRate > 0) {
                discountAmount = subtotal * (discountRate / 100);
            }

            // 3. Tính tổng thanh toán (Total)
            let total = subtotal - discountAmount;

            // 4. Cập nhật UI và Input hidden
            $('#subtotalDisplay').text(formatCurrency(subtotal));
            $('#subtotalValue').val(subtotal);

            $('#discountAmountDisplay').text(formatCurrency(discountAmount));
            $('#discountAmountValue').val(discountAmount);

            $('#totalDisplay').text(formatCurrency(total));
            $('#totalValue').val(total);
        }
    });
</script>
@endpush
