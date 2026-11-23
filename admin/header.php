<?php
    include "config.php";
    require_admin_session($hostname);

    $current = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Admin Panel | News Site</title>
        <link rel="icon" href="../images/logo.png" type="image/png">
        <link rel="stylesheet" href="../css/bootstrap.min.css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet" href="../css/style.css">
    </head>
    <body class="admin-body">
        <div id="header-admin">
            <div class="container">
                <div class="row vertical-center">
                    <div class="col-md-4">
                        <a class="admin-brand" href="post.php">
                            <img class="logo" src="../images/logo.png" alt="News Site">
                            <span>News Site <small>Admin</small></span>
                        </a>
                    </div>
                    <div class="col-md-8 text-right">
                        <a href="logout.php" class="admin-logout">
                            <i class="fa-solid fa-user-shield"></i>
                            <?php echo e($_SESSION["username"]); ?>
                            <i class="fa-solid fa-right-from-bracket"></i> Logout
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
                                    <i class="fa-solid fa-globe"></i> View Site
                                </a>
                            </li>
                            <li>
                                <a class="<?php echo in_array($current, ['post.php','add-post.php','update-post.php'], true) ? 'active' : ''; ?>" href="post.php">
                                    <i class="fa-solid fa-file-lines"></i> Posts
                                </a>
                            </li>
                           <?php if ((string) $_SESSION["role"] === '1') { ?>
                            <li>
                                <a class="<?php echo in_array($current, ['category.php','add-category.php','update-category.php'], true) ? 'active' : ''; ?>" href="category.php">
                                    <i class="fa-solid fa-folder-tree"></i> Categories
                                </a>
                            </li>
                            <li>
                                <a class="<?php echo in_array($current, ['users.php','add-user.php','update-user.php'], true) ? 'active' : ''; ?>" href="users.php">
                                    <i class="fa-solid fa-users"></i> Users
                                </a>
                            </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
