/**
 * Vue 3 — live headlines + search autocomplete for News Site
 */
(function () {
  const { createApp, ref, computed, onMounted, onBeforeUnmount } = Vue;

  function apiBase() {
    const meta = document.querySelector('meta[name="news-api-base"]');
    if (meta && meta.content) return meta.content.replace(/\/$/, '');
    const parts = window.location.pathname.split('/');
    if (parts.length >= 2 && parts[1]) {
      return '/' + parts[1] + '/api';
    }
    return '/api';
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
          if (e.name !== 'AbortError') {
            suggestions.value = [];
          }
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
        } else if (e.key === 'Escape') {
          open.value = false;
        }
      }

      function onBlur() {
        setTimeout(() => { open.value = false; }, 180);
      }

      onBeforeUnmount(() => {
        clearTimeout(timer);
        if (abortCtrl) abortCtrl.abort();
      });

      return {
        query,
        suggestions,
        open,
        loading,
        activeIndex,
        hasSuggestions,
        onInput,
        choose,
        onKeydown,
        onBlur,
        onFocus() {
          if (suggestions.value.length) open.value = true;
        },
      };
    },
    template: `
      <div class="vue-search" @keydown="onKeydown">
        <form class="search-post" :action="action" method="GET" autocomplete="off">
          <div class="search-row">
            <div class="vue-search-field">
              <input
                class="form-control"
                type="text"
                name="search"
                v-model="query"
                @input="onInput"
                @focus="onFocus"
                @blur="onBlur"
                placeholder="Search local + live headlines..."
                aria-autocomplete="list"
                :aria-expanded="hasSuggestions ? 'true' : 'false'"
                role="combobox"
              />
              <div v-if="loading" class="vue-search-spinner"><i class="fa-solid fa-circle-notch fa-spin"></i></div>
              <ul v-show="hasSuggestions" class="vue-suggest-list" role="listbox">
                <li
                  v-for="(item, idx) in suggestions"
                  :key="(item.type || 'x') + '-' + idx + '-' + item.label"
                  :class="{ active: idx === activeIndex }"
                  role="option"
                  @mousedown.prevent="choose(item)"
                >
                  <span class="suggest-label">{{ item.label }}</span>
                  <span class="suggest-meta">
                    <span :class="['suggest-badge', item.type]">{{ item.type === 'live' ? 'Live' : 'Site' }}</span>
                    {{ item.category }}
                  </span>
                </li>
              </ul>
            </div>
            <button type="submit" class="btn btn-accent">
              <i class="fa-solid fa-magnifying-glass"></i>
              <span>Go</span>
            </button>
          </div>
        </form>
      </div>
    `,
  };

  const LiveNewsFeed = {
    props: {
      limit: { type: Number, default: 8 },
    },
    setup(props) {
      const items = ref([]);
      const loading = ref(true);
      const error = ref('');
      const fetchedAt = ref('');
      const sources = ref([]);
      let refreshTimer = null;

      async function load(force) {
        const firstLoad = items.value.length === 0;
        if (firstLoad) loading.value = true;
        error.value = '';
        try {
          const qs = '?limit=' + props.limit + (force ? '&refresh=1' : '');
          const res = await fetch(apiBase() + '/live-news.php' + qs);
          const data = await res.json();
          if (!data.ok) throw new Error('Feed unavailable');
          items.value = data.items || [];
          fetchedAt.value = data.fetched_at || '';
          sources.value = Array.isArray(data.sources) ? data.sources : [];
          if (!items.value.length) {
            error.value = 'No live headlines available right now.';
          }
        } catch (e) {
          if (!items.value.length) {
            error.value = 'Could not load live headlines right now.';
          }
        } finally {
          loading.value = false;
        }
      }

      function formatTime(iso) {
        if (!iso) return '';
        try {
          return new Date(iso).toLocaleString();
        } catch (_) {
          return iso;
        }
      }

      const sourceLabel = computed(() => {
        if (!sources.value.length) return 'BBC · NPR · Guardian';
        return sources.value.join(' · ');
      });

      onMounted(() => {
        load(false);
        refreshTimer = setInterval(() => load(false), 3 * 60 * 1000);
      });

      onBeforeUnmount(() => {
        if (refreshTimer) clearInterval(refreshTimer);
      });

      return {
        items,
        loading,
        error,
        fetchedAt,
        sources,
        sourceLabel,
        load,
        formatTime,
        refresh() {
          load(true);
        },
      };
    },
    template: `
      <section class="live-feed-card">
        <div class="live-feed-head">
          <h2><i class="fa-solid fa-satellite-dish"></i> Live from the web</h2>
          <button type="button" class="btn btn-ghost btn-sm" @click="refresh" :disabled="loading">
            <i class="fa-solid fa-arrows-rotate" :class="{ 'fa-spin': loading }"></i> Refresh
          </button>
        </div>
        <p class="live-feed-sub" v-if="fetchedAt">Updated {{ formatTime(fetchedAt) }} · {{ sourceLabel }}</p>
        <p v-if="loading && !items.length" class="live-feed-status">Fetching realtime headlines...</p>
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

  function mountSearch(el) {
    if (!el || el.__vue_mounted) return;
    el.__vue_mounted = true;
    const initial = el.getAttribute('data-initial') || '';
    createApp(SearchAutocomplete, { initialQuery: initial }).mount(el);
  }

  function mountLive(el) {
    if (!el || el.__vue_mounted) return;
    el.__vue_mounted = true;
    const limit = parseInt(el.getAttribute('data-limit') || '8', 10);
    createApp(LiveNewsFeed, { limit }).mount(el);
  }

  function boot() {
    document.querySelectorAll('[data-vue-search]').forEach(mountSearch);
    document.querySelectorAll('[data-vue-live]').forEach(mountLive);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();
