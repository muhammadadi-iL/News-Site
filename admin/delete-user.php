<?php
include "config.php";
require_admin_role($hostname);

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id < 1) {
    header("Location: {$hostname}/admin/users.php");
    exit;
}

if ($id === (int) $_SESSION['id']) {
    die("<div style='font-family:sans-serif;margin:2rem;'>You cannot delete your own account. <a href='users.php'>Back</a></div>");
}

if (mysqli_query($conn, "DELETE FROM users WHERE id = {$id}")) {
    header("Location: {$hostname}/admin/users.php");
    exit;
}

echo "Can't delete user record";
