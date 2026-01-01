document.addEventListener('DOMContentLoaded', function() {
    const isGuest = !document.body.classList.contains('logged-in'); 
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const STORAGE_KEY = 'guest_cart';
    const offcanvasBody = document.querySelector('.cart-offcanvas-wrapper .offcanvas-body');
    const offcanvasFooter = document.querySelector('.cart-offcanvas-wrapper .offcanvas-footer');
    const cartCountSpan = document.querySelector('.cart-action .cart-count');
    const cartTotalSpan = document.querySelector('.cart-offcanvas-wrapper .total-price');
    const itemTemplate = document.getElementById('guest-cart-item-template'); 
    const renderOffCanvasCart = (cartData) => {
        if (!offcanvasBody || !itemTemplate) return;
        offcanvasBody.innerHTML = '';
        const { items = [], total_quantity = 0, total_price = 0 } = cartData;
        if (items.length === 0) {
            offcanvasBody.innerHTML = '<p class="text-center p-4">Giỏ hàng của bạn đang trống.</p>';
            if(offcanvasFooter) offcanvasFooter.style.display = 'none';
        } else {
            items.forEach(item => {
                        const itemId = isGuest ? item.cartId : item.id;
                        const itemHtml = itemTemplate.innerHTML
                        .replace(/__ID__/g, itemId) 
                        .replace(/__NAME__/g, item.name)
                        .replace(/__PRICE__/g, Number(item.price).toLocaleString('vi-VN'))
                        .replace(/__QUANTITY__/g, item.quantity)
                        .replace(/__IMAGE__/g, item.image)
                        .replace(/__URL__/g, `/san-pham/${item.slug}`)
                        .replace(/__VARIANT__/g, item.variantText || '');
                        offcanvasBody.innerHTML += itemHtml;
                    });
            if(offcanvasFooter) offcanvasFooter.style.display = 'block';
        }
        if (cartCountSpan) cartCountSpan.innerText = total_quantity;
        if (cartTotalSpan) cartTotalSpan.innerText = Number(total_price).toLocaleString('vi-VN') + 'đ';
    };
    const getGuestCart = () => JSON.parse(localStorage.getItem(STORAGE_KEY)) || [];
    const saveGuestCart = (cart) => {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(cart));
        updateAndRenderGuestCart();
    };
    const updateAndRenderGuestCart = () => {
        const cart = getGuestCart();
        const total_quantity = cart.reduce((sum, item) => sum + item.quantity, 0);
        const total_price = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        renderOffCanvasCart({ items: cart, total_quantity, total_price });
    };
    window.addGuestCartItem = (productData) => {
        let cart = getGuestCart();
        let existingItem = cart.find(item => item.cartId === productData.cartId);
        if (existingItem) {
            existingItem.quantity += productData.quantity;
        } else {
            cart.push(productData);
        }
        saveGuestCart(cart);
        Swal.fire({ icon: 'success', title: 'Thành công!', text: 'Sản phẩm đã được thêm vào giỏ.' });
        document.body.classList.add('show-cart-offcanvas');
    };
    window.removeGuestCartItemFromOffCanvas = (productId) => {
        let cart = getGuestCart();
        cart = cart.filter(item => item.cartId != cartItemId);
        saveGuestCart(cart);
    };
    const addToCartAPI = (productId, quantity) => {
        $.post('/cart/add', {
            _token: csrf,
            product_id: productId,
            variant_id: variantId,
            quantity: quantity
        })
        .done(function(res) {
            if(res.success) {
                Swal.fire({ icon: 'success', title: 'Thành công!', text: 'Sản phẩm đã được thêm vào giỏ.' });
                renderOffCanvasCart(res.cart); 
                document.body.classList.add('show-cart-offcanvas');
            }
        })
        .fail(function() {
            Swal.fire({ icon: 'error', title: 'Lỗi!', text: 'Đã xảy ra lỗi, vui lòng thử lại.' });
        });
    };
    window.removeAuthCartItem = (cartItemId) => {
        $.post(`/cart/remove/${cartItemId}`, {
            _token: csrf,
        _method: 'DELETE' 
    })
        .done(function(res) {
            if (res.success) {
                console.log(res.message);
                renderOffCanvasCart(res.cart);
            }
        })
        .fail(function() {
            Swal.fire({ icon: 'error', title: 'Lỗi!', text: 'Không thể xóa sản phẩm, vui lòng thử lại.' });
        });
    };
    const mergeCartOnLogin = () => {
        const guestCart = getGuestCart();
        if (!isGuest && guestCart.length > 0) {
            console.log('Phát hiện giỏ hàng của khách, tiến hành gộp...');
            $.post('/cart/merge', {
                _token: csrf,
                guest_cart: guestCart
            })
            .done(function(res) {
                if(res.success) {
                    console.log('Gộp giỏ hàng thành công!');
                    localStorage.removeItem(STORAGE_KEY); 
                    location.reload(); 
                }
            })
            .fail(function() {
                console.error('Lỗi khi gộp giỏ hàng.');
            });
        }
    };
    $(document).on('click', '.btn-add-to-cart', function(e) {
        e.preventDefault();
          const el = this;
          const quantity  = parseInt(el.dataset.quantity || '1', 10) || 1;
          const productId = el.dataset.id;
          const variantId = el.dataset.variantId || null;
          const name      = el.dataset.name;
          const price     = parseFloat(el.dataset.price);
          const image     = el.dataset.image;
          const slug      = el.dataset.slug;

        const variantText = Array.from(document.querySelectorAll('.variant-selector:checked'))
    .map(input => {
      const group  = input.closest('.swatch');
      const label  = group?.querySelector(`label[for="${input.id}"]`)?.textContent?.trim() || '';
      return label ? `${label}` : label;
    })
    .join(' /\ ');

        if (isGuest) {
            const cartId = variantId ? `${productId}-${variantId}` : `${productId}`;
            const productData = {
              cartId,
              product_id: Number(productId),
              variant_id: variantId ? Number(variantId) : null,
              name,
              slug,
              image,
              price: Number(price),
              quantity: Number(quantity),
              variantText
            };
            addGuestCartItem(productData);
        } else {
            addToCartAPI(productId, variantId, quantity);
        }
    });
    $(document).on('click', '.cart-offcanvas-wrapper .item-remove', function(e) {
        e.preventDefault();
        const itemId = $(this).data('item-id');
        if (!itemId) return;
        if (isGuest) {
            let cart = getGuestCart();
            cart = cart.filter(item => item.cartId != itemId);
            saveGuestCart(cart);
        } else {
            removeAuthCartItem(itemId);
        }
    });
    if (isGuest) {
        updateAndRenderGuestCart(); 
    }
    mergeCartOnLogin();
});