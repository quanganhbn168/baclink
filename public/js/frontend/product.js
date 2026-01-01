/**
 * product.js
 * Handles product detail page interactions:
 * 1. Quantity selector
 * 2. Variant selection & Price update
 * 3. Add to Cart & Buy Now actions
 */

// --- GLOBAL STATE ---
let currentVariantId = null;

// Expose current variant ID getter globally (optional, but good for debugging)
window.getCurrentVariantId = function() {
    return currentVariantId;
};

document.addEventListener('DOMContentLoaded', function() {
    // --- CONFIGURATION & ELEMENTS ---
    const config = window.productData || { variants: [], cartCount: 0 };
    const variants = config.variants;

    // Elements
    const qtyInput = document.getElementById('qtyInput');
    const qtyBtns = document.querySelectorAll('.btn-qty');
    const priceContainer = document.getElementById('price-container');
    const groupBuyButtons = document.getElementById('group-buy-buttons');
    const btnContact = document.getElementById('btnContact');
    const variantInputs = document.querySelectorAll('.variant-input');
    const btnAdd = document.getElementById('btnAddToCart');
    const btnBuy = document.getElementById('btnBuyNowAjax');
    const badge = document.querySelector('.cart-count');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    // Formatter
    const moneyFormatter = new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    });

    // --- 1. INITIALIZATION ---
    
    // Set initial cart count badge
    if (badge) {
        badge.textContent = config.cartCount;
        badge.style.display = config.cartCount > 0 ? 'flex' : 'none';
    }

    // --- 2. QUANTITY LOGIC ---
    
    function updateQtyUI() {
        if (!qtyInput) return;
        let val = parseInt(qtyInput.value || '1', 10);
        if (isNaN(val) || val < 1) val = 1;
        qtyInput.value = val;
    }

    if (qtyInput && qtyBtns.length > 0) {
        qtyBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                let val = parseInt(qtyInput.value || '1', 10);
                if (btn.dataset.act === 'inc') val++;
                if (btn.dataset.act === 'dec') val = Math.max(1, val - 1);
                qtyInput.value = val;
            });
        });
        qtyInput.addEventListener('input', updateQtyUI);
    }

    // --- 3. VARIANT LOGIC ---

    function toggleContactState(isContact) {
        if (isContact) {
            if (groupBuyButtons) groupBuyButtons.style.setProperty('display', 'none', 'important');
            if (btnContact) btnContact.style.display = 'inline-block';
        } else {
            if (groupBuyButtons) groupBuyButtons.style.display = 'flex';
            if (btnContact) btnContact.style.display = 'none';
        }
    }

    function updatePriceUI(price, comparePrice) {
        if (!priceContainer) return;

        if (price <= 0) {
            priceContainer.innerHTML = '<span class="price-main">Liên hệ</span>';
            toggleContactState(true);
        } else {
            let html = `<span class="price-main">${moneyFormatter.format(price)}</span>`;
            if (comparePrice > price) {
                const percent = Math.round(100 - (price / comparePrice * 100));
                html += ` <span class="price-compare">${moneyFormatter.format(comparePrice)}</span> <span class="badge-sale">-${percent}%</span>`;
            }
            priceContainer.innerHTML = html;
            toggleContactState(false);
        }
    }

    function checkVariant() {
        // Get all checked inputs
        const checkedInputs = document.querySelectorAll('.variant-input:checked');
        let selectedAttrIds = [];
        
        checkedInputs.forEach(inp => selectedAttrIds.push(parseInt(inp.value)));
        selectedAttrIds.sort((a, b) => a - b);

        // Check if all groups have a selection
        const totalGroups = document.querySelectorAll('.variant-group').length;
        if (selectedAttrIds.length < totalGroups) return;

        // Find matching variant
        const foundVariant = variants.find(v => {
            // Ensure v.attr_ids is sorted for comparison if not already
            const variantAttrIds = [...v.attr_ids].sort((a, b) => a - b);
            return JSON.stringify(variantAttrIds) === JSON.stringify(selectedAttrIds);
        });

        if (foundVariant) {
            currentVariantId = foundVariant.id;
            updatePriceUI(foundVariant.price, foundVariant.compare_at_price);
        } else {
            currentVariantId = null;
            if (priceContainer) {
                priceContainer.innerHTML = '<span class="text-danger font-weight-bold">Tạm hết hàng</span>';
            }
            toggleContactState(true);
        }
    }

    if (variantInputs.length > 0) {
        variantInputs.forEach(input => {
            input.addEventListener('change', checkVariant);
        });
    }

    // --- 4. CART & BUY NOW LOGIC ---

    function handleCartAction(btn, isBuyNow = false) {
        const productId = parseInt(btn.dataset.productId, 10);
        const qty = parseInt(qtyInput ? qtyInput.value : 1, 10) || 1;
        const url = btn.dataset.url;

        // Validate Variants
        const totalGroups = document.querySelectorAll('.variant-group').length;
        const checked = document.querySelectorAll('.variant-input:checked').length;

        if (totalGroups > 0 && checked < totalGroups) {
            Swal.fire({
                icon: 'warning',
                title: 'Thông báo',
                text: 'Vui lòng chọn đầy đủ thuộc tính (Màu sắc, Kích thước...)',
                confirmButtonColor: '#3085d6',
            });
            return;
        }

        // Send Request
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: qty,
                variant_id: currentVariantId
            }),
            credentials: 'same-origin'
        })
        .then(res => {
            if (res.status === 401 || res.status === 419) {
                window.location.href = '/login';
                return;
            }
            if (!res.ok) throw new Error('Network response was not ok');
            return res.json();
        })
        .then(data => {
            if (!data || !data.success) {
                throw new Error(data.message || 'Có lỗi xảy ra');
            }

            if (isBuyNow) {
                // Logic Buy Now
                window.location.href = data.redirect_url;
            } else {
                // Logic Add to Cart
                Swal.fire({
                    icon: 'success',
                    title: 'Thành công!',
                    text: 'Đã thêm vào giỏ hàng!',
                    timer: 1500,
                    showConfirmButton: false
                });

                // Update Badge
                if (badge && typeof data.total_quantity !== 'undefined') {
                    badge.textContent = data.total_quantity;
                    badge.style.display = data.total_quantity > 0 ? 'flex' : 'none';
                }
            }
        })
        .catch(err => {
            console.error(err);
            Swal.fire({
                icon: 'error',
                title: 'Lỗi!',
                text: 'Có lỗi xảy ra, vui lòng thử lại.'
            });
        });
    }

    // Attach Events
    if (btnAdd) {
        btnAdd.addEventListener('click', function(e) {
            e.preventDefault();
            handleCartAction(this, false);
        });
    }

    if (btnBuy) {
        btnBuy.addEventListener('click', function(e) {
            e.preventDefault();
            handleCartAction(this, true);
        });
    }
});