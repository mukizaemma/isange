/**
 * Toggle RWF suffix on dual-currency price spans (click / tap / keyboard).
 */
(function () {
  function toggle(el) {
    el.classList.toggle('dual-currency--open');
    var open = el.classList.contains('dual-currency--open');
    el.setAttribute('aria-expanded', open ? 'true' : 'false');
  }

  document.addEventListener('click', function (e) {
    var el = e.target.closest('.js-dual-currency');
    if (!el) return;
    e.preventDefault();
    toggle(el);
  });

  document.addEventListener('keydown', function (e) {
    if (e.key !== 'Enter' && e.key !== ' ') return;
    var el = e.target.closest('.js-dual-currency');
    if (!el) return;
    e.preventDefault();
    toggle(el);
  });
})();
