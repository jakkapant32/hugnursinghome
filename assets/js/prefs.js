(function () {
  var ZOOM_KEY = 'hug-zoom';
  var THEME_KEY = 'hug-theme';
  var ZOOM_STEPS = [0.85, 1, 1.15, 1.3];
  var DEFAULT_ZOOM = 1;

  function readZoom() {
    var v = parseFloat(localStorage.getItem(ZOOM_KEY) || '');
    return ZOOM_STEPS.indexOf(v) >= 0 ? v : DEFAULT_ZOOM;
  }

  function applyZoom(scale) {
    localStorage.setItem(ZOOM_KEY, String(scale));
    document.documentElement.dataset.zoom = String(scale);
    if (typeof document.body.style.zoom !== 'undefined') {
      document.body.style.zoom = String(scale);
    } else {
      document.documentElement.style.fontSize = 16 * scale + 'px';
    }
    var label = document.getElementById('pref-zoom-label');
    if (label) {
      label.textContent = Math.round(scale * 100) + '%';
    }
  }

  function readTheme() {
    var t = localStorage.getItem(THEME_KEY);
    return t === 'dark' ? 'dark' : 'light';
  }

  function applyTheme(theme) {
    localStorage.setItem(THEME_KEY, theme);
    document.documentElement.setAttribute('data-theme', theme);
    var btn = document.getElementById('pref-theme');
    if (btn) {
      btn.setAttribute('aria-pressed', theme === 'dark' ? 'true' : 'false');
      btn.title = theme === 'dark'
        ? btn.getAttribute('data-title-light') || ''
        : btn.getAttribute('data-title-dark') || '';
    }
  }

  function stepZoom(delta) {
    var cur = readZoom();
    var i = ZOOM_STEPS.indexOf(cur);
    if (i < 0) i = ZOOM_STEPS.indexOf(DEFAULT_ZOOM);
    i = Math.max(0, Math.min(ZOOM_STEPS.length - 1, i + delta));
    applyZoom(ZOOM_STEPS[i]);
  }

  document.addEventListener('DOMContentLoaded', function () {
    applyTheme(readTheme());
    var initialZoom = readZoom();
    applyZoom(initialZoom);
    var label = document.getElementById('pref-zoom-label');
    if (label) {
      label.textContent = Math.round(initialZoom * 100) + '%';
    }

    var themeBtn = document.getElementById('pref-theme');
    if (themeBtn) {
      themeBtn.addEventListener('click', function () {
        applyTheme(readTheme() === 'dark' ? 'light' : 'dark');
      });
    }
    var outBtn = document.getElementById('pref-zoom-out');
    var inBtn = document.getElementById('pref-zoom-in');
    var resetBtn = document.getElementById('pref-zoom-reset');
    if (outBtn) outBtn.addEventListener('click', function () { stepZoom(-1); });
    if (inBtn) inBtn.addEventListener('click', function () { stepZoom(1); });
    if (resetBtn) resetBtn.addEventListener('click', function () { applyZoom(DEFAULT_ZOOM); });
  });
})();
