# Pulse Markets — Crypto & Forex News

PHP + MySQL market-news CMS for **cryptocurrency & forex**: live prices, interactive charts, realtime wire stories (Vue 3), editorial posts, and a full **admin panel**.

| | |
| --- | --- |
| GitHub | https://github.com/muhammadadi-iL/News-Site |
| Contributor | [@muhammadadi-iL](https://github.com/muhammadadi-iL) · `adilm0616@gmail.com` |
| Home | http://localhost/news-site/ |
| Markets | http://localhost/news-site/markets.php |
| Stories | http://localhost/news-site/stories.php |
| Admin | http://localhost/news-site/admin/ |
| Stack | PHP 8 + Apache (`httpd`) + MySQL + Vue 3 + Chart.js (CDN) |

**Admin login:** `admin` / `admin` · editor: `editor` / `editor`  
(Passwords stored as legacy MD5 in the seed DB.)

---

## Features

### Front desk
- **Brand:** Pulse Markets — teal market-desk UI (Sora + IBM Plex Sans)
- **Live ticker** — crypto + FX tape across every page
- **Market snapshot** — CoinGecko crypto board + Frankfurter forex board
- **Charts** — Chart.js ranges (crypto `1D/7D/30D/90D`, forex `7D/30D/90D`)
- **Live market stories** — featured wire, story rail, detail panel, source link
- **Dedicated Stories page** — fuller realtime wire desk (`stories.php`)
- **Desk briefings** — editorial CMS posts with categories and pagination
- **Autocomplete search** — local posts + live headlines (Vue)
- **Responsive layout** — phone / tablet / desktop shells for home, markets, stories, single, category, search, author

### Categories (seed)
Crypto · Forex · Bitcoin · Ethereum · Analysis

### Admin
- Login / logout sessions
- Posts CRUD + image upload (`admin/upload/`)
- Categories CRUD (admin role)
- Users CRUD (admin role)
- View site shortcut back to the front desk

---

## Local setup (macOS + Homebrew)

```bash
brew install php httpd mysql
brew services start mysql php httpd

cd /Users/mac/Projects/News-Site
mysql -u root -h 127.0.0.1 --protocol=TCP < database/news-cms.sql
```

Apache Alias example:

```apache
Alias /news-site "/Users/mac/Projects/News-Site"
<Directory "/Users/mac/Projects/News-Site">
    Options Indexes FollowSymLinks
    AllowOverride All
    Require all granted
    DirectoryIndex index.php
</Directory>
```

Then:

```bash
brew services restart httpd
open http://localhost/news-site/
```

### Config

`config.php` / `admin/config.php`:

| Setting | Value |
| --- | --- |
| `$hostname` | `http://localhost/news-site` |
| DB host | `localhost` |
| DB user | `root` |
| DB password | *(empty)* |
| Database | `news_site` |

PHP **`allow_url_fopen`** must be **On** (live prices, charts, RSS).

---

## Pages

| URL | Purpose |
| --- | --- |
| `/` | Hero + snapshot + live stories + charts + editorial briefings |
| `/markets.php` | Charts, price boards, live stories |
| `/stories.php` | Realtime wire desk with detail panel |
| `/single.php?id=` | Full briefing + related notes + compact live stories |
| `/category.php?cid=` | Posts by desk category |
| `/author.php?aid=` | Posts by author |
| `/search.php?search=` | Local archive search |
| `/admin/` | CMS login + CRUD |

---

## Realtime APIs

| Endpoint | Data |
| --- | --- |
| `api/crypto.php` | Live crypto markets (CoinGecko) |
| `api/forex.php` | Live FX spots (Frankfurter) |
| `api/chart-crypto.php?id=bitcoin&days=7` | Crypto history chart |
| `api/chart-forex.php?base=EUR&quote=USD&days=30` | FX history chart |
| `api/live-news.php?limit=8` | Crypto/FX RSS stories |
| `api/search-suggest.php?q=btc&live=1` | Autocomplete (local + live) |

Wire sources: **CoinTelegraph**, **CoinDesk**, **ForexLive** (with fallbacks).  
Short JSON cache in `cache/` (~45–180s) keeps autocomplete and boards fast.

Quick checks:

```bash
curl -s "http://localhost/news-site/api/crypto.php" | head
curl -s "http://localhost/news-site/api/forex.php" | head
curl -s "http://localhost/news-site/api/chart-crypto.php?id=bitcoin&days=7" | head
curl -s "http://localhost/news-site/api/live-news.php?limit=3"
curl -s "http://localhost/news-site/api/search-suggest.php?q=bitcoin&live=1"
```

---

## Project layout

```text
News-Site/
├── index.php                 # home desk
├── markets.php               # live markets + charts
├── stories.php               # realtime story desk
├── single.php                # post detail
├── category.php / author.php / search.php
├── header.php / footer.php / sidebar.php
├── config.php
├── api/
│   ├── crypto.php
│   ├── forex.php
│   ├── chart-crypto.php
│   ├── chart-forex.php
│   ├── live-news.php
│   └── search-suggest.php
├── includes/
│   ├── helpers.php
│   ├── markets.php           # CoinGecko + Frankfurter + chart helpers
│   └── live_feed.php         # RSS/Atom fetch + cache
├── js/news-app.js            # Vue ticker, boards, charts, stories, search
├── css/style.css
├── images/                   # logo.svg, favicons, post art
├── admin/                    # login + posts/categories/users CRUD
├── database/news-cms.sql     # schema + Pulse Markets seed
├── cache/                    # gitignored API cache
├── CONTRIBUTORS.md
└── README.md
```

---

## Schema note (legacy field names)

`posts.post_content` stores the **category id** (not HTML body).  
Story/body text lives in `posts.post_keywords`.

```sql
LEFT JOIN category ON posts.post_content = category.id
LEFT JOIN users ON posts.post_author = users.id
```

---

## Git commits (contribution graph)

Use this author so GitHub counts activity for **muhammadadi-iL**:

```text
Muhammad Adil <adilm0616@gmail.com>
```

Email must stay **verified** on GitHub:  
https://github.com/muhammadadi-iL/News-Site/graphs/contributors

---

## Disclaimer

Learning / demo market-news project. Live prices and third-party headlines are for practice only — verify against official sources before any trading decision.
