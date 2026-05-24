/**
 * Subtle parallax on sections with .parallax-bg (CSS background-image).
 * Respects prefers-reduced-motion.
 */
(function () {
    if (typeof window.matchMedia === 'function' && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        return;
    }

    var ticking = false;

    function updateParallaxBackgrounds() {
        ticking = false;
        var nodes = document.querySelectorAll('.parallax-bg');
        var vh = window.innerHeight || document.documentElement.clientHeight || 600;
        var mid = vh / 2;

        nodes.forEach(function (el) {
            var cs = window.getComputedStyle(el);
            var bg = cs.backgroundImage;
            if (!bg || bg === 'none') {
                return;
            }

            var rect = el.getBoundingClientRect();
            var speed = parseFloat(el.getAttribute('data-parallax-speed'));
            if (isNaN(speed)) {
                speed = 0.14;
            }

            var elCenter = rect.top + rect.height / 2;
            var shift = (mid - elCenter) * speed;
            el.style.backgroundPosition = 'center calc(50% + ' + shift.toFixed(2) + 'px)';
        });
    }

    function requestTick() {
        if (!ticking) {
            ticking = true;
            requestAnimationFrame(updateParallaxBackgrounds);
        }
    }

    window.addEventListener('scroll', requestTick, { passive: true });
    window.addEventListener('resize', requestTick, { passive: true });

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', updateParallaxBackgrounds);
    } else {
        updateParallaxBackgrounds();
    }

    window.initParallaxBackgrounds = updateParallaxBackgrounds;
})();
