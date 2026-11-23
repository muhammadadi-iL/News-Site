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
    <link rel="icon" href="images/logo.png" type="image/png" sizes="256x256">
    <link rel="apple-touch-icon" href="images/apple-touch-icon.png">
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header id="header">
    <div class="container">
        <div class="header-inner">
            <a href="<?php echo $hostname; ?>" id="logo" class="brand-lockup">
                <img src="images/logo.png" alt="News Site">
                <span class="brand-text">
                    <strong>NEWS SITE</strong>
                    <em>Headlines · Stories · Insight</em>
                </span>
            </a>
            <div class="header-actions">
                <a class="btn btn-ghost" href="search.php"><i class="fa-solid fa-magnifying-glass"></i><span>Search</span></a>
                <a class="btn btn-light-solid" href="admin/"><i class="fa-solid fa-lock"></i><span>Admin</span></a>
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
                        <span>Menu</span>
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
