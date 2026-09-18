/**
 * Call Button Widget (cbw) — reusable floating contact menu
 *
 * Usage:
 *   1. Include call-button.css and call-button.js
 *   2. Add .cbw markup (see partials/call-button.blade.php)
 *   3. Or init programmatically: CallButtonWidget.init('.cbw', { phone: '...' })
 *
 * Data attributes on root .cbw:
 *   data-cbw-phone, data-cbw-facebook, data-cbw-instagram, data-cbw-tiktok
 *   data-cbw-position="right"|"left"
 *   data-cbw-theme="brand"|"light"
 *   data-cbw-above-totop="true" — lift above .totop button
 */
(function (global) {
    'use strict';

    var SELECTOR = '.cbw';
    var OPEN_CLASS = 'is-open';

    function normalizePhone(value) {
        if (!value) return '';
        var digits = String(value).replace(/[^\d+]/g, '');
        return digits || '';
    }

    function normalizeUrl(value) {
        if (!value || typeof value !== 'string') return '';
        var url = value.trim();
        if (!url) return '';
        if (/^(tel:|mailto:|https?:)/i.test(url)) return url;
        return 'https://' + url.replace(/^\/+/, '');
    }

    function parseConfig(root) {
        return {
            phone: root.getAttribute('data-cbw-phone') || '',
            facebook: root.getAttribute('data-cbw-facebook') || '',
            instagram: root.getAttribute('data-cbw-instagram') || '',
            tiktok: root.getAttribute('data-cbw-tiktok') || '',
            position: root.getAttribute('data-cbw-position') || 'right',
            theme: root.getAttribute('data-cbw-theme') || 'brand',
            aboveTotop: root.getAttribute('data-cbw-above-totop') === 'true',
        };
    }

    function applyConfig(root, config) {
        if (config.phone) root.setAttribute('data-cbw-phone', config.phone);
        if (config.facebook) root.setAttribute('data-cbw-facebook', config.facebook);
        if (config.instagram) root.setAttribute('data-cbw-instagram', config.instagram);
        if (config.tiktok) root.setAttribute('data-cbw-tiktok', config.tiktok);
    }

    function bindItem(root, selector, href, visible) {
        var el = root.querySelector(selector);
        if (!el) return;
        if (!visible || !href) {
            el.classList.remove('is-visible');
            el.setAttribute('hidden', 'hidden');
            el.removeAttribute('href');
            return;
        }
        el.classList.add('is-visible');
        el.removeAttribute('hidden');
        el.setAttribute('href', href);
    }

    function syncLinks(root) {
        var cfg = parseConfig(root);
        var phoneHref = normalizePhone(cfg.phone);
        if (phoneHref && phoneHref.indexOf('+') !== 0 && phoneHref.indexOf('0') === 0) {
            phoneHref = '+84' + phoneHref.replace(/^0/, '');
        }
        if (phoneHref && phoneHref.indexOf('tel:') !== 0) {
            phoneHref = 'tel:' + phoneHref;
        }

        bindItem(root, '[data-cbw-link="phone"]', phoneHref, !!cfg.phone);
        bindItem(root, '[data-cbw-link="facebook"]', normalizeUrl(cfg.facebook), !!cfg.facebook);
        bindItem(root, '[data-cbw-link="instagram"]', normalizeUrl(cfg.instagram), !!cfg.instagram);
        bindItem(root, '[data-cbw-link="tiktok"]', normalizeUrl(cfg.tiktok), !!cfg.tiktok);

        var anyVisible = root.querySelector('.cbw__item.is-visible');
        root.classList.toggle('cbw--has-links', !!anyVisible);
    }

    function close(root) {
        root.classList.remove(OPEN_CLASS);
        var toggle = root.querySelector('.cbw__toggle');
        if (toggle) toggle.setAttribute('aria-expanded', 'false');
    }

    function open(root) {
        root.classList.add(OPEN_CLASS);
        var toggle = root.querySelector('.cbw__toggle');
        if (toggle) toggle.setAttribute('aria-expanded', 'true');
    }

    function toggle(root) {
        if (root.classList.contains(OPEN_CLASS)) {
            close(root);
        } else {
            open(root);
        }
    }

    function initOne(root) {
        if (!root || root.getAttribute('data-cbw-inited') === '1') return root;

        var cfg = parseConfig(root);
        if (cfg.position === 'left') root.classList.add('cbw--left');
        if (cfg.theme === 'light') root.classList.add('cbw--theme-light');
        if (cfg.aboveTotop) root.classList.add('cbw--above-totop');

        syncLinks(root);

        var toggleBtn = root.querySelector('.cbw__toggle');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                toggle(root);
            });
        }

        root.querySelectorAll('.cbw__item').forEach(function (link) {
            link.addEventListener('click', function () {
                close(root);
            });
        });

        document.addEventListener('click', function (e) {
            if (!root.classList.contains(OPEN_CLASS)) return;
            if (!root.contains(e.target)) close(root);
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') close(root);
        });

        /* Hint on first visit */
        var hint = root.querySelector('.cbw__hint');
        if (hint && !sessionStorage.getItem('cbw-hint-seen')) {
            setTimeout(function () {
                hint.classList.add('cbw__hint--show');
                setTimeout(function () {
                    hint.classList.remove('cbw__hint--show');
                    sessionStorage.setItem('cbw-hint-seen', '1');
                }, 4000);
            }, 1500);
        }

        root.setAttribute('data-cbw-inited', '1');
        return root;
    }

    function init(selectorOrRoot, config) {
        var nodes;
        if (typeof selectorOrRoot === 'string') {
            nodes = document.querySelectorAll(selectorOrRoot || SELECTOR);
        } else if (selectorOrRoot instanceof Element) {
            nodes = [selectorOrRoot];
        } else {
            nodes = document.querySelectorAll(SELECTOR);
        }

        nodes.forEach(function (root) {
            if (config) applyConfig(root, config);
            initOne(root);
        });
    }

    global.CallButtonWidget = {
        init: init,
        closeAll: function () {
            document.querySelectorAll(SELECTOR + '.' + OPEN_CLASS).forEach(close);
        },
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            init(SELECTOR);
        });
    } else {
        init(SELECTOR);
    }
})(typeof window !== 'undefined' ? window : this);
