<?php include 'header.php'; ?>
<div id="main-content">
  <div class="container page-shell markets-page">
    <div class="page-hero-band">
      <div>
        <p class="page-eyebrow"><i class="fa-solid fa-chart-area"></i> Trading desk</p>
        <h1 class="page-heading">Live Markets</h1>
        <p class="section-sub">Realtime crypto prices, forex spots, interactive charts, and wire stories in one board.</p>
      </div>
      <div class="page-hero-actions">
        <a class="btn btn-soft" href="stories.php"><i class="fa-solid fa-newspaper"></i> Stories</a>
        <a class="btn btn-accent" href="admin/"><i class="fa-solid fa-pen-to-square"></i> Publish</a>
      </div>
    </div>

    <section class="desk-block">
      <div class="section-head">
        <h2 class="page-heading page-heading-sm">Charts</h2>
        <p class="section-sub">Pick an asset or pair and switch ranges.</p>
      </div>
      <div class="markets-split markets-split-lg charts-split">
        <div data-vue-crypto-chart></div>
        <div data-vue-forex-chart></div>
      </div>
    </section>

    <section class="desk-block">
      <div class="section-head">
        <h2 class="page-heading page-heading-sm">Price boards</h2>
        <p class="section-sub">CoinGecko crypto + Frankfurter FX, auto-refresh every minute.</p>
      </div>
      <div class="markets-split markets-split-lg">
        <div data-vue-crypto data-limit="8"></div>
        <div data-vue-forex data-limit="8"></div>
      </div>
    </section>

    <section class="desk-block">
      <div class="live-feed-wrap" data-vue-live data-limit="10" data-layout="markets"></div>
    </section>
  </div>
</div>
<?php include 'footer.php'; ?>
