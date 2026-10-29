(function () {
    'use strict';

    function formatVnd(value) {
        var n = Number(value || 0);
        if (!n || n <= 0) return 'Liên hệ';
        return n.toLocaleString('vi-VN') + '₫';
    }

    function getCsrfToken() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    function initGallery() {
        var mainEl = document.querySelector('.product-detail-gallery-main');
        var thumbsEl = document.querySelector('.product-detail-gallery-thumbs');
        if (!mainEl || typeof Swiper === 'undefined') return;

        var thumbsSwiper = null;
        if (thumbsEl) {
            thumbsSwiper = new Swiper(thumbsEl, {
                spaceBetween: 8,
                slidesPerView: 'auto',
                watchSlidesProgress: true,
                freeMode: true,
            });
        }

        new Swiper(mainEl, {
            spaceBetween: 8,
            navigation: {
                nextEl: '.product-detail-gallery-next',
                prevEl: '.product-detail-gallery-prev',
            },
            thumbs: thumbsSwiper ? { swiper: thumbsSwiper } : undefined,
        });
    }

    function initTabs() {
        var nav = document.querySelector('.product-detail-tabs__nav');
        if (!nav) return;

        var buttons = nav.querySelectorAll('button[data-tab]');
        var panels = document.querySelectorAll('.product-detail-tabs__panel');

        buttons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                var target = btn.getAttribute('data-tab');
                buttons.forEach(function (b) { b.classList.remove('active'); });
                panels.forEach(function (p) { p.classList.remove('active'); });
                btn.classList.add('active');
                var panel = document.getElementById(target);
                if (panel) panel.classList.add('active');
            });
        });
    }

    function initQuantity() {
        var wrap = document.querySelector('.pd-quantity-control');
        if (!wrap) return;
        var input = wrap.querySelector('.pd-quantity-input');
        var minus = wrap.querySelector('.pd-qty-minus');
        var plus = wrap.querySelector('.pd-qty-plus');
        if (!input) return;

        function clamp() {
            var min = Number(input.min || 1);
            var max = Number(input.max || 999);
            var val = parseInt(input.value, 10);
            if (isNaN(val) || val < min) val = min;
            if (val > max) val = max;
            input.value = String(val);
            return val;
        }

        if (minus) {
            minus.addEventListener('click', function () {
                input.value = String(Math.max(Number(input.min || 1), clamp() - 1));
            });
        }
        if (plus) {
            plus.addEventListener('click', function () {
                input.value = String(Math.min(Number(input.max || 999), clamp() + 1));
            });
        }
        input.addEventListener('change', clamp);
        input.addEventListener('blur', clamp);
    }

    function initVariants(form) {
        if (!form) return;

        var statusVariant = Number(form.getAttribute('data-status-variant') || 0);
        var variants = [];
        var originalPrice = Number(form.getAttribute('data-original-price') || 0);
        var salePrice = Number(form.getAttribute('data-sale-price') || 0);

        try {
            variants = JSON.parse(form.getAttribute('data-variants') || '[]') || [];
        } catch (e) {
            variants = [];
        }

        var priceCurrent = document.getElementById('pd-price-current');
        var priceOld = document.getElementById('pd-price-old');

        function selectedValues() {
            var values = [];
            form.querySelectorAll('.pd-variant-group').forEach(function (group) {
                var checked = group.querySelector('input[type="radio"]:checked');
                var selectedLabel = group.querySelector('.pd-variant-selected');
                if (checked) {
                    values.push(checked.value);
                    if (selectedLabel) selectedLabel.textContent = checked.value;
                }
                group.querySelectorAll('.pd-variant-option').forEach(function (opt) {
                    var input = opt.querySelector('input');
                    opt.classList.toggle('is-active', !!(input && input.checked));
                });
            });
            return values;
        }

        function matchVariant(values) {
            if (!values.length || !variants.length) return null;
            var key = values.join(' / ');
            var found = variants.find(function (v) {
                return String(v.version || '') === key || String(v.version || '') === values.join('-');
            });
            if (found) return found;
            return variants.find(function (v) {
                var ver = String(v.version || '');
                return values.every(function (val) { return ver.indexOf(val) !== -1; });
            }) || null;
        }

        function updatePrice() {
            var values = selectedValues();
            var matched = statusVariant === 1 ? matchVariant(values) : null;
            var current = salePrice > 0 && (originalPrice <= 0 || salePrice < originalPrice) ? salePrice : originalPrice;
            var old = originalPrice;
            var availableQty = Number(form.getAttribute('data-stock-qty') || 0);

            if (matched) {
                var vSale = Number(matched.discount || matched.price || 0);
                var vOld = Number(matched.price || 0);
                if (vSale > 0) current = vSale;
                if (vOld > 0 && vOld > current) old = vOld;
                else old = 0;
                availableQty = Number(matched.qty || 0);
            }

            if (priceCurrent) priceCurrent.textContent = formatVnd(current);
            if (priceOld) {
                if (old > 0 && old > current) {
                    priceOld.textContent = formatVnd(old);
                    priceOld.style.display = '';
                } else {
                    priceOld.style.display = 'none';
                }
            }
            form.setAttribute('data-current-price', String(current > 0 ? current : 0));
            form.setAttribute('data-current-variant', values.join(' / '));
            form.setAttribute('data-available-qty', String(Math.max(0, availableQty)));

            var stockStatus = document.getElementById('pd-stock-status');
            var stockMessage = document.getElementById('pd-stock-message');
            var qtyInput = form.querySelector('.pd-quantity-input');
            var btnCart = form.querySelector('.pd-btn--cart');
            var btnBuy = form.querySelector('.pd-btn--buy');
            var inStock = availableQty > 0;

            if (stockStatus) {
                stockStatus.textContent = inStock ? 'Còn hàng' : 'Hết hàng';
                stockStatus.style.color = inStock ? '#16a34a' : '#c8103d';
            }
            if (stockMessage) {
                stockMessage.style.display = inStock ? 'none' : '';
                stockMessage.textContent = inStock ? '' : 'Phân loại này đã hết hàng';
            }
            if (qtyInput) {
                qtyInput.max = String(Math.max(1, availableQty || 1));
                if (Number(qtyInput.value || 1) > availableQty && availableQty > 0) {
                    qtyInput.value = String(availableQty);
                }
            }
            if (btnCart) btnCart.disabled = !inStock;
            if (btnBuy) btnBuy.disabled = !inStock;
        }

        form.querySelectorAll('.pd-variant-option input').forEach(function (input) {
            input.addEventListener('change', updatePrice);
        });
        updatePrice();
    }

    function updateHeaderCartCount(count) {
        var value = Number(count || 0);
        document.querySelectorAll('.count_item_pr, #header-cart-count').forEach(function (el) {
            el.textContent = String(value);
            if (value > 0) {
                el.classList.add('has-items');
            } else {
                el.classList.remove('has-items');
            }
        });
        window.dispatchEvent(new CustomEvent('cart:updated', { detail: { cart_count: value } }));
    }

    function showCartToast(message, isError) {
        var existing = document.querySelector('.pd-cart-toast');
        if (existing) existing.remove();

        var toast = document.createElement('div');
        toast.className = 'pd-cart-toast' + (isError ? ' is-error' : '');
        toast.textContent = message || 'Đã thêm vào giỏ hàng';
        document.body.appendChild(toast);
        requestAnimationFrame(function () {
            toast.classList.add('is-visible');
        });
        setTimeout(function () {
            toast.classList.remove('is-visible');
            setTimeout(function () { toast.remove(); }, 250);
        }, 2200);
    }

    function initPurchase(form) {
        if (!form) return;

        var addUrl = form.getAttribute('data-add-cart-url');
        var checkoutUrl = form.getAttribute('data-checkout-url');
        var productId = form.getAttribute('data-product-id');
        var qtyInput = form.querySelector('.pd-quantity-input');
        var btnCart = form.querySelector('.pd-btn--cart');
        var btnBuy = form.querySelector('.pd-btn--buy');
        var busy = false;

        function setBusy(state, goCheckout) {
            busy = state;
            [btnCart, btnBuy].forEach(function (btn) {
                if (!btn) return;
                btn.disabled = state;
            });
            if (btnCart) {
                btnCart.textContent = state && !goCheckout ? 'Đang thêm...' : 'Thêm vào giỏ hàng';
            }
            if (btnBuy) {
                btnBuy.textContent = state && goCheckout ? 'Đang xử lý...' : 'Mua ngay';
            }
        }

        function addToCart(goCheckout) {
            if (!addUrl || !productId || busy) return;

            var quantity = qtyInput ? Math.max(1, parseInt(qtyInput.value, 10) || 1) : 1;
            var price = Number(
                form.getAttribute('data-current-price') ||
                form.getAttribute('data-sale-price') ||
                form.getAttribute('data-original-price') ||
                0
            );
            var variant = form.getAttribute('data-current-variant') || '';
            var statusVariant = Number(form.getAttribute('data-status-variant') || 0);

            if (statusVariant === 1 && !variant) {
                showCartToast('Vui lòng chọn phân loại sản phẩm', true);
                return;
            }

            var availableQty = Number(form.getAttribute('data-available-qty') || form.getAttribute('data-stock-qty') || 0);
            if (availableQty <= 0) {
                showCartToast('Sản phẩm đã hết hàng', true);
                return;
            }
            if (quantity > availableQty) {
                showCartToast('Chỉ còn ' + availableQty + ' sản phẩm trong kho', true);
                return;
            }

            setBusy(true, goCheckout);

            var body = new URLSearchParams();
            body.set('product_id', String(productId));
            body.set('quantity', String(quantity));
            body.set('price', String(price > 0 ? price : 0));
            body.set('variant', variant);
            body.set('_token', getCsrfToken());

            fetch(addUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': getCsrfToken(),
                },
                body: body.toString(),
                credentials: 'same-origin',
            })
                .then(function (res) {
                    return res.json().then(function (data) {
                        return { ok: res.ok, status: res.status, data: data };
                    });
                })
                .then(function (result) {
                    var data = result.data || {};
                    if (!result.ok || !data.success) {
                        var msg = data.message || 'Không thể thêm vào giỏ hàng';
                        if (data.errors) {
                            var first = Object.values(data.errors)[0];
                            if (Array.isArray(first) && first[0]) msg = first[0];
                        }
                        showCartToast(msg, true);
                        return;
                    }

                    if (typeof data.cart_count !== 'undefined') {
                        updateHeaderCartCount(data.cart_count);
                    }

                    if (goCheckout) {
                        window.location.href = data.redirect_checkout || checkoutUrl || '/thanh-toan.html';
                        return;
                    }

                    showCartToast(data.message || 'Đã thêm vào giỏ hàng');
                })
                .catch(function () {
                    showCartToast('Có lỗi xảy ra, vui lòng thử lại', true);
                })
                .finally(function () {
                    setBusy(false, false);
                });
        }

        if (btnCart) {
            btnCart.addEventListener('click', function (e) {
                e.preventDefault();
                addToCart(false);
            });
        }
        if (btnBuy) {
            btnBuy.addEventListener('click', function (e) {
                e.preventDefault();
                addToCart(true);
            });
        }
    }

    function boot() {
        initGallery();
        initTabs();
        initQuantity();
        var form = document.getElementById('product-purchase-form');
        initVariants(form);
        initPurchase(form);
    }

    window.updateHeaderCartCount = updateHeaderCartCount;

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }
})();
