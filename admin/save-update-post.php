<?php
include "config.php";
require_admin_session($hostname);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: {$hostname}/admin/post.php");
    exit;
}

$id = (int) ($_POST['id'] ?? 0);
$post_title = mysqli_real_escape_string($conn, trim($_POST['post_title'] ?? ''));
$post_keywords = mysqli_real_escape_string($conn, trim($_POST['post_keywords'] ?? ''));
$new_category = (int) ($_POST['post_content'] ?? 0);
$old_image = mysqli_real_escape_string($conn, $_POST['old_image'] ?? '');

if ($id < 1 || $post_title === '' || $post_keywords === '' || $new_category < 1) {
    die("<div class='alert alert-danger' style='margin:2rem;'>Invalid form data. <a href='post.php'>Back</a></div>");
}

$current = mysqli_query($conn, "SELECT post_content, post_image, post_author FROM posts WHERE id = {$id}") or die('Post not found');
$row = mysqli_fetch_assoc($current);
if (!$row) {
    die('Post not found');
}

if ((string) $_SESSION['role'] !== '1' && (int) $row['post_author'] !== (int) $_SESSION['id']) {
    die('Not allowed.');
}

$old_category = (int) $row['post_content'];
$post_image = $old_image !== '' ? $old_image : $row['post_image'];

if (!empty($_FILES['new_image']['name'])) {
    list($ok, $result) = save_uploaded_image('new_image', __DIR__ . '/upload');
    if (!$ok) {
        die("<div class='alert alert-danger' style='margin:2rem;'>" . e($result) . "</div>");
    }
    $post_image = mysqli_real_escape_string($conn, $result);
    @copy(__DIR__ . '/upload/' . $result, dirname(__DIR__) . '/images/' . $result);
}

$sql = "UPDATE posts SET
            post_title = '{$post_title}',
            post_keywords = '{$post_keywords}',
            post_content = {$new_category},
            post_image = '{$post_image}'
        WHERE id = {$id}";

if (mysqli_query($conn, $sql)) {
    if ($old_category !== $new_category) {
        mysqli_query($conn, "UPDATE category SET post = GREATEST(post - 1, 0) WHERE id = {$old_category}");
        mysqli_query($conn, "UPDATE category SET post = post + 1 WHERE id = {$new_category}");
    }
    header("Location: {$hostname}/admin/post.php");
    exit;
}

echo "Query Failed: " . e(mysqli_error($conn));
