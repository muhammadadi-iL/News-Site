<?php
    include "config.php";
    require_admin_session($hostname);

    $current = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Admin Panel | Pulse Markets</title>
        <link rel="icon" href="../images/favicon.ico?v=pulse2" sizes="any">
        <link rel="icon" href="../images/logo.svg?v=pulse2" type="image/svg+xml">
        <link rel="icon" href="../images/favicon-32.png?v=pulse2" type="image/png" sizes="32x32">
        <link rel="icon" href="../images/favicon-16.png?v=pulse2" type="image/png" sizes="16x16">
        <link rel="apple-touch-icon" href="../images/apple-touch-icon.png?v=pulse2">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="../css/bootstrap.min.css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet" href="../css/style.css">
    </head>
    <body class="admin-body">
        <div id="header-admin">
            <div class="container">
                <div class="row admin-header-row">
                    <div class="col-xs-12 col-sm-6">
                        <a class="admin-brand" href="post.php">
                            <img class="logo" src="../images/logo.svg" alt="Pulse Markets">
                            <span>Pulse Markets <small>Admin</small></span>
                        </a>
                    </div>
                    <div class="col-xs-12 col-sm-6 text-right-sm">
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
                    <div class="col-xs-12">
                       <button type="button" class="nav-toggle admin-nav-toggle" id="adminNavToggle" aria-controls="adminMenu" aria-expanded="false" aria-label="Toggle admin menu">
                           <i class="fa-solid fa-bars" aria-hidden="true"></i>
                           <span>Admin Menu</span>
                       </button>
                       <ul class="admin-menu" id="adminMenu">
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
