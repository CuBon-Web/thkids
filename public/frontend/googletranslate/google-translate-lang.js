/**
 * EN / VI — HTML gốc từ server là tiếng Anh (database).
 * EN = không dùng widget. VI = Google Translate (en → vi).
 */
(function (global) {
    'use strict';

    var PAGE_LANG = 'en';
    var DEFAULT_LANG = 'en';
    var STORAGE_KEY = 'gt_site_lang';
    var COOKIE_NAME = 'googtrans';

    function readCookie() {
        var match = document.cookie.match(/(?:^|;\s*)googtrans=([^;]*)/);
        return match ? decodeURIComponent(match[1]) : '';
    }

    function purgeGoogTransCookies() {
        var expired = COOKIE_NAME + '=;path=/;expires=Thu, 01 Jan 1970 00:00:00 GMT';
        var host = global.location.hostname;
        var domains = [null];

        if (host && host.indexOf('.') !== -1 && host !== 'localhost') {
            var root = host.replace(/^www\./, '');
            domains.push('.' + root, root);
            if (host.indexOf('www.') === 0) {
                domains.push(host);
            }
        }

        domains.forEach(function (domain) {
            document.cookie = domain ? expired + ';domain=' + domain : expired;
        });
    }

    function setVietnameseCookie() {
        var secure = global.location.protocol === 'https:' ? ';Secure' : '';
        purgeGoogTransCookies();
        document.cookie =
            COOKIE_NAME +
            '=' +
            encodeURIComponent('/' + PAGE_LANG + '/vi') +
            ';path=/;max-age=31536000;SameSite=Lax' +
            secure;
    }

    function stripTranslateArtifacts() {
        var html = document.documentElement;
        if (html) {
            html.classList.remove('translated-ltr', 'translated-rtl');
            html.removeAttribute('lang');
        }
        document.querySelectorAll('font[style*="vertical-align"]').forEach(function (node) {
            var parent = node.parentNode;
            if (!parent) {
                return;
            }
            while (node.firstChild) {
                parent.insertBefore(node.firstChild, node);
            }
            parent.removeChild(node);
        });
    }

    function getSavedLang() {
        try {
            var stored = global.localStorage.getItem(STORAGE_KEY);
            if (stored === 'en' || stored === 'vi') {
                return stored;
            }
        } catch (e) {}

        var cookie = readCookie();
        if (cookie.indexOf('/vi') !== -1) {
            return 'vi';
        }

        return DEFAULT_LANG;
    }

    function shouldUseTranslate() {
        return getSavedLang() === 'vi';
    }

    function updateButtons() {
        var active = getSavedLang();
        document.querySelectorAll('.gt-lang [data-gt-lang]').forEach(function (btn) {
            var lang = btn.getAttribute('data-gt-lang');
            var isActive = lang === active;
            btn.classList.toggle('is-active', isActive);
            btn.setAttribute('aria-pressed', isActive ? 'true' : 'false');
        });
    }

    function switchLanguage(lang) {
        if (lang !== 'vi' && lang !== 'en') {
            return;
        }
        if (lang === getSavedLang()) {
            return;
        }

        document.querySelectorAll('.gt-lang').forEach(function (el) {
            el.classList.add('is-loading');
        });

        try {
            global.localStorage.setItem(STORAGE_KEY, lang);
        } catch (e) {}

        if (lang === 'vi') {
            setVietnameseCookie();
        } else {
            purgeGoogTransCookies();
        }

        global.location.reload();
    }

    function loadGoogleTranslate() {
        if (!shouldUseTranslate()) {
            stripTranslateArtifacts();
            return;
        }

        if (document.getElementById('google-translate-script')) {
            return;
        }

        setVietnameseCookie();

        global.googleTranslateElementInit = function () {
            if (!global.google || !global.google.translate) {
                updateButtons();
                return;
            }

            new global.google.translate.TranslateElement(
                {
                    pageLanguage: PAGE_LANG,
                    includedLanguages: 'en,vi',
                    autoDisplay: false,
                },
                'google_translate_element'
            );

            updateButtons();

            global.setTimeout(function () {
                var combo = document.querySelector('select.goog-te-combo');
                if (combo && combo.value !== 'vi') {
                    combo.value = 'vi';
                    combo.dispatchEvent(new Event('change'));
                }
            }, 300);
        };

        var script = document.createElement('script');
        script.id = 'google-translate-script';
        script.src = 'https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit';
        script.async = true;
        document.body.appendChild(script);
    }

    function bindSwitchers() {
        document.querySelectorAll('.gt-lang').forEach(function (root) {
            if (root.getAttribute('data-gt-inited') === '1') {
                return;
            }

            root.querySelectorAll('[data-gt-lang]').forEach(function (btn) {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    switchLanguage(btn.getAttribute('data-gt-lang'));
                });
            });

            root.setAttribute('data-gt-inited', '1');
        });

        updateButtons();
    }

    function init() {
        if (!shouldUseTranslate()) {
            purgeGoogTransCookies();
            stripTranslateArtifacts();
        }

        bindSwitchers();
        loadGoogleTranslate();
    }

    global.GTLang = {
        init: init,
        getSavedLang: getSavedLang,
        switchLanguage: switchLanguage,
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})(window);
