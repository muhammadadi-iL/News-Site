/**
 * Vue 3 — Pulse Markets: live prices, forex, news, autocomplete
 */
(function () {
  const { createApp, ref, computed, onMounted, onBeforeUnmount } = Vue;

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

  const LiveNewsFeed = {
    props: { limit: { type: Number, default: 8 } },
    setup(props) {
      const items = ref([]);
      const loading = ref(true);
      const error = ref('');
      const fetchedAt = ref('');
      const sources = ref([]);
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
          if (!items.value.length) error.value = 'No live market headlines right now.';
        } catch (_) {
          if (!items.value.length) error.value = 'Could not load live headlines right now.';
        } finally {
          loading.value = false;
        }
      }

      const sourceLabel = computed(() => sources.value.length ? sources.value.join(' · ') : 'Crypto · Forex wires');

      onMounted(() => {
        load(false);
        refreshTimer = setInterval(() => load(false), 3 * 60 * 1000);
      });
      onBeforeUnmount(() => { if (refreshTimer) clearInterval(refreshTimer); });

      return {
        items, loading, error, fetchedAt, sourceLabel,
        refresh() { load(true); },
        formatTime(iso) {
          if (!iso) return '';
          try { return new Date(iso).toLocaleString(); } catch (_) { return iso; }
        },
      };
    },
    template: `
      <section class="live-feed-card">
        <div class="live-feed-head">
          <h2><i class="fa-solid fa-satellite-dish"></i> Live market news</h2>
          <button type="button" class="btn btn-ghost btn-sm" @click="refresh" :disabled="loading">
            <i class="fa-solid fa-arrows-rotate" :class="{ 'fa-spin': loading }"></i> Refresh
          </button>
        </div>
        <p class="live-feed-sub" v-if="fetchedAt">Updated {{ formatTime(fetchedAt) }} · {{ sourceLabel }}</p>
        <p v-if="loading && !items.length" class="live-feed-status">Fetching realtime headlines…</p>
        <p v-if="error && !items.length" class="live-feed-status error">{{ error }}</p>
        <ul class="live-feed-list" v-if="items.length">
          <li v-for="(item, idx) in items" :key="idx">
            <a :href="item.link" target="_blank" rel="noopener">
              <span class="live-source">{{ item.source }}</span>
              <strong>{{ item.title }}</strong>
              <em v-if="item.summary">{{ item.summary }}</em>
            </a>
          </li>
        </ul>
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
        createApp(LiveNewsFeed, { limit: parseInt(node.getAttribute('data-limit') || '8', 10) }).mount(node);
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
  }

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', boot);
  else boot();
})();
