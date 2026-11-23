<?php
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: public, max-age=60');
header('Access-Control-Allow-Origin: *');

require_once dirname(__DIR__) . '/includes/markets.php';

$base = isset($_GET['base']) ? (string) $_GET['base'] : 'EUR';
$quote = isset($_GET['quote']) ? (string) $_GET['quote'] : 'USD';
$days = isset($_GET['days']) ? (int) $_GET['days'] : 30;
$force = isset($_GET['refresh']) && $_GET['refresh'] === '1';

$bundle = markets_fetch_forex_chart($base, $quote, $days, $force);

echo json_encode($bundle, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
