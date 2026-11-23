<?php
include "config.php";
require_admin_session($hostname);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: {$hostname}/admin/add-post.php");
    exit;
}

$post_title = mysqli_real_escape_string($conn, trim($_POST['post_title'] ?? ''));
$post_keywords = mysqli_real_escape_string($conn, trim($_POST['post_keywords'] ?? ''));
$category_id = (int) ($_POST['post_content'] ?? 0);
$post_date = date('d M, Y');
$post_author = (int) $_SESSION['id'];

if ($post_title === '' || $post_keywords === '' || $category_id < 1) {
    die("<div class='alert alert-danger' style='margin:2rem;'>Title, description and category are required. <a href='add-post.php'>Go back</a></div>");
}

list($ok, $result) = save_uploaded_image('filetoupload', __DIR__ . '/upload');
if (!$ok) {
    die("<div class='alert alert-danger' style='margin:2rem;'>" . e($result) . " <a href='add-post.php'>Go back</a></div>");
}
$post_image = mysqli_real_escape_string($conn, $result);

// Also keep a copy under images/ for simple front paths
@copy(__DIR__ . '/upload/' . $result, dirname(__DIR__) . '/images/' . $result);

$sql = "INSERT INTO posts (post_title, post_date, post_author, post_keywords, post_content, post_image)
        VALUES ('{$post_title}', '{$post_date}', {$post_author}, '{$post_keywords}', {$category_id}, '{$post_image}')";

if (mysqli_query($conn, $sql)) {
    mysqli_query($conn, "UPDATE category SET post = post + 1 WHERE id = {$category_id}");
    header("Location: {$hostname}/admin/post.php");
    exit;
}

echo "<div class='alert alert-danger' style='margin:2rem;'>Query Failed: " . e(mysqli_error($conn)) . "</div>";
