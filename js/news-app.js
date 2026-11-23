/**
 * Vue 3 — Pulse Markets: live prices, forex, charts, news, autocomplete
 */
(function () {
  const { createApp, ref, computed, watch, onMounted, onBeforeUnmount, nextTick } = Vue;

  function apiBase() {
    const meta = document.querySelector('meta[name="news-api-base"]');
    if (meta && meta.content) return meta.content.replace(/\/$/, '');
    const parts = window.location.pathname.split('/');
    if (parts.length >= 2 && parts[1]) return '/' + parts[1] + '/api';
    return '/api';
  }

  function money(n, digits) {
    if (n == null || Number.isNaN(Number(n))) return '—';
    const x = Number(n);
    const d = digits != null ? digits : (x >= 1000 ? 2 : x >= 1 ? 4 : 6);
    return x.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: d });
  }

  function pct(n) {
    if (n == null || Number.isNaN(Number(n))) return '';
    const x = Number(n);
    const sign = x > 0 ? '+' : '';
    return sign + x.toFixed(2) + '%';
  }

  const SearchAutocomplete = {
    props: {
      initialQuery: { type: String, default: '' },
      action: { type: String, default: 'search.php' },
    },
    setup(props) {
      const query = ref(props.initialQuery || '');
      const suggestions = ref([]);
      const open = ref(false);
      const loading = ref(false);
      const activeIndex = ref(-1);
      let timer = null;
      let abortCtrl = null;
      const hasSuggestions = computed(() => open.value && suggestions.value.length > 0);

      async function fetchSuggestions(q) {
        if (!q || q.trim().length < 1) {
          suggestions.value = [];
          return;
        }
        loading.value = true;
        if (abortCtrl) abortCtrl.abort();
        abortCtrl = new AbortController();
        try {
          const url = apiBase() + '/search-suggest.php?q=' + encodeURIComponent(q.trim()) + '&live=1&limit=8';
          const res = await fetch(url, { signal: abortCtrl.signal });
          const data = await res.json();
          suggestions.value = Array.isArray(data.suggestions) ? data.suggestions : [];
          open.value = true;
          activeIndex.value = -1;
        } catch (e) {
          if (e.name !== 'AbortError') suggestions.value = [];
        } finally {
          loading.value = false;
        }
      }

      function onInput() {
        clearTimeout(timer);
        timer = setTimeout(() => fetchSuggestions(query.value), 220);
      }

      function choose(item) {
        query.value = item.label;
        open.value = false;
        if (item.type === 'live' && item.url) {
          window.open(item.url, '_blank', 'noopener');
          return;
        }
        if (item.url) {
          window.location.href = item.url;
          return;
        }
        window.location.href = props.action + '?search=' + encodeURIComponent(item.label);
      }

      function onKeydown(e) {
        if (!hasSuggestions.value) return;
        if (e.key === 'ArrowDown') {
          e.preventDefault();
          activeIndex.value = Math.min(activeIndex.value + 1, suggestions.value.length - 1);
        } else if (e.key === 'ArrowUp') {
          e.preventDefault();
          activeIndex.value = Math.max(activeIndex.value - 1, 0);
        } else if (e.key === 'Enter' && activeIndex.value >= 0) {
          e.preventDefault();
          choose(suggestions.value[activeIndex.value]);
        } else if (e.key === 'Escape') open.value = false;
      }

      onBeforeUnmount(() => {
        clearTimeout(timer);
        if (abortCtrl) abortCtrl.abort();
      });

      return {
        query, suggestions, open, loading, activeIndex, hasSuggestions, onInput, choose, onKeydown,
        onBlur() { setTimeout(() => { open.value = false; }, 180); },
        onFocus() { if (suggestions.value.length) open.value = true; },
      };
    },
    template: `
      <div class="vue-search" @keydown="onKeydown">
        <form class="search-post" :action="action" method="GET" autocomplete="off">
          <div class="search-row">
            <div class="vue-search-field">
              <input class="form-control" type="text" name="search" v-model="query"
                @input="onInput" @focus="onFocus" @blur="onBlur"
                placeholder="Search crypto, forex, live headlines..."
                aria-autocomplete="list" :aria-expanded="hasSuggestions ? 'true' : 'false'" role="combobox" />
              <div v-if="loading" class="vue-search-spinner"><i class="fa-solid fa-circle-notch fa-spin"></i></div>
              <ul v-show="hasSuggestions" class="vue-suggest-list" role="listbox">
                <li v-for="(item, idx) in suggestions" :key="(item.type||'x')+'-'+idx+'-'+item.label"
                  :class="{ active: idx === activeIndex }" role="option" @mousedown.prevent="choose(item)">
                  <span class="suggest-label">{{ item.label }}</span>
                  <span class="suggest-meta">
                    <span :class="['suggest-badge', item.type]">{{ item.type === 'live' ? 'Live' : 'Site' }}</span>
                    {{ item.category }}
                  </span>
                </li>
              </ul>
            </div>
            <button type="submit" class="btn btn-accent"><i class="fa-solid fa-magnifying-glass"></i><span>Go</span></button>
          </div>
        </form>
      </div>
    `,
  };

  const MarketTicker = {
    setup() {
      const coins = ref([]);
      const pairs = ref([]);
      const loading = ref(true);
      let timer = null;

      async function load() {
        try {
          const [cRes, fRes] = await Promise.all([
            fetch(apiBase() + '/crypto.php'),
            fetch(apiBase() + '/forex.php'),
          ]);
          const c = await cRes.json();
          const f = await fRes.json();
          coins.value = (c.items || []).slice(0, 6);
          pairs.value = (f.items || []).slice(0, 5);
        } catch (_) {
          /* keep last */
        } finally {
          loading.value = false;
        }
      }

      onMounted(() => {
        load();
        timer = setInterval(load, 60 * 1000);
      });
      onBeforeUnmount(() => { if (timer) clearInterval(timer); });

      const tape = computed(() => {
        const rows = [];
        coins.value.forEach((c) => {
          rows.push({
            key: 'c-' + c.symbol,
            label: c.symbol + '/USD',
            price: '$' + money(c.price),
            change: c.change_24h,
          });
        });
        pairs.value.forEach((p) => {
          rows.push({
            key: 'f-' + p.pair,
            label: p.pair,
            price: money(p.price, p.pair.includes('JPY') ? 3 : 5),
            change: p.change_24h,
          });
        });
        return rows;
      });

      return { tape, loading, money, pct };
    },
    template: `
      <div class="market-ticker" v-if="tape.length || loading">
        <div class="ticker-label"><i class="fa-solid fa-bolt"></i> Live</div>
        <div class="ticker-track">
          <div class="ticker-seq" :class="{ 'is-loading': loading && !tape.length }">
            <template v-if="tape.length">
              <span class="ticker-item" v-for="(t, i) in tape.concat(tape)" :key="t.key + '-' + i">
                <strong>{{ t.label }}</strong>
                <em>{{ t.price }}</em>
                <b v-if="t.change != null" :class="t.change >= 0 ? 'up' : 'down'">{{ pct(t.change) }}</b>
              </span>
            </template>
            <span v-else class="ticker-item">Loading live markets…</span>
          </div>
        </div>
      </div>
    `,
  };

  const CryptoBoard = {
    props: { limit: { type: Number, default: 8 } },
    setup(props) {
      const items = ref([]);
      const loading = ref(true);
      const error = ref('');
      const fetchedAt = ref('');
      let timer = null;

      async function load(force) {
        if (!items.value.length) loading.value = true;
        error.value = '';
        try {
          const res = await fetch(apiBase() + '/crypto.php' + (force ? '?refresh=1' : ''));
          const data = await res.json();
          items.value = (data.items || []).slice(0, props.limit);
          fetchedAt.value = data.fetched_at || '';
          if (!items.value.length) error.value = 'Crypto prices unavailable right now.';
        } catch (_) {
          if (!items.value.length) error.value = 'Could not load crypto prices.';
        } finally {
          loading.value = false;
        }
      }

      onMounted(() => {
        load(false);
        timer = setInterval(() => load(false), 60 * 1000);
      });
      onBeforeUnmount(() => { if (timer) clearInterval(timer); });

      return { items, loading, error, fetchedAt, load, money, pct };
    },
    template: `
      <section class="market-board crypto-board">
        <div class="board-head">
          <h2><i class="fa-solid fa-coins"></i> Crypto prices</h2>
          <button type="button" class="btn btn-ghost btn-sm" @click="load(true)" :disabled="loading">
            <i class="fa-solid fa-arrows-rotate" :class="{ 'fa-spin': loading }"></i> Refresh
          </button>
        </div>
        <p class="board-sub" v-if="fetchedAt">CoinGecko · updated {{ new Date(fetchedAt).toLocaleString() }}</p>
        <p v-if="loading && !items.length" class="board-status">Fetching live crypto…</p>
        <p v-if="error && !items.length" class="board-status error">{{ error }}</p>
        <div class="board-table" v-if="items.length">
          <div class="board-row head"><span>Asset</span><span>Price</span><span>24h</span></div>
          <div class="board-row" v-for="c in items" :key="c.id">
            <span class="asset"><img v-if="c.image" :src="c.image" alt="" /><strong>{{ c.symbol }}</strong> <small>{{ c.name }}</small></span>
            <span class="price">\${{ money(c.price) }}</span>
            <span class="chg" :class="c.change_24h >= 0 ? 'up' : 'down'">{{ pct(c.change_24h) }}</span>
          </div>
        </div>
      </section>
    `,
  };

  const ForexBoard = {
    props: { limit: { type: Number, default: 8 } },
    setup(props) {
      const items = ref([]);
      const loading = ref(true);
      const error = ref('');
      const fetchedAt = ref('');
      let timer = null;

      async function load(force) {
        if (!items.value.length) loading.value = true;
        error.value = '';
        try {
          const res = await fetch(apiBase() + '/forex.php' + (force ? '?refresh=1' : ''));
          const data = await res.json();
          items.value = (data.items || []).slice(0, props.limit);
          fetchedAt.value = data.fetched_at || '';
          if (!items.value.length) error.value = 'Forex rates unavailable right now.';
        } catch (_) {
          if (!items.value.length) error.value = 'Could not load forex rates.';
        } finally {
          loading.value = false;
        }
      }

      function digits(pair) {
        if (!pair) return 5;
        if (pair.includes('JPY')) return 3;
        if (pair.includes('PKR')) return 2;
        return 5;
      }

      onMounted(() => {
        load(false);
        timer = setInterval(() => load(false), 60 * 1000);
      });
      onBeforeUnmount(() => { if (timer) clearInterval(timer); });

      return { items, loading, error, fetchedAt, load, money, digits };
    },
    template: `
      <section class="market-board forex-board">
        <div class="board-head">
          <h2><i class="fa-solid fa-chart-line"></i> Forex rates</h2>
          <button type="button" class="btn btn-ghost btn-sm" @click="load(true)" :disabled="loading">
            <i class="fa-solid fa-arrows-rotate" :class="{ 'fa-spin': loading }"></i> Refresh
          </button>
        </div>
        <p class="board-sub" v-if="fetchedAt">Frankfurter · updated {{ new Date(fetchedAt).toLocaleString() }}</p>
        <p v-if="loading && !items.length" class="board-status">Fetching live FX…</p>
        <p v-if="error && !items.length" class="board-status error">{{ error }}</p>
        <div class="board-table" v-if="items.length">
          <div class="board-row head"><span>Pair</span><span>Rate</span><span></span></div>
          <div class="board-row" v-for="p in items" :key="p.pair">
            <span class="asset"><strong>{{ p.pair }}</strong></span>
            <span class="price">{{ money(p.price, digits(p.pair)) }}</span>
            <span class="chg muted">Spot</span>
          </div>
        </div>
      </section>
    `,
  };

  const CRYPTO_OPTIONS = [
    { id: 'bitcoin', symbol: 'BTC' },
    { id: 'ethereum', symbol: 'ETH' },
    { id: 'solana', symbol: 'SOL' },
    { id: 'ripple', symbol: 'XRP' },
    { id: 'binancecoin', symbol: 'BNB' },
    { id: 'cardano', symbol: 'ADA' },
    { id: 'dogecoin', symbol: 'DOGE' },
    { id: 'polkadot', symbol: 'DOT' },
  ];

  const FOREX_OPTIONS = [
    { base: 'EUR', quote: 'USD', label: 'EUR/USD' },
    { base: 'GBP', quote: 'USD', label: 'GBP/USD' },
    { base: 'USD', quote: 'JPY', label: 'USD/JPY' },
    { base: 'USD', quote: 'CHF', label: 'USD/CHF' },
    { base: 'AUD', quote: 'USD', label: 'AUD/USD' },
    { base: 'USD', quote: 'CAD', label: 'USD/CAD' },
    { base: 'EUR', quote: 'GBP', label: 'EUR/GBP' },
    { base: 'NZD', quote: 'USD', label: 'NZD/USD' },
  ];

  function buildLineChart(canvas, labels, values, color) {
    if (typeof Chart === 'undefined' || !canvas) return null;
    const up = values.length > 1 && values[values.length - 1] >= values[0];
    const stroke = color || (up ? '#059669' : '#dc2626');
    return new Chart(canvas.getContext('2d'), {
      type: 'line',
      data: {
        labels,
        datasets: [{
          data: values,
          borderColor: stroke,
          backgroundColor: stroke + '22',
          borderWidth: 2,
          fill: true,
          tension: 0.25,
          pointRadius: 0,
          pointHoverRadius: 4,
        }],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              label(ctx) {
                const v = ctx.parsed.y;
                return v == null ? '' : Number(v).toLocaleString(undefined, { maximumFractionDigits: 6 });
              },
            },
          },
        },
        scales: {
          x: {
            grid: { display: false },
            ticks: { maxTicksLimit: 6, color: '#5b6e6c', font: { size: 11 } },
          },
          y: {
            grid: { color: 'rgba(15, 61, 58, 0.08)' },
            ticks: {
              color: '#5b6e6c',
              font: { size: 11 },
              callback(v) {
                return Number(v).toLocaleString(undefined, { maximumFractionDigits: 4 });
              },
            },
          },
        },
      },
    });
  }

  function formatChartLabel(ts, days) {
    const d = new Date(ts);
    if (days <= 1) {
      return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }
    if (days <= 7) {
      return d.toLocaleString([], { month: 'short', day: 'numeric', hour: '2-digit' });
    }
    return d.toLocaleDateString([], { month: 'short', day: 'numeric' });
  }

  const CryptoChart = {
    setup() {
      const coinId = ref('bitcoin');
      const days = ref(7);
      const loading = ref(true);
      const error = ref('');
      const meta = ref({ symbol: 'BTC', last: null, change: null });
      const canvas = ref(null);
      let chart = null;
      let abortCtrl = null;

      async function load() {
        loading.value = true;
        error.value = '';
        if (abortCtrl) abortCtrl.abort();
        abortCtrl = new AbortController();
        try {
          const url = apiBase() + '/chart-crypto.php?id=' + encodeURIComponent(coinId.value) + '&days=' + days.value;
          const res = await fetch(url, { signal: abortCtrl.signal });
          const data = await res.json();
          const points = data.points || [];
          if (!points.length) throw new Error('empty');
          const values = points.map((p) => p.v);
          const labels = points.map((p) => formatChartLabel(p.t, days.value));
          const first = values[0];
          const last = values[values.length - 1];
          meta.value = {
            symbol: data.symbol || '—',
            last,
            change: first ? ((last - first) / first) * 100 : null,
          };
          await nextTick();
          if (chart) { chart.destroy(); chart = null; }
          chart = buildLineChart(canvas.value, labels, values);
        } catch (e) {
          if (e.name !== 'AbortError') error.value = 'Crypto chart unavailable right now.';
        } finally {
          loading.value = false;
        }
      }

      watch([coinId, days], load);
      onMounted(load);
      onBeforeUnmount(() => {
        if (abortCtrl) abortCtrl.abort();
        if (chart) chart.destroy();
      });

      return { coinId, days, loading, error, meta, canvas, CRYPTO_OPTIONS, money, pct, load };
    },
    template: `
      <section class="chart-card">
        <div class="chart-head">
          <div>
            <h2><i class="fa-solid fa-chart-line"></i> Crypto chart</h2>
            <p class="board-sub" v-if="meta.last != null">
              {{ meta.symbol }}/USD · \${{ money(meta.last) }}
              <span v-if="meta.change != null" :class="meta.change >= 0 ? 'up' : 'down'">{{ pct(meta.change) }}</span>
              over selected range
            </p>
          </div>
          <div class="chart-controls">
            <select v-model="coinId" class="form-control chart-select" aria-label="Crypto asset">
              <option v-for="c in CRYPTO_OPTIONS" :key="c.id" :value="c.id">{{ c.symbol }}</option>
            </select>
            <div class="range-pills">
              <button type="button" :class="{ active: days === 1 }" @click="days = 1">1D</button>
              <button type="button" :class="{ active: days === 7 }" @click="days = 7">7D</button>
              <button type="button" :class="{ active: days === 30 }" @click="days = 30">30D</button>
              <button type="button" :class="{ active: days === 90 }" @click="days = 90">90D</button>
            </div>
          </div>
        </div>
        <p v-if="loading" class="board-status">Loading chart…</p>
        <p v-if="error" class="board-status error">{{ error }}</p>
        <div class="chart-canvas-wrap">
          <canvas ref="canvas"></canvas>
        </div>
      </section>
    `,
  };

  const ForexChart = {
    setup() {
      const pairKey = ref('EUR/USD');
      const days = ref(30);
      const loading = ref(true);
      const error = ref('');
      const meta = ref({ pair: 'EUR/USD', last: null, change: null });
      const canvas = ref(null);
      let chart = null;
      let abortCtrl = null;

      const selected = computed(() => FOREX_OPTIONS.find((p) => p.label === pairKey.value) || FOREX_OPTIONS[0]);

      async function load() {
        loading.value = true;
        error.value = '';
        if (abortCtrl) abortCtrl.abort();
        abortCtrl = new AbortController();
        const p = selected.value;
        try {
          const url = apiBase() + '/chart-forex.php?base=' + encodeURIComponent(p.base)
            + '&quote=' + encodeURIComponent(p.quote) + '&days=' + days.value;
          const res = await fetch(url, { signal: abortCtrl.signal });
          const data = await res.json();
          const points = data.points || [];
          if (!points.length) throw new Error('empty');
          const values = points.map((pt) => pt.v);
          const labels = points.map((pt) => formatChartLabel(pt.t, days.value));
          const first = values[0];
          const last = values[values.length - 1];
          meta.value = {
            pair: data.pair || p.label,
            last,
            change: first ? ((last - first) / first) * 100 : null,
          };
          await nextTick();
          if (chart) { chart.destroy(); chart = null; }
          chart = buildLineChart(canvas.value, labels, values, '#0d9488');
        } catch (e) {
          if (e.name !== 'AbortError') error.value = 'Forex chart unavailable right now.';
        } finally {
          loading.value = false;
        }
      }

      watch([pairKey, days], load);
      onMounted(load);
      onBeforeUnmount(() => {
        if (abortCtrl) abortCtrl.abort();
        if (chart) chart.destroy();
      });

      function fxDigits(pair) {
        if (!pair) return 5;
        if (pair.includes('JPY')) return 3;
        return 5;
      }

      return { pairKey, days, loading, error, meta, canvas, FOREX_OPTIONS, money, pct, fxDigits, load };
    },
    template: `
      <section class="chart-card">
        <div class="chart-head">
          <div>
            <h2><i class="fa-solid fa-chart-area"></i> Forex chart</h2>
            <p class="board-sub" v-if="meta.last != null">
              {{ meta.pair }} · {{ money(meta.last, fxDigits(meta.pair)) }}
              <span v-if="meta.change != null" :class="meta.change >= 0 ? 'up' : 'down'">{{ pct(meta.change) }}</span>
              over selected range
            </p>
          </div>
          <div class="chart-controls">
            <select v-model="pairKey" class="form-control chart-select" aria-label="Forex pair">
              <option v-for="p in FOREX_OPTIONS" :key="p.label" :value="p.label">{{ p.label }}</option>
            </select>
            <div class="range-pills">
              <button type="button" :class="{ active: days === 7 }" @click="days = 7">7D</button>
              <button type="button" :class="{ active: days === 30 }" @click="days = 30">30D</button>
              <button type="button" :class="{ active: days === 90 }" @click="days = 90">90D</button>
            </div>
          </div>
        </div>
        <p v-if="loading" class="board-status">Loading chart…</p>
        <p v-if="error" class="board-status error">{{ error }}</p>
        <div class="chart-canvas-wrap">
          <canvas ref="canvas"></canvas>
        </div>
      </section>
    `,
  };

  function timeAgo(iso) {
    if (!iso) return '';
    const t = new Date(iso).getTime();
    if (Number.isNaN(t)) return '';
    const sec = Math.max(0, Math.floor((Date.now() - t) / 1000));
    if (sec < 60) return 'just now';
    if (sec < 3600) return Math.floor(sec / 60) + 'm ago';
    if (sec < 86400) return Math.floor(sec / 3600) + 'h ago';
    return Math.floor(sec / 86400) + 'd ago';
  }

  const LiveNewsFeed = {
    props: {
      limit: { type: Number, default: 8 },
      layout: { type: String, default: 'desk' },
    },
    setup(props) {
      const items = ref([]);
      const loading = ref(true);
      const error = ref('');
      const fetchedAt = ref('');
      const sources = ref([]);
      const active = ref(null);
      let refreshTimer = null;

      async function load(force) {
        const first = items.value.length === 0;
        if (first) loading.value = true;
        error.value = '';
        try {
          const qs = '?limit=' + props.limit + (force ? '&refresh=1' : '');
          const res = await fetch(apiBase() + '/live-news.php' + qs);
          const data = await res.json();
          if (!data.ok) throw new Error('Feed unavailable');
          items.value = data.items || [];
          fetchedAt.value = data.fetched_at || '';
          sources.value = Array.isArray(data.sources) ? data.sources : [];
          if (!items.value.length) error.value = 'No live market stories right now.';
          if (active.value) {
            const still = items.value.find((i) => i.link === active.value.link);
            active.value = still || items.value[0] || null;
          } else if (items.value.length) {
            active.value = items.value[0];
          }
        } catch (_) {
          if (!items.value.length) error.value = 'Could not load live stories right now.';
        } finally {
          loading.value = false;
        }
      }

      const sourceLabel = computed(() => sources.value.length ? sources.value.join(' · ') : 'Crypto · Forex wires');
      const featured = computed(() => items.value[0] || null);
      const rest = computed(() => items.value.slice(1));

      onMounted(() => {
        load(false);
        refreshTimer = setInterval(() => load(false), 3 * 60 * 1000);
      });
      onBeforeUnmount(() => { if (refreshTimer) clearInterval(refreshTimer); });

      return {
        items, loading, error, fetchedAt, sourceLabel, featured, rest, active, timeAgo,
        layout: props.layout,
        refresh() { load(true); },
        openDetail(item) { active.value = item; },
        formatTime(iso) {
          if (!iso) return '';
          try { return new Date(iso).toLocaleString(); } catch (_) { return iso; }
        },
      };
    },
    template: `
      <section class="live-stories" :class="'layout-' + layout">
        <div class="live-stories-head">
          <div>
            <h2><i class="fa-solid fa-satellite-dish"></i> Live market stories</h2>
            <p class="live-feed-sub" v-if="fetchedAt">Realtime wires · Updated {{ formatTime(fetchedAt) }} · {{ sourceLabel }}</p>
          </div>
          <div class="live-stories-actions">
            <a class="btn btn-soft btn-sm" href="stories.php"><i class="fa-solid fa-newspaper"></i> All stories</a>
            <button type="button" class="btn btn-ghost btn-sm" @click="refresh" :disabled="loading">
              <i class="fa-solid fa-arrows-rotate" :class="{ 'fa-spin': loading }"></i> Refresh
            </button>
          </div>
        </div>

        <p v-if="loading && !items.length" class="live-feed-status">Fetching realtime stories…</p>
        <p v-if="error && !items.length" class="live-feed-status error">{{ error }}</p>

        <div class="live-stories-grid" v-if="items.length">
          <article class="story-feature" v-if="featured" @click="openDetail(featured)">
            <div class="story-feature-media" v-if="featured.image">
              <img :src="featured.image" :alt="featured.title" loading="lazy" />
            </div>
            <div class="story-feature-body">
              <div class="story-meta-row">
                <span class="live-source">{{ featured.source }}</span>
                <span class="story-time">{{ timeAgo(featured.published) }}</span>
              </div>
              <h3>{{ featured.title }}</h3>
              <p v-if="featured.summary">{{ featured.summary }}</p>
              <div class="story-feature-cta">
                <button type="button" class="btn btn-accent btn-sm" @click.stop="openDetail(featured)">Read details</button>
                <a class="btn btn-soft btn-sm" :href="featured.link" target="_blank" rel="noopener" @click.stop>
                  Open source <i class="fa-solid fa-arrow-up-right-from-square"></i>
                </a>
              </div>
            </div>
          </article>

          <div class="story-rail">
            <article
              class="story-card"
              v-for="(item, idx) in rest"
              :key="idx"
              :class="{ active: active && active.link === item.link, 'no-thumb': !item.image }"
              @click="openDetail(item)"
            >
              <div class="story-card-thumb" v-if="item.image">
                <img :src="item.image" :alt="item.title" loading="lazy" />
              </div>
              <div class="story-card-body">
                <div class="story-meta-row">
                  <span class="live-source">{{ item.source }}</span>
                  <span class="story-time">{{ timeAgo(item.published) }}</span>
                </div>
                <h4>{{ item.title }}</h4>
                <p v-if="item.summary">{{ item.summary }}</p>
              </div>
            </article>
          </div>

          <aside class="story-detail" v-if="active">
            <p class="story-detail-kicker"><i class="fa-solid fa-book-open"></i> Story details</p>
            <div class="story-meta-row">
              <span class="live-source">{{ active.source }}</span>
              <span class="story-time">{{ timeAgo(active.published) }} · {{ formatTime(active.published) }}</span>
            </div>
            <h3>{{ active.title }}</h3>
            <div class="story-detail-media" v-if="active.image">
              <img :src="active.image" :alt="active.title" />
            </div>
            <p class="story-detail-copy">{{ active.summary || 'Full wire available on the source site.' }}</p>
            <a class="btn btn-accent" :href="active.link" target="_blank" rel="noopener">
              Continue on {{ active.source }} <i class="fa-solid fa-arrow-up-right-from-square"></i>
            </a>
          </aside>
        </div>
      </section>
    `,
  };

  function mountOnce(el, factory) {
    if (!el || el.__vue_mounted) return;
    el.__vue_mounted = true;
    factory(el);
  }

  function boot() {
    document.querySelectorAll('[data-vue-search]').forEach((el) => {
      mountOnce(el, (node) => {
        createApp(SearchAutocomplete, { initialQuery: node.getAttribute('data-initial') || '' }).mount(node);
      });
    });
    document.querySelectorAll('[data-vue-live]').forEach((el) => {
      mountOnce(el, (node) => {
        createApp(LiveNewsFeed, {
          limit: parseInt(node.getAttribute('data-limit') || '8', 10),
          layout: node.getAttribute('data-layout') || 'desk',
        }).mount(node);
      });
    });
    document.querySelectorAll('[data-vue-ticker]').forEach((el) => {
      mountOnce(el, (node) => createApp(MarketTicker).mount(node));
    });
    document.querySelectorAll('[data-vue-crypto]').forEach((el) => {
      mountOnce(el, (node) => {
        createApp(CryptoBoard, { limit: parseInt(node.getAttribute('data-limit') || '8', 10) }).mount(node);
      });
    });
    document.querySelectorAll('[data-vue-forex]').forEach((el) => {
      mountOnce(el, (node) => {
        createApp(ForexBoard, { limit: parseInt(node.getAttribute('data-limit') || '8', 10) }).mount(node);
      });
    });
    document.querySelectorAll('[data-vue-crypto-chart]').forEach((el) => {
      mountOnce(el, (node) => createApp(CryptoChart).mount(node));
    });
    document.querySelectorAll('[data-vue-forex-chart]').forEach((el) => {
      mountOnce(el, (node) => createApp(ForexChart).mount(node));
    });
  }

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', boot);
  else boot();
})();
