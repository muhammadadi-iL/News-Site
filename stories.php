<?php include 'header.php'; ?>
<div id="main-content">
  <div class="container page-shell">
    <div class="page-hero-band">
      <div>
        <p class="page-eyebrow"><i class="fa-solid fa-satellite-dish"></i> Realtime wires</p>
        <h1 class="page-heading">Live market stories</h1>
        <p class="section-sub">Detailed crypto and forex headlines from CoinTelegraph, CoinDesk, ForexLive and more — with summaries, timestamps, and source links.</p>
      </div>
      <div class="page-hero-actions">
        <a class="btn btn-soft" href="markets.php"><i class="fa-solid fa-chart-area"></i> Markets</a>
        <a class="btn btn-accent" href="search.php"><i class="fa-solid fa-magnifying-glass"></i> Search</a>
      </div>
    </div>

    <div class="live-feed-wrap" data-vue-live data-limit="14" data-layout="full"></div>
  </div>
</div>
<?php include 'footer.php'; ?>
