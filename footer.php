    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

    <!-- Fether Icons  -->
    <script>
      feather.replace();
    </script>

<script>
      new DataTable('#example', {
    order: [[0, 'desc']]
});
  </script>


<script>
      new DataTable('#example1', {
    order: [[0, 'desc']]
});
  </script>


<script>
      new DataTable('#example2', {
    order: [[0, 'desc']]
});
  </script>


<script>
      new DataTable('#example3', {
    order: [[0, 'desc']]
});
  </script>

<script>
(function(){
  function getAlertType(el){
    if (el.classList.contains('alert-success')) return 'success';
    if (el.classList.contains('alert-danger')) return 'danger';
    if (el.classList.contains('alert-warning')) return 'warning';
    if (el.classList.contains('alert-info')) return 'info';
    if (el.classList.contains('alert-primary')) return 'primary';
    if (el.classList.contains('alert-secondary')) return 'secondary';
    return 'info';
  }

  function ensureContainer(){
    var container = document.querySelector('.app-toast-container');
    if (!container) {
      container = document.createElement('div');
      container.className = 'app-toast-container';
      document.body.appendChild(container);
    }
    return container;
  }

  function showToast(message, type){
    if (!message) return;
    var container = ensureContainer();
    var toast = document.createElement('div');
    toast.className = 'app-toast app-toast-' + type;

    var text = document.createElement('p');
    text.className = 'app-toast-message';
    text.textContent = message;

    var close = document.createElement('button');
    close.className = 'app-toast-close';
    close.setAttribute('type', 'button');
    close.setAttribute('aria-label', 'Close');
    close.textContent = 'x';
    close.addEventListener('click', function(){
      if (toast.parentNode) toast.parentNode.removeChild(toast);
    });

    toast.appendChild(text);
    toast.appendChild(close);
    container.appendChild(toast);

    window.setTimeout(function(){
      if (toast.parentNode) toast.parentNode.removeChild(toast);
    }, 4500);
  }

  var alerts = document.querySelectorAll('.alert[role="alert"]');
  alerts.forEach(function(alertEl){
    if (alertEl.closest('.modal') || alertEl.hasAttribute('data-no-toast')) return;
    var message = alertEl.textContent ? alertEl.textContent.trim() : '';
    if (!message) return;
    showToast(message, getAlertType(alertEl));
    alertEl.remove();
  });
})();
</script>

    


  </body>
</html>

