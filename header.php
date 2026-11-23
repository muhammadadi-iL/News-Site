<?php
include "config.php";

$page = basename($_SERVER['PHP_SELF']);
$page_title = "News Site";

switch ($page) {
    case "single.php":
        if (isset($_GET['id'])) {
            $sql_title = "SELECT * FROM posts WHERE id = {$_GET['id']}";
            $result_title = mysqli_query($conn, $sql_title) or die('Title Query Failed');
            $row_title = mysqli_fetch_assoc($result_title);
            $page_title = $row_title ? $row_title['post_title'] : "No Post Found";
        } else {
            $page_title = "No Post Found";
        }
        break;

    case "category.php":
        if (isset($_GET['cid'])) {
            $cid = (int) $_GET['cid'];
            $sql_title = "SELECT * FROM category WHERE id = {$cid}";
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
        $page_title = "News Site";
        break;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <link rel="icon" href="images/news.jpg" type="image/jpeg">
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<!-- HEADER -->
<div id="header">
    <div class="container">
        <div class="row">
            <div class="col-md-offset-4 col-md-4">
                <a href="<?php echo $hostname; ?>" id="logo"><img src="images/news.jpg" alt="News Site"></a>
            </div>
        </div>
    </div>
</div>
<!-- /HEADER -->
<!-- Menu Bar -->
<div id="menu-bar">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
            <?php
            if (isset($_GET['cid'])) {
                $id = (int) $_GET['cid'];
            }

            $sql = "SELECT * FROM category";
            $result = mysqli_query($conn, $sql) or die("Query Failed : Category");
            $active = "";
            ?>
                <ul class="menu">
                    <li>
                        <a class="<?php echo !isset($_GET['cid']) && $page === 'index.php' ? 'active' : ''; ?>" href="<?php echo $hostname; ?>">
                            <i class="fa-solid fa-house" aria-hidden="true"></i> Home
                        </a>
                    </li>
                    <?php
                    while ($row = mysqli_fetch_assoc($result)) {
                        $active = (isset($_GET['cid']) && (int) $row['id'] === (int) $id) ? "active" : "";
                        echo "<li><a class='{$active}' href='category.php?cid={$row['id']}'>"
                            . "<i class='fa-solid fa-folder-open'></i> "
                            . htmlspecialchars($row['category_name'])
                            . "</a></li>";
                    }
                    ?>
                    <li>
                        <a href="admin/">
                            <i class="fa-solid fa-gauge-high" aria-hidden="true"></i> Admin
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- /Menu Bar -->
