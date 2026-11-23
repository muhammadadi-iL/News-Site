<?php
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('Access-Control-Allow-Origin: *');

require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/live_feed.php';

$q = isset($_GET['q']) ? trim((string) $_GET['q']) : '';
$includeLive = !isset($_GET['live']) || $_GET['live'] !== '0';
$limit = isset($_GET['limit']) ? max(1, min(15, (int) $_GET['limit'])) : 8;

$qLen = function_exists('mb_strlen') ? mb_strlen($q) : strlen($q);
if ($qLen < 1) {
    echo json_encode(['ok' => true, 'query' => $q, 'suggestions' => []]);
    exit;
}

$safe = mysqli_real_escape_string($conn, $q);
$sql = "SELECT posts.id, posts.post_title, posts.post_keywords, category.category_name
        FROM posts
        LEFT JOIN category ON posts.post_content = category.id
        WHERE posts.post_title LIKE '%{$safe}%'
           OR posts.post_keywords LIKE '%{$safe}%'
           OR category.category_name LIKE '%{$safe}%'
        ORDER BY posts.id DESC
        LIMIT {$limit}";

$suggestions = [];
$result = mysqli_query($conn, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $suggestions[] = [
            'id' => (int) $row['id'],
            'label' => $row['post_title'],
            'category' => $row['category_name'] ?: 'News',
            'type' => 'local',
            'url' => 'single.php?id=' . (int) $row['id'],
            'search_url' => 'search.php?search=' . rawurlencode($row['post_title']),
        ];
    }
}

if ($includeLive && count($suggestions) < $limit) {
    $liveItems = news_live_items(12, $q);
    foreach ($liveItems as $item) {
        if (count($suggestions) >= $limit) {
            break;
        }
        $suggestions[] = [
            'id' => null,
            'label' => $item['title'],
            'category' => $item['source'] ?? 'Live',
            'type' => 'live',
            'url' => $item['link'],
            'search_url' => 'search.php?search=' . rawurlencode($item['title']),
        ];
    }
}

echo json_encode([
    'ok' => true,
    'query' => $q,
    'suggestions' => $suggestions,
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
