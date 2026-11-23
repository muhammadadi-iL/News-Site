# News Site (PHP + MySQL CMS)

Practice news website in **native PHP + MySQL** (Bootstrap front end, admin panel for posts / categories / users).

| | |
| --- | --- |
| GitHub | https://github.com/muhammadadi-iL/News-Site |
| Contributor | [@muhammadadi-iL](https://github.com/muhammadadi-iL) · `adilm0616@gmail.com` |
| Local URL | http://localhost/news-site/ |
| Admin URL | http://localhost/news-site/admin/ |
| Stack | Homebrew PHP 8.x + Apache (`httpd`) + MySQL + **Vue 3** (CDN) |
| Live APIs | `api/live-news.php` · `api/search-suggest.php` (BBC / NPR / Guardian RSS) |

---

## Local setup — start here (macOS + Homebrew)

Yeh project isi machine pe **dusre PHP sites** ki tarah Apache Alias se chalta hai: `http://localhost/news-site/`.

### Step 1 — Homebrew (agar nahi hai)

```bash
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
```

### Step 2 — PHP, Apache, MySQL install

```bash
brew install php httpd mysql
php -v
mysql --version
httpd -v
```

Expect PHP **8.x**. Paths aksar:

- Intel Mac: `/usr/local/opt/php/...`, `/usr/local/etc/httpd/`
- Apple Silicon: `/opt/homebrew/opt/php/...`, `/opt/homebrew/etc/httpd/`

### Step 3 — Services start karo

```bash
brew services start mysql
brew services start php
brew services start httpd
brew services list
```

`mysql`, `php`, aur `httpd` ke status **started** hone chahiye.

### Step 4 — Repo clone / open

```bash
git clone https://github.com/muhammadadi-iL/News-Site.git
cd News-Site
```

Is machine pe path:

```text
/Users/mac/Projects/News-Site
```

### Step 5 — Database import

`config.php` yeh expect karta hai:

| Setting | Value |
| --- | --- |
| Host | `localhost` |
| User | `root` |
| Password | *(empty)* |
| Database | `news_site` |

Import:

```bash
mysql -u root < database/news-cms.sql
```

Verify:

```bash
mysql -u root -e "USE news_site; SHOW TABLES; SELECT id, username, role FROM users;"
```

Tables: `users`, `category`, `posts`.

**Default admin login**

| Field | Value |
| --- | --- |
| Username | `admin` |
| Password | `admin` |

Password DB mein **MD5** store hoti hai (legacy app). Extra sample user: `editor` / `editor`.

Agar MySQL root pe password laga ho, `config.php` aur `admin/config.php` dono mein `$password` update karo.

### Step 6 — Apache pe `/news-site` Alias

Homebrew Apache config (Intel example):

`/usr/local/etc/httpd/httpd.conf`

Is block ko `ScriptAlias /cgi-bin/...` se **pehle** add karo:

```apache
Alias /news-site "/Users/mac/Projects/News-Site"
<Directory "/Users/mac/Projects/News-Site">
    Options Indexes FollowSymLinks
    AllowOverride All
    Require all granted
    DirectoryIndex index.php
</Directory>
```

PHP module loaded hona chahiye (`LoadModule php_module ...` + `AddType application/x-httpd-php .php`).

Optional vhost (dusri sites jaisa), `extra/httpd-vhosts.conf` mein:

```apache
<VirtualHost *:80>
    ServerName news-site.localhost
    DocumentRoot "/Users/mac/Projects/News-Site"
    <Directory "/Users/mac/Projects/News-Site">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
        DirectoryIndex index.php
    </Directory>
</VirtualHost>
```

Phir:

```bash
brew services restart httpd
```

### Step 7 — Open in browser

- Front: http://localhost/news-site/
- Admin: http://localhost/news-site/admin/
- phpMyAdmin (agar install hai): http://localhost/phpmyadmin/ → DB `news_site`

Quick check:

```bash
curl -sI http://localhost/news-site/ | head -5
curl -s  http://localhost/news-site/ | grep -o '<title>[^<]*</title>'
```

### Setup summary (short)

```text
1. brew install php httpd mysql
2. brew services start mysql php httpd
3. git clone … && cd News-Site
4. mysql -u root < database/news-cms.sql
5. Apache Alias /news-site → project folder
6. brew services restart httpd
7. open http://localhost/news-site/
8. admin / admin  →  /admin/
```

---

## Minor details (useful when something breaks)

### `$hostname` redirects

`config.php` / `admin/config.php` mein:

```php
$hostname = "http://localhost/news-site";
```

Admin login / save ke baad redirects isi URL pe jate hain. Agar Alias path badlo (e.g. `/News-Site`), yeh string bhi match karo — trailing slash usually mat lagao; app khud `/admin/...` jodti hai.

### Schema note (legacy field names)

Is project mein posts table ka column `post_content` **category id** store karta hai (description nahi). Description / body `post_keywords` mein hai. Joins aise hain:

```sql
LEFT JOIN category ON posts.post_content = category.id
LEFT JOIN users ON posts.post_author = users.id
```

SQL dump (`database/news-cms.sql`) isi mapping ke mutabiq hai — purana empty `post` / `user` schema use mat karna.

### Upload folder

Admin post images `admin/upload/` mein save hoti hain. Folder writable hona chahiye:

```bash
mkdir -p admin/upload
chmod 755 admin/upload
```

### Common errors

| Symptom | Likely fix |
| --- | --- |
| Database connection failed | `brew services start mysql` + re-import SQL |
| 404 on `/news-site` | Alias missing / wrong path / `httpd` not started |
| Blank / download PHP | PHP module not loaded in Apache |
| Login fails | Re-import seed (`admin` / `admin`) or check MD5 password |
| Icons missing | Font Awesome 6 CDN needs network (or check browser console) |

### Icons / UI

Front + admin **Font Awesome 6** (CDN) use karte hain — house, tags, calendar, search, users, etc. Copy English mein hi hai (Home, Admin, Search, read more…).

### Vue.js + realtime search

- **Vue 3** CDN + `js/news-app.js` — header/sidebar pe **autocomplete search**, home pe **Live from the web** feed.
- Autocomplete local DB posts + live RSS headlines mix karta hai (`api/search-suggest.php`).
- Live feed BBC / NPR / Guardian RSS se pull hota hai (`api/live-news.php`), har ~3 min auto-refresh.
- PHP `allow_url_fopen` on hona chahiye (RSS fetch ke liye).

Quick API check:

```bash
curl -s "http://localhost/news-site/api/live-news.php?limit=3"
curl -s "http://localhost/news-site/api/search-suggest.php?q=the&live=1"
```

---

## Project layout

```text
News-Site/
├── index.php          # home + Vue live feed
├── single.php         # post detail
├── category.php       # posts by category (?cid=)
├── author.php         # posts by author (?aid=)
├── search.php         # title/keywords search
├── header.php         # shared head + Vue autocomplete
├── footer.php         # Vue CDN + news-app.js
├── sidebar.php        # Vue search + recent posts
├── config.php         # MySQL + $hostname
├── api/
│   ├── live-news.php      # JSON realtime headlines
│   └── search-suggest.php # autocomplete suggestions
├── includes/
│   ├── helpers.php
│   └── live_feed.php  # RSS fetch (BBC/NPR/Guardian)
├── js/news-app.js     # Vue SearchAutocomplete + LiveNewsFeed
├── admin/             # login, posts, categories, users
├── css/               # bootstrap + style.css
├── images/            # logos + post images
├── database/
│   └── news-cms.sql   # schema + sample data
├── CONTRIBUTORS.md
└── README.md
```

---

## Git commits (contribution graph)

Commits is email se honi chahiye taake GitHub pe **muhammadadi-iL** ki progress dikhe:

```text
Muhammad Adil <adilm0616@gmail.com>
```

Email GitHub account pe **verified** rakho. Dekho: [Contributors graph](https://github.com/muhammadadi-iL/News-Site/graphs/contributors).

---

## License / credit

Learning / practice CMS project. UI refreshed for local demo; original tutorial-style structure retained.
