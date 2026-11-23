# Pulse Markets — Crypto & Forex News

PHP + MySQL news CMS focused on **cryptocurrency & forex**, with **realtime prices/rates**, live market headlines (Vue 3), and a full **admin panel**.

| | |
| --- | --- |
| GitHub | https://github.com/muhammadadi-iL/News-Site |
| Contributor | [@muhammadadi-iL](https://github.com/muhammadadi-iL) · `adilm0616@gmail.com` |
| Local URL | http://localhost/news-site/ |
| Markets | http://localhost/news-site/markets.php |
| Admin | http://localhost/news-site/admin/ |
| Stack | PHP 8 + Apache + MySQL + Vue 3 (CDN) |

**Admin login:** `admin` / `admin` (MD5 legacy) · editor: `editor` / `editor`

---

## What you get

- **Editorial CMS** — posts, categories (Crypto, Forex, Bitcoin, Ethereum, Analysis), users
- **Live crypto board** — CoinGecko (BTC, ETH, SOL, XRP, BNB, ADA, DOGE, DOT)
- **Live forex board** — Frankfurter G10-style pairs (EUR/USD, GBP/USD, USD/JPY, …)
- **Interactive charts** — Chart.js line charts (crypto 1D–90D, forex 7D–90D) on Home + Markets
- **Scrolling ticker** — prices across the top of every page
- **Live market news** — CoinTelegraph, CoinDesk, ForexLive RSS
- **Vue autocomplete search** — local posts + live headlines
- **Admin CRUD** — create/edit/delete posts, categories, users + image upload

---

## Local setup (macOS + Homebrew)

```bash
brew install php httpd mysql
brew services start mysql php httpd
cd /Users/mac/Projects/News-Site
mysql -u root -h 127.0.0.1 --protocol=TCP < database/news-cms.sql
```

Apache Alias (example):

```apache
Alias /news-site "/Users/mac/Projects/News-Site"
<Directory "/Users/mac/Projects/News-Site">
    Options Indexes FollowSymLinks
    AllowOverride All
    Require all granted
    DirectoryIndex index.php
</Directory>
```

`config.php` expects DB `news_site`, user `root`, empty password, hostname `http://localhost/news-site`.

---

## Realtime APIs

| Endpoint | Source |
| --- | --- |
| `api/crypto.php` | CoinGecko markets |
| `api/forex.php` | Frankfurter latest |
| `api/chart-crypto.php` | CoinGecko market chart |
| `api/chart-forex.php` | Frankfurter timeseries |
| `api/live-news.php` | Crypto/FX RSS |
| `api/search-suggest.php` | Local DB + live headlines |

Short file cache lives in `cache/` (~45–90s) so autocomplete stays fast.

```bash
curl -s "http://localhost/news-site/api/crypto.php" | head
curl -s "http://localhost/news-site/api/forex.php" | head
curl -s "http://localhost/news-site/api/live-news.php?limit=3"
```

PHP `allow_url_fopen` must be on.

---

## Project layout

```text
News-Site/
├── index.php              # hero + crypto/forex boards + live news + posts
├── markets.php            # full live markets desk
├── single.php / category.php / author.php / search.php
├── header.php / footer.php / sidebar.php
├── api/crypto.php / forex.php / live-news.php / search-suggest.php
├── includes/helpers.php / markets.php / live_feed.php
├── js/news-app.js         # Vue ticker, boards, feed, autocomplete
├── admin/                 # login + CRUD
├── database/news-cms.sql
└── cache/                 # gitignored JSON cache
```

---

## Git commits

```text
Muhammad Adil <adilm0616@gmail.com>
```

---

## License / credit

Learning / practice market-news CMS. Prices and third-party headlines are for demo use; always verify against official sources before trading decisions.
