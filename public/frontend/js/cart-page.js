(function () {
    'use strict';

    var routes = window.cartPageRoutes || {};

    function getCsrfToken() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    function formatVnd(value) {
        var n = Number(value || 0);
        return n.toLocaleString('vi-VN') + '₫';
    }

    function updateHeaderCartCount(count) {
        var value = Number(count || 0);
        document.querySelectorAll('.count_item_pr, #header-cart-count').forEach(function (el) {
            el.textContent = String(value);
            el.classList.toggle('has-items', value > 0);
        });
        if (typeof window.updateHeaderCartCount === 'function' && window.updateHeaderCartCount !== updateHeaderCartCount) {
            try { window.updateHeaderCartCount(value); } catch (e) {}
        }
    }

    function showToast(message, isError) {
        var existing = document.querySelector('.cart-page-toast');
        if (existing) existing.remove();

        var toast = document.createElement('div');
        toast.className = 'cart-page-toast' + (isError ? ' is-error' : '');
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
                return { ok: res.ok, status: res.status, data: json || {} };
            }).catch(function () {
                return { ok: false, status: res.status, data: { message: 'Phản hồi không hợp lệ' } };
            });
        });
    }

    function updateCountLabels(count) {
        var text = count + ' sản phẩm';
        document.querySelectorAll('.js-cart-count-label').forEach(function (el) {
            if (el.classList.contains('cart-page-count') || el.closest('.cart-page-title')) {
                el.textContent = '(' + text + ')';
            } else {
                el.textContent = text;
            }
        });
    }

    function updateTotals(data) {
        if (typeof data.total !== 'undefined') {
            var totalEl = document.querySelector('.js-cart-total');
            if (totalEl) totalEl.textContent = formatVnd(data.total);
        }
        if (typeof data.cart_count !== 'undefined') {
            updateCountLabels(data.cart_count);
            updateHeaderCartCount(data.cart_count);
        }
    }

    function reloadIfEmpty(cartCount) {
        if (Number(cartCount || 0) <= 0) {
            window.location.reload();
        }
    }

    function initCartPage() {
        var cartRoot = document.querySelector('.js-cart');
        if (!cartRoot) return;

        cartRoot.addEventListener('click', function (event) {
            var item = event.target.closest('.cart-item');
            if (!item) return;

            var key = item.getAttribute('data-cart-key');
            var input = item.querySelector('.js-quantity');
            if (!key || !input) return;

            if (event.target.closest('.cart-qty__btn--minus')) {
                event.preventDefault();
                changeQty(item, key, input, -1);
                return;
            }
            if (event.target.closest('.cart-qty__btn--plus')) {
                event.preventDefault();
                changeQty(item, key, input, 1);
                return;
            }
            if (event.target.closest('.cart-item__remove')) {
                event.preventDefault();
                removeItem(item, key);
            }
        });

        var clearBtn = document.querySelector('.js-clearcart');
        if (clearBtn) {
            clearBtn.addEventListener('click', function (event) {
                event.preventDefault();
                if (!window.confirm('Bạn có chắc muốn xóa tất cả sản phẩm trong giỏ?')) return;
                if (!routes.clear) return;

                clearBtn.disabled = true;
                postForm(routes.clear, {})
                    .then(function (result) {
                        if (!result.ok || !result.data.success) {
                            showToast((result.data && result.data.message) || 'Không thể xóa giỏ hàng', true);
                            return;
                        }
                        updateHeaderCartCount(0);
                        window.location.reload();
                    })
                    .catch(function () {
                        showToast('Có lỗi xảy ra, vui lòng thử lại', true);
                    })
                    .finally(function () {
                        clearBtn.disabled = false;
                    });
            });
        }
    }

    function changeQty(item, key, input, delta) {
        if (!routes.update || item.classList.contains('is-updating')) return;

        var current = Math.max(1, parseInt(input.value, 10) || 1);
        var next = current + delta;
        if (next < 1) next = 1;
        if (next === current) return;

        item.classList.add('is-updating');
        postForm(routes.update, { id: key, quantity: next })
            .then(function (result) {
                if (!result.ok || !result.data.success) {
                    showToast((result.data && result.data.message) || 'Không thể cập nhật số lượng', true);
                    return;
                }
                input.value = String(result.data.quantity || next);
                var lineEl = item.querySelector('.js-line-total');
                if (lineEl && typeof result.data.line_total !== 'undefined') {
                    lineEl.textContent = formatVnd(result.data.line_total);
                }
                updateTotals(result.data);
                showToast('Đã cập nhật số lượng');
            })
            .catch(function () {
                showToast('Có lỗi xảy ra, vui lòng thử lại', true);
            })
            .finally(function () {
                item.classList.remove('is-updating');
            });
    }

    function removeItem(item, key) {
        if (!routes.remove || item.classList.contains('is-updating')) return;
        if (!window.confirm('Xóa sản phẩm này khỏi giỏ hàng?')) return;

        item.classList.add('is-updating');
        postForm(routes.remove, { id: key })
            .then(function (result) {
                if (!result.ok || !result.data.success) {
                    showToast((result.data && result.data.message) || 'Không thể xóa sản phẩm', true);
                    item.classList.remove('is-updating');
                    return;
                }
                item.remove();
                updateTotals(result.data);
                showToast('Đã xóa sản phẩm');
                reloadIfEmpty(result.data.cart_count);
            })
            .catch(function () {
                item.classList.remove('is-updating');
                showToast('Có lỗi xảy ra, vui lòng thử lại', true);
            });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCartPage);
    } else {
        initCartPage();
    }
})();
