<?php
    include "config.php";
    session_start();

    if (!isset($_SESSION["username"])) {
        header("Location: {$hostname}/admin/");
        exit;
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>ADMIN Panel</title>
        <link rel="icon" href="../images/news.jpg" type="image/jpeg">
        <link rel="stylesheet" href="../css/bootstrap.min.css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet" href="../css/style.css">
    </head>
    <body>
        <div id="header-admin">
            <div class="container">
                <div class="row">
                    <div class="col-md-2">
                        <a href="post.php"><img class="logo" src="images/news.jpg" alt="News Site Admin"></a>
                    </div>
                    <div class="col-md-offset-6 col-md-4 text-right">
                        <a href="logout.php" class="admin-logout">
                            <i class="fa-solid fa-user" aria-hidden="true"></i>
                            Hello <?php echo htmlspecialchars($_SESSION["username"]); ?>,
                            <i class="fa-solid fa-right-from-bracket" aria-hidden="true"></i> logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div id="admin-menubar">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                       <ul class="admin-menu">
                            <li>
                                <a href="<?php echo $hostname; ?>">
                                    <i class="fa-solid fa-globe" aria-hidden="true"></i> News Site
                                </a>
                            </li>
                            <li>
                                <a href="post.php">
                                    <i class="fa-solid fa-file-lines" aria-hidden="true"></i> Post
                                </a>
                            </li>
                           <?php if ($_SESSION["role"] == '1') { ?>
                            <li>
                                <a href="category.php">
                                    <i class="fa-solid fa-folder-tree" aria-hidden="true"></i> Category
                                </a>
                            </li>
                            <li>
                                <a href="users.php">
                                    <i class="fa-solid fa-users" aria-hidden="true"></i> Users
                                </a>
                            </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
