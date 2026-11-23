<?php include 'header.php'; ?>
<div id="main-content">
  <div class="container markets-page">
    <div class="section-head markets-page-head">
      <h1 class="page-heading"><i class="fa-solid fa-chart-area"></i> Live Markets</h1>
      <p class="section-sub">Realtime crypto prices (CoinGecko) and forex spot rates (Frankfurter), refreshed on the desk every minute.</p>
    </div>

    <div class="markets-split markets-split-lg charts-split">
      <div data-vue-crypto-chart></div>
      <div data-vue-forex-chart></div>
    </div>

    <div class="markets-split markets-split-lg">
      <div data-vue-crypto data-limit="8"></div>
      <div data-vue-forex data-limit="8"></div>
    </div>

    <div class="live-feed-wrap" data-vue-live data-limit="10"></div>
  </div>
</div>
<?php include 'footer.php'; ?>
