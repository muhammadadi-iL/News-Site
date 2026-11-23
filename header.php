<?php
include "config.php";

$page = basename($_SERVER['PHP_SELF']);
$page_title = "News Site";

switch ($page) {
    case "single.php":
        if (isset($_GET['id'])) {
            $sql_title = "SELECT post_title FROM posts WHERE id = " . (int) $_GET['id'];
            $result_title = mysqli_query($conn, $sql_title) or die('Title Query Failed');
            $row_title = mysqli_fetch_assoc($result_title);
            $page_title = $row_title ? $row_title['post_title'] : "No Post Found";
        }
        break;
    case "category.php":
        if (isset($_GET['cid'])) {
            $cid = (int) $_GET['cid'];
            $sql_title = "SELECT category_name FROM category WHERE id = {$cid}";
            $result_title = mysqli_query($conn, $sql_title) or die('Title Query Failed');
            $row_title = mysqli_fetch_assoc($result_title);
            $page_title = $row_title ? $row_title['category_name'] : "Category";
        } else {
            $page_title = "Category";
        }
        break;
    case "author.php":
        if (isset($_GET['aid'])) {
            $aid = (int) $_GET['aid'];
            $sql_title = "SELECT username FROM users WHERE id = {$aid}";
            $result_title = mysqli_query($conn, $sql_title) or die('Title Query Failed');
            $row_title = mysqli_fetch_assoc($result_title);
            $page_title = $row_title ? ("Author: " . $row_title['username']) : "Author";
        } else {
            $page_title = "Author";
        }
        break;
    case "search.php":
        $page_title = isset($_GET['search']) ? ("Search: " . $_GET['search']) : "Search";
        break;
    default:
        $page_title = "News Site — Latest Headlines";
        break;
}

$breaking = mysqli_query($conn, "SELECT id, post_title FROM posts ORDER BY id DESC LIMIT 1");
$break_row = $breaking ? mysqli_fetch_assoc($breaking) : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo e($page_title); ?></title>
    <link rel="icon" href="images/favicon.ico" sizes="any">
    <link rel="icon" href="images/favicon-32.png" type="image/png" sizes="32x32">
    <link rel="apple-touch-icon" href="images/apple-touch-icon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="css/style.css">
    <meta name="news-api-base" content="<?php echo e(rtrim($hostname, '/') . '/api'); ?>">
</head>
<body>
<?php if ($break_row) { ?>
<div class="breaking-bar">
    <div class="container breaking-inner">
        <span class="breaking-label"><i class="fa-solid fa-tower-broadcast"></i> Breaking</span>
        <a href="single.php?id=<?php echo (int) $break_row['id']; ?>"><?php echo e($break_row['post_title']); ?></a>
    </div>
</div>
<?php } ?>

<header id="header">
    <div class="container">
        <div class="header-inner">
            <a href="<?php echo $hostname; ?>" id="logo" class="brand-lockup">
                <img src="images/logo.png" alt="News Site">
                <span class="brand-text">
                    <strong>NEWS SITE</strong>
                    <em>Independent reporting for every screen</em>
                </span>
            </a>
            <div class="header-actions header-search-slot">
                <div class="header-vue-search" data-vue-search data-initial=""></div>
                <a class="btn btn-accent" href="admin/"><i class="fa-solid fa-lock"></i><span>Admin</span></a>
            </div>
        </div>
    </div>
</header>

<nav id="menu-bar">
    <div class="container">
        <?php
        $id = isset($_GET['cid']) ? (int) $_GET['cid'] : 0;
        $sql = "SELECT * FROM category ORDER BY category_name ASC";
        $result = mysqli_query($conn, $sql) or die("Query Failed : Category");
        ?>
        <div class="nav-bar-inner">
            <button type="button" class="nav-toggle" id="navToggle" aria-controls="siteMenu" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fa-solid fa-bars" aria-hidden="true"></i>
                <span>Sections</span>
            </button>
            <ul class="menu" id="siteMenu">
                <li>
                    <a class="<?php echo ($page === 'index.php' && $id === 0) ? 'active' : ''; ?>" href="<?php echo $hostname; ?>">
                        <i class="fa-solid fa-house"></i> Home
                    </a>
                </li>
                <?php
                while ($row = mysqli_fetch_assoc($result)) {
                    $active = ($id === (int) $row['id']) ? "active" : "";
                    $icon = category_icon($row['category_name']);
                    echo "<li><a class='{$active}' href='category.php?cid=" . (int) $row['id'] . "'>"
                        . "<i class='fa-solid {$icon}'></i> "
                        . e($row['category_name'])
                        . "</a></li>";
                }
                ?>
            </ul>
        </div>
    </div>
</nav>
