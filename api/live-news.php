<?php
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: public, max-age=60');
header('Access-Control-Allow-Origin: *');

require_once dirname(__DIR__) . '/includes/live_feed.php';

$limit = isset($_GET['limit']) ? max(1, min(30, (int) $_GET['limit'])) : 12;
$query = isset($_GET['q']) ? trim((string) $_GET['q']) : '';
$force = isset($_GET['refresh']) && $_GET['refresh'] === '1';

if ($force) {
    news_live_bundle(true);
}

$meta = news_live_meta();
$items = news_live_items($limit, $query);

echo json_encode([
    'ok' => true,
    'count' => count($items),
    'fetched_at' => $meta['fetched_at'],
    'from_cache' => $meta['from_cache'],
    'sources' => $meta['sources'],
    'items' => $items,
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
