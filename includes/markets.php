<?php

/**
 * Realtime crypto + forex helpers (CoinGecko + Frankfurter) with short file cache.
 */

function markets_cache_dir()
{
    $dir = dirname(__DIR__) . '/cache';
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
    return $dir;
}

function markets_http_get($url, $timeout = 8)
{
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'timeout' => $timeout,
            'follow_location' => 1,
            'max_redirects' => 3,
            'header' => "User-Agent: PulseMarketsBot/1.0 (+localhost)\r\nAccept: application/json\r\n",
        ],
        'ssl' => [
            'verify_peer' => true,
            'verify_peer_name' => true,
        ],
    ]);

    $raw = @file_get_contents($url, false, $context);
    return ($raw === false || $raw === '') ? null : $raw;
}

function markets_cache_get($key, $ttl)
{
    $file = markets_cache_dir() . '/' . $key . '.json';
    if (!is_file($file)) {
        return null;
    }
    if (time() - filemtime($file) >= $ttl) {
        return null;
    }
    $data = json_decode((string) file_get_contents($file), true);
    return is_array($data) ? $data : null;
}

function markets_cache_set($key, $data)
{
    $file = markets_cache_dir() . '/' . $key . '.json';
    @file_put_contents($file, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), LOCK_EX);
}

function markets_crypto_list()
{
    return [
        'bitcoin' => 'BTC',
        'ethereum' => 'ETH',
        'solana' => 'SOL',
        'ripple' => 'XRP',
        'binancecoin' => 'BNB',
        'cardano' => 'ADA',
        'dogecoin' => 'DOGE',
        'polkadot' => 'DOT',
    ];
}

function markets_fetch_crypto($force = false)
{
    if (!$force) {
        $cached = markets_cache_get('crypto-markets', 45);
        if ($cached) {
            $cached['from_cache'] = true;
            return $cached;
        }
    }

    $ids = implode(',', array_keys(markets_crypto_list()));
    $url = 'https://api.coingecko.com/api/v3/coins/markets?vs_currency=usd'
        . '&ids=' . rawurlencode($ids)
        . '&order=market_cap_desc&per_page=20&page=1&sparkline=false&price_change_percentage=24h';

    $raw = markets_http_get($url, 10);
    $list = $raw ? json_decode($raw, true) : null;
    $symbols = markets_crypto_list();
    $items = [];

    if (is_array($list)) {
        foreach ($list as $coin) {
            $id = $coin['id'] ?? '';
            $items[] = [
                'id' => $id,
                'symbol' => strtoupper($coin['symbol'] ?? ($symbols[$id] ?? '')),
                'name' => $coin['name'] ?? $id,
                'price' => isset($coin['current_price']) ? (float) $coin['current_price'] : null,
                'change_24h' => isset($coin['price_change_percentage_24h']) ? (float) $coin['price_change_percentage_24h'] : null,
                'market_cap' => isset($coin['market_cap']) ? (float) $coin['market_cap'] : null,
                'volume' => isset($coin['total_volume']) ? (float) $coin['total_volume'] : null,
                'image' => $coin['image'] ?? '',
            ];
        }
    }

    // Stale cache fallback
    if (empty($items)) {
        $file = markets_cache_dir() . '/crypto-markets.json';
        if (is_file($file)) {
            $stale = json_decode((string) file_get_contents($file), true);
            if (is_array($stale) && !empty($stale['items'])) {
                $stale['from_cache'] = true;
                $stale['stale'] = true;
                return $stale;
            }
        }
    }

    $bundle = [
        'ok' => !empty($items),
        'fetched_at' => date('c'),
        'from_cache' => false,
        'items' => $items,
    ];

    if (!empty($items)) {
        markets_cache_set('crypto-markets', $bundle);
    }

    return $bundle;
}

function markets_fetch_forex($force = false)
{
    if (!$force) {
        $cached = markets_cache_get('forex-rates', 60);
        if ($cached) {
            $cached['from_cache'] = true;
            return $cached;
        }
    }

    $pairs = [
        ['base' => 'EUR', 'quote' => 'USD', 'label' => 'EUR/USD'],
        ['base' => 'GBP', 'quote' => 'USD', 'label' => 'GBP/USD'],
        ['base' => 'USD', 'quote' => 'JPY', 'label' => 'USD/JPY'],
        ['base' => 'USD', 'quote' => 'CHF', 'label' => 'USD/CHF'],
        ['base' => 'AUD', 'quote' => 'USD', 'label' => 'AUD/USD'],
        ['base' => 'USD', 'quote' => 'CAD', 'label' => 'USD/CAD'],
        ['base' => 'EUR', 'quote' => 'GBP', 'label' => 'EUR/GBP'],
        ['base' => 'NZD', 'quote' => 'USD', 'label' => 'NZD/USD'],
    ];

    $items = [];
    $url = 'https://api.frankfurter.app/latest?from=USD&to=EUR,GBP,JPY,CHF,AUD,CAD,NZD';
    $raw = markets_http_get($url, 8);
    $data = $raw ? json_decode($raw, true) : null;
    $rates = is_array($data) && isset($data['rates']) ? $data['rates'] : [];

    // frankfurter returns units of TO per 1 FROM (USD)
    $perUsd = $rates;

    foreach ($pairs as $pair) {
        $base = $pair['base'];
        $quote = $pair['quote'];
        $price = null;

        if ($base === 'USD' && isset($perUsd[$quote])) {
            $price = (float) $perUsd[$quote];
        } elseif ($quote === 'USD' && isset($perUsd[$base]) && $perUsd[$base] != 0) {
            $price = 1 / (float) $perUsd[$base];
        } elseif (isset($perUsd[$base]) && isset($perUsd[$quote]) && $perUsd[$base] != 0) {
            // both vs USD: base/quote = (1/perUsd[base]) / (1/perUsd[quote]) = perUsd[quote]/perUsd[base]
            // Wait: perUsd[EUR]=0.92 means 0.92 EUR per 1 USD, so 1 EUR = 1/0.92 USD
            // EUR/GBP = (USD per EUR) / (USD per GBP) = (1/perUsd[EUR]) / (1/perUsd[GBP]) = perUsd[GBP]/perUsd[EUR]
            $price = (float) $perUsd[$quote] / (float) $perUsd[$base];
        }

        if ($price === null) {
            continue;
        }

        $items[] = [
            'pair' => $pair['label'],
            'base' => $base,
            'quote' => $quote,
            'price' => $price,
            'change_24h' => null,
        ];
    }

    if (empty($items)) {
        $file = markets_cache_dir() . '/forex-rates.json';
        if (is_file($file)) {
            $stale = json_decode((string) file_get_contents($file), true);
            if (is_array($stale) && !empty($stale['items'])) {
                $stale['from_cache'] = true;
                $stale['stale'] = true;
                return $stale;
            }
        }
    }

    $bundle = [
        'ok' => !empty($items),
        'fetched_at' => date('c'),
        'from_cache' => false,
        'base' => 'USD',
        'items' => $items,
    ];

    if (!empty($items)) {
        markets_cache_set('forex-rates', $bundle);
    }

    return $bundle;
}

function markets_allowed_crypto_ids()
{
    return array_keys(markets_crypto_list());
}

function markets_fetch_crypto_chart($coinId, $days = 7, $force = false)
{
    $coinId = strtolower(preg_replace('/[^a-z0-9-]/', '', (string) $coinId));
    if (!in_array($coinId, markets_allowed_crypto_ids(), true)) {
        $coinId = 'bitcoin';
    }
    $days = (int) $days;
    if (!in_array($days, [1, 7, 30, 90], true)) {
        $days = 7;
    }

    $cacheKey = 'chart-crypto-' . $coinId . '-' . $days;
    if (!$force) {
        $cached = markets_cache_get($cacheKey, 120);
        if ($cached) {
            $cached['from_cache'] = true;
            return $cached;
        }
    }

    $url = 'https://api.coingecko.com/api/v3/coins/' . rawurlencode($coinId)
        . '/market_chart?vs_currency=usd&days=' . $days;
    $raw = markets_http_get($url, 12);
    $data = $raw ? json_decode($raw, true) : null;
    $points = [];

    if (is_array($data) && !empty($data['prices']) && is_array($data['prices'])) {
        foreach ($data['prices'] as $row) {
            if (!is_array($row) || count($row) < 2) {
                continue;
            }
            $points[] = [
                't' => (int) $row[0],
                'v' => (float) $row[1],
            ];
        }
        // Keep chart snappy — downsample long series
        if (count($points) > 180) {
            $step = (int) ceil(count($points) / 160);
            $points = array_values(array_filter($points, function ($_, $i) use ($step) {
                return $i % $step === 0;
            }, ARRAY_FILTER_USE_BOTH));
        }
    }

    $symbols = markets_crypto_list();
    $bundle = [
        'ok' => !empty($points),
        'kind' => 'crypto',
        'id' => $coinId,
        'symbol' => $symbols[$coinId] ?? strtoupper($coinId),
        'days' => $days,
        'fetched_at' => date('c'),
        'from_cache' => false,
        'points' => $points,
    ];

    if (!empty($points)) {
        markets_cache_set($cacheKey, $bundle);
    }

    return $bundle;
}

function markets_fetch_forex_chart($base, $quote, $days = 30, $force = false)
{
    $base = strtoupper(preg_replace('/[^A-Za-z]/', '', (string) $base));
    $quote = strtoupper(preg_replace('/[^A-Za-z]/', '', (string) $quote));
    $allowed = ['USD', 'EUR', 'GBP', 'JPY', 'CHF', 'AUD', 'CAD', 'NZD'];
    if (!in_array($base, $allowed, true) || !in_array($quote, $allowed, true) || $base === $quote) {
        $base = 'EUR';
        $quote = 'USD';
    }
    $days = (int) $days;
    if (!in_array($days, [7, 30, 90], true)) {
        $days = 30;
    }

    $cacheKey = 'chart-fx-' . $base . $quote . '-' . $days;
    if (!$force) {
        $cached = markets_cache_get($cacheKey, 180);
        if ($cached) {
            $cached['from_cache'] = true;
            return $cached;
        }
    }

    $end = new DateTime('today');
    $start = (clone $end)->modify('-' . $days . ' days');
    $url = 'https://api.frankfurter.app/'
        . $start->format('Y-m-d') . '..' . $end->format('Y-m-d')
        . '?from=' . rawurlencode($base)
        . '&to=' . rawurlencode($quote);

    $raw = markets_http_get($url, 12);
    $data = $raw ? json_decode($raw, true) : null;
    $points = [];

    if (is_array($data) && !empty($data['rates']) && is_array($data['rates'])) {
        ksort($data['rates']);
        foreach ($data['rates'] as $date => $row) {
            if (!isset($row[$quote])) {
                continue;
            }
            $ts = strtotime($date . ' UTC');
            if ($ts === false) {
                continue;
            }
            $points[] = [
                't' => $ts * 1000,
                'v' => (float) $row[$quote],
            ];
        }
    }

    $bundle = [
        'ok' => !empty($points),
        'kind' => 'forex',
        'base' => $base,
        'quote' => $quote,
        'pair' => $base . '/' . $quote,
        'days' => $days,
        'fetched_at' => date('c'),
        'from_cache' => false,
        'points' => $points,
    ];

    if (!empty($points)) {
        markets_cache_set($cacheKey, $bundle);
    }

    return $bundle;
}
