(function () {
    'use strict';

    function getCsrfToken() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    function formatVnd(value) {
        return Number(value || 0).toLocaleString('vi-VN') + '₫';
    }

    function showToast(message, isError) {
        var existing = document.querySelector('.checkout-page-toast');
        if (existing) existing.remove();

        var toast = document.createElement('div');
        toast.className = 'checkout-page-toast' + (isError ? ' is-error' : '');
        toast.textContent = message || '';
        document.body.appendChild(toast);
        requestAnimationFrame(function () {
            toast.classList.add('is-visible');
        });
        setTimeout(function () {
            toast.classList.remove('is-visible');
            setTimeout(function () { toast.remove(); }, 250);
        }, 2200);
    }

    function updateHeaderCartCount(count) {
        var value = Number(count || 0);
        document.querySelectorAll('.count_item_pr, #header-cart-count').forEach(function (el) {
            el.textContent = String(value);
            el.classList.toggle('has-items', value > 0);
        });
    }

    function postForm(url, data) {
        var body = new URLSearchParams();
        Object.keys(data || {}).forEach(function (key) {
            body.set(key, String(data[key]));
        });
        body.set('_token', getCsrfToken());

        return fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': getCsrfToken(),
            },
            body: body.toString(),
            credentials: 'same-origin',
        }).then(function (res) {
            return res.json().then(function (json) {
                return { ok: res.ok, data: json || {} };
            }).catch(function () {
                return { ok: false, data: { message: 'Phản hồi không hợp lệ' } };
            });
        });
    }

    function initCheckoutPage() {
        var root = document.getElementById('checkout-page');
        if (!root) return;

        var updateUrl = root.getAttribute('data-update-url');
        var removeUrl = root.getAttribute('data-remove-url');
        var cartUrl = root.getAttribute('data-cart-url') || '/gio-hang.html';
        var shippingFee = Number(root.getAttribute('data-shipping-fee') || 0);

        var form = document.getElementById('checkout-form');
        var productsEl = document.getElementById('checkout-products');
        var totalMoneyInput = document.getElementById('checkout-total-money');
        var shippingMethodInput = document.getElementById('checkout-shipping-method');
        var paymentMethodInput = document.getElementById('checkout-payment-method');
        var subtotalDisplay = document.getElementById('checkout-subtotal-display');
        var shippingDisplay = document.getElementById('checkout-shipping-display');
        var totalDisplay = document.getElementById('checkout-total-display');
        var submitBtn = form ? form.querySelector('.checkout-submit-btn') : null;

        function recalculateFromDom() {
            var subtotal = 0;
            root.querySelectorAll('.single-product').forEach(function (item) {
                var price = Number(item.getAttribute('data-price') || 0);
                var input = item.querySelector('.js-qty-input');
                var qty = input ? Math.max(1, parseInt(input.value, 10) || 1) : 1;
                var line = price * qty;
                subtotal += line;
                var lineEl = item.querySelector('.js-line-total');
                if (lineEl) lineEl.textContent = formatVnd(line);
            });

            var grand = subtotal + shippingFee;
            if (subtotalDisplay) subtotalDisplay.textContent = formatVnd(subtotal);
            if (shippingDisplay) shippingDisplay.textContent = formatVnd(shippingFee);
            if (totalDisplay) totalDisplay.textContent = formatVnd(grand);
            if (totalMoneyInput) totalMoneyInput.value = String(grand);
            if (shippingMethodInput) shippingMethodInput.value = String(shippingFee);
            return { subtotal: subtotal, grand: grand };
        }

        function syncPaymentChoice() {
            var checked = root.querySelector('.js-payment-choice:checked');
            root.querySelectorAll('.payment-check').forEach(function (label) {
                var input = label.querySelector('.js-payment-choice');
                label.classList.toggle('is-checked', !!(input && input.checked));
            });
            if (paymentMethodInput && checked) {
                paymentMethodInput.value = checked.value;
            }
        }

        function changeQty(item, delta) {
            if (!updateUrl || item.classList.contains('is-updating')) return;
            var key = item.getAttribute('data-id');
            var input = item.querySelector('.js-qty-input');
            if (!key || !input) return;

            var current = Math.max(1, parseInt(input.value, 10) || 1);
            var next = Math.max(1, current + delta);
            if (next === current) return;

            item.classList.add('is-updating');
            postForm(updateUrl, { id: key, quantity: next })
                .then(function (result) {
                    if (!result.ok || !result.data.success) {
                        showToast((result.data && result.data.message) || 'Không thể cập nhật số lượng', true);
                        return;
                    }
                    input.value = String(result.data.quantity || next);
                    if (typeof result.data.line_total !== 'undefined') {
                        var lineEl = item.querySelector('.js-line-total');
                        if (lineEl) lineEl.textContent = formatVnd(result.data.line_total);
                    }
                    if (typeof result.data.total !== 'undefined') {
                        var grand = Number(result.data.total) + shippingFee;
                        if (subtotalDisplay) subtotalDisplay.textContent = formatVnd(result.data.total);
                        if (totalDisplay) totalDisplay.textContent = formatVnd(grand);
                        if (totalMoneyInput) totalMoneyInput.value = String(grand);
                    } else {
                        recalculateFromDom();
                    }
                    if (typeof result.data.cart_count !== 'undefined') {
                        updateHeaderCartCount(result.data.cart_count);
                    }
                })
                .catch(function () {
                    showToast('Có lỗi xảy ra, vui lòng thử lại', true);
                })
                .finally(function () {
                    item.classList.remove('is-updating');
                });
        }

        function removeItem(item) {
            if (!removeUrl || item.classList.contains('is-updating')) return;
            var key = item.getAttribute('data-id');
            if (!key) return;
            if (!window.confirm('Xóa sản phẩm này khỏi đơn hàng?')) return;

            item.classList.add('is-updating');
            postForm(removeUrl, { id: key })
                .then(function (result) {
                    if (!result.ok || !result.data.success) {
                        showToast((result.data && result.data.message) || 'Không thể xóa sản phẩm', true);
                        item.classList.remove('is-updating');
                        return;
                    }
                    item.remove();
                    if (typeof result.data.cart_count !== 'undefined') {
                        updateHeaderCartCount(result.data.cart_count);
                    }
                    if (Number(result.data.cart_count || 0) <= 0) {
                        window.location.href = cartUrl;
                        return;
                    }
                    if (typeof result.data.total !== 'undefined') {
                        var grand = Number(result.data.total) + shippingFee;
                        if (subtotalDisplay) subtotalDisplay.textContent = formatVnd(result.data.total);
                        if (totalDisplay) totalDisplay.textContent = formatVnd(grand);
                        if (totalMoneyInput) totalMoneyInput.value = String(grand);
                    } else {
                        recalculateFromDom();
                    }
                    showToast('Đã xóa sản phẩm');
                })
                .catch(function () {
                    item.classList.remove('is-updating');
                    showToast('Có lỗi xảy ra, vui lòng thử lại', true);
                });
        }

        if (productsEl) {
            productsEl.addEventListener('click', function (event) {
                var item = event.target.closest('.single-product');
                if (!item) return;
                if (event.target.closest('.js-qty-minus')) {
                    event.preventDefault();
                    changeQty(item, -1);
                    return;
                }
                if (event.target.closest('.js-qty-plus')) {
                    event.preventDefault();
                    changeQty(item, 1);
                    return;
                }
                if (event.target.closest('.js-remove-item')) {
                    event.preventDefault();
                    removeItem(item);
                }
            });

            productsEl.addEventListener('change', function (event) {
                if (!event.target.classList.contains('js-qty-input')) return;
                var item = event.target.closest('.single-product');
                if (!item || !updateUrl) return;
                var key = item.getAttribute('data-id');
                var next = Math.max(1, parseInt(event.target.value, 10) || 1);
                event.target.value = String(next);
                item.classList.add('is-updating');
                postForm(updateUrl, { id: key, quantity: next })
                    .then(function (result) {
                        if (!result.ok || !result.data.success) {
                            showToast((result.data && result.data.message) || 'Không thể cập nhật số lượng', true);
                            return;
                        }
                        if (typeof result.data.line_total !== 'undefined') {
                            var lineEl = item.querySelector('.js-line-total');
                            if (lineEl) lineEl.textContent = formatVnd(result.data.line_total);
                        }
                        if (typeof result.data.total !== 'undefined') {
                            var grand = Number(result.data.total) + shippingFee;
                            if (subtotalDisplay) subtotalDisplay.textContent = formatVnd(result.data.total);
                            if (totalDisplay) totalDisplay.textContent = formatVnd(grand);
                            if (totalMoneyInput) totalMoneyInput.value = String(grand);
                        } else {
                            recalculateFromDom();
                        }
                        if (typeof result.data.cart_count !== 'undefined') {
                            updateHeaderCartCount(result.data.cart_count);
                        }
                    })
                    .catch(function () {
                        showToast('Có lỗi xảy ra, vui lòng thử lại', true);
                    })
                    .finally(function () {
                        item.classList.remove('is-updating');
                    });
            });
        }

        root.querySelectorAll('.js-payment-choice').forEach(function (input) {
            input.addEventListener('change', syncPaymentChoice);
        });
        syncPaymentChoice();

        if (form) {
            form.addEventListener('submit', function (event) {
                var name = (document.getElementById('billingName') || {}).value || '';
                var phone = (document.getElementById('billingPhone') || {}).value || '';
                var email = (document.getElementById('billingEmail') || {}).value || '';
                var address = (document.getElementById('billingAddress') || {}).value || '';

                if (!name.trim() || !phone.trim() || !address.trim()) {
                    event.preventDefault();
                    showToast('Vui lòng điền đầy đủ thông tin nhận hàng', true);
                    return;
                }

                if (!/^0\d{8,10}$/.test(phone.replace(/[\s.-]/g, ''))) {
                    event.preventDefault();
                    showToast('Số điện thoại không hợp lệ', true);
                    return;
                }

                if (email.trim() && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.trim())) {
                    event.preventDefault();
                    showToast('Email không hợp lệ', true);
                    return;
                }

                recalculateFromDom();
                syncPaymentChoice();

                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Đang đặt hàng...';
                }
            });
        }

        recalculateFromDom();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCheckoutPage);
    } else {
        initCheckoutPage();
    }
})();
