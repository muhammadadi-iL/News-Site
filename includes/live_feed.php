<?php

/**
 * Fetch and normalize public RSS/Atom headlines for the live news APIs.
 * Uses a short file cache so autocomplete stays fast while data stays near-realtime.
 */

function news_feed_cache_dir()
{
    $dir = dirname(__DIR__) . '/cache';
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
    return $dir;
}

function news_fetch_feed_xml($url)
{
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'timeout' => 6,
            'follow_location' => 1,
            'max_redirects' => 3,
            'header' => "User-Agent: Mozilla/5.0 (compatible; NewsSiteBot/1.1; +localhost)\r\nAccept: application/rss+xml, application/atom+xml, application/xml, text/xml, */*\r\n",
        ],
        'ssl' => [
            'verify_peer' => true,
            'verify_peer_name' => true,
        ],
    ]);

    $raw = @file_get_contents($url, false, $context);
    if ($raw === false || $raw === '') {
        return null;
    }

    libxml_use_internal_errors(true);
    $xml = simplexml_load_string($raw, 'SimpleXMLElement', LIBXML_NOCDATA);
    return $xml ?: null;
}

function news_feed_entries($xml)
{
    if (!$xml) {
        return [];
    }

    $entries = [];

    // RSS 2.0
    if (isset($xml->channel->item)) {
        foreach ($xml->channel->item as $item) {
            $entries[] = $item;
        }
        return $entries;
    }

    // Atom
    if (isset($xml->entry)) {
        foreach ($xml->entry as $entry) {
            $entries[] = $entry;
        }
    }

    return $entries;
}

function news_entry_link($entry)
{
    if (isset($entry->link)) {
        // RSS: <link>url</link>
        $text = trim((string) $entry->link);
        if ($text !== '') {
            return $text;
        }
        // Atom: <link href="..."/>
        foreach ($entry->link as $link) {
            $attrs = $link->attributes();
            if (isset($attrs['href'])) {
                $rel = isset($attrs['rel']) ? (string) $attrs['rel'] : 'alternate';
                if ($rel === 'alternate' || $rel === '') {
                    return trim((string) $attrs['href']);
                }
            }
        }
        $attrs = $entry->link->attributes();
        if (isset($attrs['href'])) {
            return trim((string) $attrs['href']);
        }
    }
    return '';
}

function news_entry_summary($entry)
{
    $raw = '';
    if (isset($entry->description)) {
        $raw = (string) $entry->description;
    } elseif (isset($entry->summary)) {
        $raw = (string) $entry->summary;
    } elseif (isset($entry->content)) {
        $raw = (string) $entry->content;
    }
    return trim(strip_tags($raw));
}

function news_entry_published($entry)
{
    $pub = '';
    if (isset($entry->pubDate)) {
        $pub = trim((string) $entry->pubDate);
    } elseif (isset($entry->published)) {
        $pub = trim((string) $entry->published);
    } elseif (isset($entry->updated)) {
        $pub = trim((string) $entry->updated);
    }
    return $pub !== '' ? date('c', strtotime($pub) ?: time()) : date('c');
}

function news_entry_image($entry)
{
    $image = '';
    if (isset($entry->children('media', true)->thumbnail)) {
        $attrs = $entry->children('media', true)->thumbnail->attributes();
        $image = isset($attrs['url']) ? (string) $attrs['url'] : '';
    }
    if ($image === '' && isset($entry->children('media', true)->content)) {
        $attrs = $entry->children('media', true)->content->attributes();
        $type = isset($attrs['type']) ? (string) $attrs['type'] : '';
        if (isset($attrs['url']) && ($type === '' || strpos($type, 'image/') === 0)) {
            $image = (string) $attrs['url'];
        }
    }
    if ($image === '' && isset($entry->enclosure)) {
        $enc = $entry->enclosure->attributes();
        $type = isset($enc['type']) ? (string) $enc['type'] : '';
        if (isset($enc['url']) && strpos($type, 'image/') === 0) {
            $image = (string) $enc['url'];
        }
    }
    return $image;
}

function news_parse_feed_items($source, $url)
{
    $xml = news_fetch_feed_xml($url);
    $entries = news_feed_entries($xml);
    $items = [];

    foreach ($entries as $entry) {
        $title = trim((string) $entry->title);
        $link = news_entry_link($entry);
        if ($title === '' || $link === '') {
            continue;
        }

        $desc = news_entry_summary($entry);
        $items[] = [
            'title' => $title,
            'link' => $link,
            'summary' => function_exists('mb_substr') ? mb_substr($desc, 0, 180) : substr($desc, 0, 180),
            'source' => $source,
            'published' => news_entry_published($entry),
            'image' => news_entry_image($entry),
            'type' => 'live',
        ];
    }

    return $items;
}

function news_live_sources()
{
    // Primary outlets first. `fallback` sources only run if fewer than 3 primaries succeed.
    return [
        [
            'source' => 'BBC News',
            'fallback' => false,
            'urls' => [
                'https://feeds.bbci.co.uk/news/rss.xml',
                'https://feeds.bbci.co.uk/news/world/rss.xml',
            ],
        ],
        [
            'source' => 'NPR',
            'fallback' => false,
            'urls' => [
                'https://feeds.npr.org/1001/rss.xml',
                'https://www.npr.org/rss/rss.php?id=1001',
                'https://feeds.npr.org/1004/rss.xml',
            ],
        ],
        [
            'source' => 'The Guardian',
            'fallback' => false,
            'urls' => [
                'https://www.theguardian.com/world/rss',
                'https://www.theguardian.com/uk/rss',
            ],
        ],
        [
            'source' => 'NYTimes',
            'fallback' => true,
            'urls' => [
                'https://rss.nytimes.com/services/xml/rss/nyt/World.xml',
            ],
        ],
    ];
}

function news_fetch_all_live_items()
{
    $items = [];
    $activeSources = [];
    $primarySuccess = 0;

    foreach (news_live_sources() as $feed) {
        if (!empty($feed['fallback']) && $primarySuccess >= 3) {
            continue;
        }

        $parsed = [];
        foreach ($feed['urls'] as $url) {
            $parsed = news_parse_feed_items($feed['source'], $url);
            if (!empty($parsed)) {
                break;
            }
        }
        if (empty($parsed)) {
            continue;
        }
        $activeSources[] = $feed['source'];
        if (empty($feed['fallback'])) {
            $primarySuccess++;
        }
        foreach ($parsed as $item) {
            $items[] = $item;
        }
    }

    usort($items, function ($a, $b) {
        return strcmp($b['published'], $a['published']);
    });

    // De-dupe by link
    $seen = [];
    $unique = [];
    foreach ($items as $item) {
        $key = strtolower($item['link']);
        if (isset($seen[$key])) {
            continue;
        }
        $seen[$key] = true;
        $unique[] = $item;
    }

    return [
        'items' => $unique,
        'sources' => $activeSources,
        'fetched_at' => date('c'),
    ];
}

function news_live_bundle($forceRefresh = false)
{
    $cacheFile = news_feed_cache_dir() . '/live-news.json';
    $ttl = 90; // seconds — near-realtime, fast for autocomplete

    if (!$forceRefresh && is_file($cacheFile)) {
        $age = time() - filemtime($cacheFile);
        if ($age >= 0 && $age < $ttl) {
            $cached = json_decode((string) file_get_contents($cacheFile), true);
            if (is_array($cached) && isset($cached['items']) && is_array($cached['items'])) {
                $cached['from_cache'] = true;
                return $cached;
            }
        }
    }

    $bundle = news_fetch_all_live_items();
    $bundle['from_cache'] = false;

    if (!empty($bundle['items'])) {
        @file_put_contents($cacheFile, json_encode($bundle, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), LOCK_EX);
    } elseif (is_file($cacheFile)) {
        // Keep last good cache if fresh fetch failed entirely.
        $cached = json_decode((string) file_get_contents($cacheFile), true);
        if (is_array($cached) && !empty($cached['items'])) {
            $cached['from_cache'] = true;
            $cached['stale'] = true;
            return $cached;
        }
    }

    return $bundle;
}

function news_live_items($limit = 12, $query = '')
{
    $bundle = news_live_bundle(false);
    $items = $bundle['items'] ?? [];

    if ($query !== '') {
        $lower = function_exists('mb_strtolower') ? 'mb_strtolower' : 'strtolower';
        $q = $lower($query);
        $items = array_values(array_filter($items, function ($item) use ($q, $lower) {
            $hay = $lower(($item['title'] ?? '') . ' ' . ($item['summary'] ?? ''));
            return $q === '' || strpos($hay, $q) !== false;
        }));
    }

    return array_slice($items, 0, $limit);
}

function news_live_meta()
{
    $bundle = news_live_bundle(false);
    return [
        'sources' => $bundle['sources'] ?? [],
        'fetched_at' => $bundle['fetched_at'] ?? date('c'),
        'from_cache' => !empty($bundle['from_cache']),
        'count' => isset($bundle['items']) ? count($bundle['items']) : 0,
    ];
}
