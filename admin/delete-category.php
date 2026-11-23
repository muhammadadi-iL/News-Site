<?php
include "config.php";
require_admin_role($hostname);

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id < 1) {
    header("Location: {$hostname}/admin/category.php");
    exit;
}

$check = mysqli_query($conn, "SELECT COUNT(*) AS c FROM posts WHERE post_content = {$id}");
$count = (int) mysqli_fetch_assoc($check)['c'];
if ($count > 0) {
    die("<div style='font-family:sans-serif;margin:2rem;'>Cannot delete: {$count} post(s) still use this category. <a href='category.php'>Back</a></div>");
}

if (mysqli_query($conn, "DELETE FROM category WHERE id = {$id}")) {
    header("Location: {$hostname}/admin/category.php");
    exit;
}

echo "Can't delete category record";
