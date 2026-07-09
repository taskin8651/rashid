<script>
  (function () {
    var root = document.documentElement;
    var btn = document.getElementById('themeToggle');
    var icon = document.getElementById('themeIcon');

    function isDark() {
      var attr = root.getAttribute('data-theme');
      if (attr === 'dark') return true;
      if (attr === 'light') return false;
      return window.matchMedia('(prefers-color-scheme: dark)').matches;
    }

    function updateIcon() {
      if (icon) icon.className = isDark() ? 'bi bi-sun-fill' : 'bi bi-moon-stars-fill';
    }

    updateIcon();

    if (btn) {
      btn.addEventListener('click', function () {
        var next = isDark() ? 'light' : 'dark';
        root.setAttribute('data-theme', next);
        localStorage.setItem('rtech-theme', next);
        updateIcon();
      });
    }
  })();
</script>
