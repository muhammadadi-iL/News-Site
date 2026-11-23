<?php
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: public, max-age=30');
header('Access-Control-Allow-Origin: *');

require_once dirname(__DIR__) . '/includes/markets.php';

$force = isset($_GET['refresh']) && $_GET['refresh'] === '1';
$bundle = markets_fetch_crypto($force);

echo json_encode([
    'ok' => !empty($bundle['items']),
    'count' => isset($bundle['items']) ? count($bundle['items']) : 0,
    'fetched_at' => $bundle['fetched_at'] ?? date('c'),
    'from_cache' => !empty($bundle['from_cache']),
    'stale' => !empty($bundle['stale']),
    'items' => $bundle['items'] ?? [],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
