{{-- resources/views/cart/index.blade.php --}}
@extends('layouts.master')

@section('title', 'Giỏ hàng của bạn')
@push('css')

<style>
    /* --- CSS CHUNG --- */
    .cart-item-img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
    }
    
    .quantity-input {
        text-align: center;
        border-left: 0;
        border-right: 0;
        font-weight: 600;
        max-width: 50px; /* Cố định chiều rộng ô số lượng */
    }

    .btn-minus, .btn-plus {
        border-color: #ced4da;
    }

    /* --- MOBILE STYLES (Màn hình nhỏ < 768px) --- */
    @media (max-width: 767.98px) {
        
        /* 1. Ẩn tiêu đề bảng (Thead) */
        .table thead {
            display: none;
        }

        /* 2. Biến mỗi dòng (tr) thành một khối Card */
        .table tbody tr {
            display: block;
            background: #fff;
            border: 1px solid #eee;
            border-radius: 12px;
            margin-bottom: 15px;
            padding: 15px;
            position: relative; /* Để định vị nút xóa */
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        /* 3. Căn chỉnh các ô (td) bên trong Card */
        .table tbody td {
            display: block;
            border: none;
            padding: 5px 0;
            text-align: left;
            width: 100% !important; /* Ghi đè style inline width */
        }

        /* 4. Layout lại từng phần tử trong Card */
        
        /* Ảnh sản phẩm: Cho nằm gọn bên trái */
        .table tbody td:first-child { 
            float: left;
            width: 90px !important;
            margin-right: 15px;
            margin-bottom: 10px;
        }
        .table tbody td:first-child img {
            width: 100%;
            height: auto;
        }

        /* Tên sản phẩm: Đậm, to */
        .table tbody td:nth-child(2) h6 {
            font-size: 16px;
            margin-bottom: 4px;
            padding-right: 30px; /* Tránh đè lên nút xóa */
        }

        /* Giá tiền: Màu nổi bật */
        .price-per-item {
            font-weight: 600;
            color: #ee4d2d; /* Màu cam Shopee/Lazada */
            margin-bottom: 8px;
        }
        .price-per-item::before {
            content: "Đơn giá: ";
            color: #666;
            font-weight: normal;
            font-size: 13px;
        }

        /* Bộ chọn số lượng (+ -) */
        .table tbody td:nth-child(4) {
            display: inline-block;
            width: auto !important;
        }

        /* Thành tiền (Subtotal): Ẩn bớt cho đỡ rối hoặc cho xuống dòng cuối */
        .item-subtotal {
            border-top: 1px dashed #eee;
            margin-top: 10px;
            padding-top: 10px !important;
            font-weight: bold;
            color: #333;
            text-align: right !important;
        }
        .item-subtotal::before {
            content: "Thành tiền: ";
            float: left;
            color: #666;
            font-weight: normal;
        }

        /* Nút xóa: Đưa lên góc trên bên phải tuyệt đối */
        .remove-item-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            color: #999;
            padding: 5px;
        }
        
        /* Ẩn cột xóa mặc định trong bảng đi vì đã đưa lên góc */
        .table tbody td:last-child {
            display: none; 
        }

        /* Clear float sau ảnh */
        .table tbody::after {
            content: "";
            display: table;
            clear: both;
        }
    }
</style>
@endpush
@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Giỏ hàng của bạn</h4>
        <button type="button" id="btnClearAllCart" class="btn btn-outline-danger">
            Xoá toàn bộ giỏ hàng
        </button>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th colspan="2">Sản phẩm</th>
                            <th class="text-center">Đơn giá</th>
                            <th class="text-center">Số lượng</th>
                            <th class="text-end">Tạm tính</th>
                            <th class="text-center">Xoá</th>
                        </tr>
                    </thead>
                    <tbody id="cart-items-container">
                        <tr>
                            <td colspan="6" class="text-center py-4">Đang tải giỏ hàng…</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Tóm tắt đơn hàng</h5>
                    <ul class="list-group list-group-flush mt-3">
                        <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 pb-0">
                            Tạm tính
                            <span id="summary-subtotal">0&nbsp;đ</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                            Phí vận chuyển
                            <span>Liên hệ</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 mb-3">
                            <strong>Tổng cộng</strong>
                            <strong id="summary-total">0&nbsp;đ</strong>
                        </li>
                    </ul>
                    <a href="{{ route('checkout.index') }}" class="btn-main btn w-100 mt-3">Tiến hành Thanh toán</a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Template giữ nguyên --}}
<template id="cart-item-template">
    <tr class="cart-item-row" data-index="__INDEX__">
        <td style="width:80px;"><img src="__IMAGE__" class="img-fluid rounded cart-item-img" alt="__NAME__"></td>
        <td>
            <h6 class="mb-0">__NAME__</h6>
            {{-- Chỗ này sẽ hiển thị tên biến thể lấy từ Controller --}}
            <div class="text-muted small">__VARIANT__</div>
        </td>
        <td class="text-center price-per-item" data-price="__PRICE_RAW__">__PRICE__</td>
        <td style="width:150px;" class="text-center">
            <div class="input-group input-group-sm mx-auto" style="max-width:120px;">
                <div class="input-group-prepend"><button class="btn btn-outline-secondary btn-minus" type="button">-</button></div>
                <input type="number" class="form-control text-center quantity-input" value="__QUANTITY__" min="0">
                <div class="input-group-append"><button class="btn btn-outline-secondary btn-plus" type="button">+</button></div>
            </div>
        </td>
        <td class="text-end item-subtotal">__SUBTOTAL__</td>
        <td class="text-center"><a href="#!" class="text-danger remove-item-btn"><i class="fas fa-trash"></i></a></td>
    </tr>
</template>
@endsection

@push('js')
{{-- Import SweetAlert2 nếu chưa có trong layout --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
(function () {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const tbody = document.getElementById('cart-items-container');
    const rowTpl = document.getElementById('cart-item-template').innerHTML;
    
    // Dữ liệu items từ Controller (đã được enrich với variant_text chuẩn)
    const BOOT_ITEMS = @json($items ?? []); 

    const money = n => new Intl.NumberFormat('vi-VN',{style:'currency',currency:'VND'}).format(Number(n||0));
    
    const setSummary = total => {
        document.getElementById('summary-subtotal').textContent = money(total);
        document.getElementById('summary-total').textContent    = money(total);
    };
    
    const setBadge = qty => {
        const badge = document.querySelector('.cart-count');
        if (!badge) return;
        const q = Number(qty||0);
        badge.textContent   = q > 0 ? String(q) : '0';
        badge.style.display = q > 0 ? 'flex' : 'none';
    };

    // ---- render ----
    function render(items) {
        if (!items || !items.length) {
            tbody.innerHTML = `<tr><td colspan="6" class="text-center py-4">Giỏ hàng của bạn đang trống.</td></tr>`;
            setSummary(0);
            return;
        }
        
        let html = '';
        let total = 0;
        
        items.forEach(it => {
            const index    = it.index;
            const image    = it.image;
            const name     = it.name;
            const price    = Number(it.price);
            const quantity = Number(it.quantity);
            
            // Lấy text biến thể từ controller (VD: "Đỏ - Size L") hoặc rỗng
            const variant  = it.variant_text || ''; 

            total += price * quantity;

            html += rowTpl
                .replace(/__INDEX__/g, index)
                .replace(/__IMAGE__/g, image)
                .replace(/__NAME__/g, name)
                .replace(/__VARIANT__/g, variant) // JS thay thế text vào div .text-muted.small
                .replace(/__PRICE_RAW__/g, price)
                .replace(/__PRICE__/g, money(price))
                .replace(/__QUANTITY__/g, quantity)
                .replace(/__SUBTOTAL__/g, money(price*quantity));
        });
        
        tbody.innerHTML = html;
        setSummary(total);
    }

    async function fetchCart() {
        const res  = await fetch(`{{ route('cart.index') }}`, { headers:{ 'Accept':'application/json' }});
        const data = await res.json();
        const items = data.items ?? [];
        render(items);
        setBadge(data.total_quantity ?? items.reduce((s,x)=>s+Number(x.quantity||0),0));
    }

    // ---- +/- quantity ----
    tbody.addEventListener('click', async (e) => {
        const plusMinus = e.target.closest('.btn-plus, .btn-minus');
        if (!plusMinus) return;
        const row   = e.target.closest('.cart-item-row');
        const input = row.querySelector('.quantity-input');
        let v = parseInt(input.value || '0', 10); v = isNaN(v) ? 0 : v;
        v = plusMinus.classList.contains('btn-plus') ? v+1 : Math.max(0, v-1);
        input.value = v;
        await commitQty(row, v);
    });

    tbody.addEventListener('change', async (e) => {
        if (!e.target.classList.contains('quantity-input')) return;
        const row = e.target.closest('.cart-item-row');
        let v = parseInt(e.target.value || '0', 10); v = isNaN(v) ? 0 : Math.max(0, v);
        e.target.value = v;
        await commitQty(row, v);
    });

    async function commitQty(row, qty) {
        const index = row.getAttribute('data-index');
        const res   = await fetch(`/cart/update/${index}`, {
            method: 'POST',
            headers: {'Accept':'application/json','Content-Type':'application/json','X-CSRF-TOKEN': csrf},
            body: JSON.stringify({_method:'PUT', quantity: qty})
        });
        const data = await res.json();
        if (qty === 0) row.remove();
        recalcTotals();
        setBadge(data.total_quantity ?? 0);
    }

    function recalcTotals() {
        let total = 0;
        tbody.querySelectorAll('.cart-item-row').forEach(row => {
            const price = Number(row.querySelector('.price-per-item')?.dataset.price || 0);
            const qty   = Number(row.querySelector('.quantity-input')?.value || 0);
            row.querySelector('.item-subtotal').textContent = money(price*qty);
            total += price * qty;
        });
        setSummary(total);
    }

    // ---- remove 1 item ----
    tbody.addEventListener('click', async (e) => {
        const btn = e.target.closest('.remove-item-btn');
        if (!btn) return;
        const row   = e.target.closest('.cart-item-row');
        const index = row.getAttribute('data-index');

        const ok = await Swal.fire({
            title: 'Xoá sản phẩm?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Xoá',
            cancelButtonText: 'Huỷ'
        }).then(r => r.isConfirmed);
        if (!ok) return;

        const res  = await fetch(`/cart/remove/${index}`, {
            method: 'POST',
            headers: {'Accept':'application/json','Content-Type':'application/json','X-CSRF-TOKEN': csrf},
            body: JSON.stringify({_method:'DELETE'})
        });
        const data = await res.json();
        row.remove();
        recalcTotals();
        setBadge(data.total_quantity ?? 0);
    });

    // ---- clear all ----
    document.getElementById('btnClearAllCart')?.addEventListener('click', async () => {
        const ok = await Swal.fire({
            title: 'Xoá toàn bộ giỏ hàng?',
            text: 'Thao tác này sẽ xoá sạch giỏ hàng hiện tại.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Xoá',
            cancelButtonText: 'Huỷ'
        }).then(r => r.isConfirmed);
        if (!ok) return;

        await fetch(`{{ route('cart.clear-all') }}`, {
            method: 'POST',
            headers: {'Accept':'application/json','X-CSRF-TOKEN': csrf}
        });

        render([]); setBadge(0);
        Swal.fire({icon:'success', title:'Đã xoá giỏ hàng', timer:1200, showConfirmButton:false});
    });

    // ---- boot ----
    if (Array.isArray(BOOT_ITEMS) && BOOT_ITEMS.length) {
        render(BOOT_ITEMS);
        setBadge(BOOT_ITEMS.reduce((s,x)=>s+Number(x.quantity||0),0));
    } else {
        fetchCart();
    }
})();
</script>
@endpush
