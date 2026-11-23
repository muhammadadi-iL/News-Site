<?php $brand = function_exists('site_brand') ? site_brand() : ['name' => 'Pulse Markets', 'tagline' => '']; ?>
<footer id="footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-brand">
                <img src="images/logo.svg" alt="<?php echo e($brand['name']); ?>">
                <div>
                    <strong><?php echo e(strtoupper($brand['name'])); ?></strong>
                    <p><?php echo e($brand['tagline']); ?>. Editorial CMS for posts, categories, and users — plus live market APIs.</p>
                </div>
            </div>
            <div class="footer-col">
                <h4>Explore</h4>
                <a href="<?php echo $hostname; ?>">Home</a>
                <a href="markets.php">Live Markets</a>
                <a href="search.php">Search</a>
                <a href="admin/">Admin</a>
            </div>
            <div class="footer-col">
                <h4>Project</h4>
                <a href="https://github.com/muhammadadi-iL/News-Site" target="_blank" rel="noopener">GitHub Repo</a>
                <a href="https://github.com/muhammadadi-iL" target="_blank" rel="noopener">@muhammadadi-iL</a>
            </div>
        </div>
        <div class="footer-bottom">
            <span><i class="fa-regular fa-copyright"></i> <?php echo date('Y'); ?> <?php echo e($brand['name']); ?></span>
            <span>Built by <a href="https://github.com/muhammadadi-iL" target="_blank" rel="noopener">Muhammad Adil</a></span>
        </div>
    </div>
</footer>
<script>
(function () {
  var toggle = document.getElementById('navToggle');
  var menu = document.getElementById('siteMenu');
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
<script src="https://unpkg.com/vue@3.5.13/dist/vue.global.prod.js"></script>
<script src="js/news-app.js"></script>
</body>
</html>
