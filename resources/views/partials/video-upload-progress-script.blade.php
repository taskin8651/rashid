<script>
  document.querySelectorAll('.js-video-upload-form').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      var fileInput = form.querySelector('input[type=file]');
      if (!fileInput || !fileInput.files.length) {
        return;
      }

      e.preventDefault();

      var wrap = form.querySelector('.video-progress');
      var bar = form.querySelector('.video-progress-bar');
      var label = form.querySelector('.video-progress-label');
      var submitBtn = form.querySelector('button[type=submit]');

      wrap.style.display = 'block';
      if (submitBtn) {
        submitBtn.disabled = true;
      }

      var xhr = new XMLHttpRequest();
      xhr.open(form.method, form.action, true);

      xhr.upload.addEventListener('progress', function (evt) {
        if (!evt.lengthComputable) {
          return;
        }
        var percent = Math.round((evt.loaded / evt.total) * 100);
        var remainingMB = ((evt.total - evt.loaded) / (1024 * 1024)).toFixed(1);
        var totalMB = (evt.total / (1024 * 1024)).toFixed(1);
        bar.style.width = percent + '%';
        bar.setAttribute('aria-valuenow', percent);
        label.textContent = percent + '% uploaded — ' + remainingMB + ' MB of ' + totalMB + ' MB remaining';
      });

      xhr.addEventListener('load', function () {
        if (xhr.status >= 200 && xhr.status < 400) {
          if (xhr.responseURL) {
            history.replaceState(null, '', xhr.responseURL);
          }
          document.open();
          document.write(xhr.responseText);
          document.close();
        } else {
          label.textContent = 'Upload failed. Please try again.';
          if (submitBtn) {
            submitBtn.disabled = false;
          }
        }
      });

      xhr.addEventListener('error', function () {
        label.textContent = 'Upload failed — check your connection and try again.';
        if (submitBtn) {
          submitBtn.disabled = false;
        }
      });

      xhr.send(new FormData(form));
    });
  });
</script>
