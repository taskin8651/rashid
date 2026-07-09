<script>
  (function () {
    var saved = localStorage.getItem('rtech-theme');
    if (saved === 'light' || saved === 'dark') {
      document.documentElement.setAttribute('data-theme', saved);
    }
  })();
</script>
