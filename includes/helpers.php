<?php

/**
 * Resolve a post image to a public URL path (relative to site root).
 * Checks admin/upload first, then images/.
 */
function post_image_src($filename, $prefix = '')
{
    $filename = trim((string) $filename);
    if ($filename === '') {
        return $prefix . 'images/post-format.jpg';
    }

    $candidates = [
        'images/' . $filename,
        'admin/upload/' . $filename,
    ];

    $root = dirname(__DIR__);
    foreach ($candidates as $rel) {
        if (is_file($root . '/' . $rel)) {
            return $prefix . $rel;
        }
    }

    return $prefix . 'images/post-format.jpg';
}

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function require_admin_session($hostname)
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (!isset($_SESSION['username'])) {
        header("Location: {$hostname}/admin/");
        exit;
    }
}

function require_admin_role($hostname)
{
    require_admin_session($hostname);
    if ((string) $_SESSION['role'] !== '1') {
        header("Location: {$hostname}/admin/post.php");
        exit;
    }
}

function allowed_image_extension($filename)
{
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    return in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true);
}

function save_uploaded_image($fileField, $destinationDir)
{
    if (!isset($_FILES[$fileField]) || $_FILES[$fileField]['error'] !== UPLOAD_ERR_OK) {
        return [false, 'Image upload failed.'];
    }

    $original = $_FILES[$fileField]['name'];
    if (!allowed_image_extension($original)) {
        return [false, 'Only JPG, PNG, WEBP or GIF images are allowed.'];
    }

    if (!is_dir($destinationDir)) {
        mkdir($destinationDir, 0755, true);
    }

    $safe = preg_replace('/[^a-zA-Z0-9._-]/', '-', pathinfo($original, PATHINFO_FILENAME));
    $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));
    $filename = $safe . '-' . time() . '.' . $ext;
    $target = rtrim($destinationDir, '/') . '/' . $filename;

    if (!move_uploaded_file($_FILES[$fileField]['tmp_name'], $target)) {
        return [false, 'Could not save uploaded image.'];
    }

    return [true, $filename];
}
