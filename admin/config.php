<?php

$hostname = "http://localhost/news-site";

$username = "root";
$password = "";
$server = "localhost";
$db = "news_site";

$conn = mysqli_connect($server, $username, $password, $db);

if (!$conn) {
    die(
        "<div style='font-family:sans-serif;padding:2rem;max-width:640px;margin:2rem auto;border:1px solid #f5c2c7;background:#f8d7da;color:#842029;border-radius:8px;'>"
        . "<strong>Database connection failed.</strong><br>"
        . "Check MySQL is running and <code>news_site</code> is imported from "
        . "<code>database/news-cms.sql</code>.<br><br>"
        . htmlspecialchars(mysqli_connect_error())
        . "</div>"
    );
}

mysqli_set_charset($conn, "utf8mb4");

require_once dirname(__DIR__) . '/includes/helpers.php';
