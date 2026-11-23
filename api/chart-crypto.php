<?php
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: public, max-age=60');
header('Access-Control-Allow-Origin: *');

require_once dirname(__DIR__) . '/includes/markets.php';

$id = isset($_GET['id']) ? (string) $_GET['id'] : 'bitcoin';
$days = isset($_GET['days']) ? (int) $_GET['days'] : 7;
$force = isset($_GET['refresh']) && $_GET['refresh'] === '1';

$bundle = markets_fetch_crypto_chart($id, $days, $force);

echo json_encode($bundle, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
