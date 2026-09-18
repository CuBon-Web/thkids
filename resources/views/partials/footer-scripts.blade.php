@php
    $scripts = [
        'frontend/js/vendor/jquary-3.7.1.min.js',
        'frontend/js/vendor/bootstrap-bundle.js',
        'frontend/js/vendor/waypoints.min.js',
        'frontend/js/vendor/venobox.min.js',
        'frontend/js/vendor/odometer.min.js',
        'frontend/js/vendor/meanmenu.js',
        'frontend/js/vendor/swiper.min.js',
        'frontend/js/vendor/split-type.min.js',
        'frontend/js/vendor/gsap.min.js',
        'frontend/js/vendor/scroll-trigger.min.js',
        'frontend/js/vendor/jquery.event.move.min.js',
        'frontend/js/vendor/jquery.twentytwenty.min.js',
        'frontend/js/slider.js',
        'frontend/js/main.js',
    ];
@endphp
@foreach($scripts as $script)
<script src="{{ r2_asset($script) }}" defer></script>
@endforeach
@stack('vr-scripts')
