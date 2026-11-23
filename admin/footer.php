<div id="footer">
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <span>
                    <i class="fa-regular fa-copyright"></i> <?php echo date('Y'); ?> Pulse Markets Admin
                    &nbsp;|&nbsp;
                    <a href="https://github.com/muhammadadi-iL" target="_blank" rel="noopener">Muhammad Adil</a>
                </span>
            </div>
        </div>
    </div>
</div>
<script>
(function () {
  var toggle = document.getElementById('adminNavToggle');
  var menu = document.getElementById('adminMenu');
  if (!toggle || !menu) return;

  function setOpen(open) {
    menu.classList.toggle('is-open', open);
    toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    toggle.querySelector('i').className = open ? 'fa-solid fa-xmark' : 'fa-solid fa-bars';
  }

  toggle.addEventListener('click', function () {
    setOpen(!menu.classList.contains('is-open'));
  });

  menu.querySelectorAll('a').forEach(function (link) {
    link.addEventListener('click', function () {
      if (window.matchMedia('(max-width: 991px)').matches) {
        setOpen(false);
      }
    });
  });

  window.addEventListener('resize', function () {
    if (window.matchMedia('(min-width: 992px)').matches) {
      setOpen(false);
    }
  });
})();
</script>
</body>
</html>
