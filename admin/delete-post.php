<?php
include "config.php";
require_admin_session($hostname);

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id < 1) {
    header("Location: {$hostname}/admin/post.php");
    exit;
}

$result = mysqli_query($conn, "SELECT * FROM posts WHERE id = {$id}") or die('Query Failed : Select');
$row = mysqli_fetch_assoc($result);

if (!$row) {
    header("Location: {$hostname}/admin/post.php");
    exit;
}

if ((string) $_SESSION['role'] !== '1' && (int) $row['post_author'] !== (int) $_SESSION['id']) {
    die('Not allowed.');
}

$catid = (int) $row['post_content'];
$img = $row['post_image'];

if (!empty($img)) {
    $paths = [
        __DIR__ . '/upload/' . $img,
        dirname(__DIR__) . '/images/' . $img,
    ];
    foreach ($paths as $path) {
        if (is_file($path) && strpos(basename($path), 'post-') === 0) {
            // keep seed demo images; only remove uniquely uploaded timed files if desired
        }
        if (is_file($path) && preg_match('/-\d{10}\./', basename($path))) {
            @unlink($path);
        }
    }
}

if (mysqli_query($conn, "DELETE FROM posts WHERE id = {$id}")) {
    mysqli_query($conn, "UPDATE category SET post = GREATEST(post - 1, 0) WHERE id = {$catid}");
    header("Location: {$hostname}/admin/post.php");
    exit;
}

echo "Query Failed";
