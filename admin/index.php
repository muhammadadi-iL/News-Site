<?php
include "config.php";
session_start();

if (isset($_SESSION["username"])) {
    header("Location: {$hostname}/admin/post.php");
    exit;
}

$error = '';
if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, trim($_POST['username'] ?? ''));
    $password = md5($_POST['password'] ?? '');
    $sql = "SELECT id, username, role FROM users WHERE username = '{$username}' AND password = '{$password}'";
    $result = mysqli_query($conn, $sql) or die("Query Failed");

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION["username"] = $row['username'];
        $_SESSION["id"] = $row['id'];
        $_SESSION["role"] = $row['role'];
        header("Location: {$hostname}/admin/post.php");
        exit;
    }
    $error = 'Username and password do not match.';
}
?>
<!doctype html>
<html lang="en">
   <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Admin Login | News Site</title>
        <link rel="icon" href="../images/favicon.ico" sizes="any">
        <link rel="icon" href="../images/favicon-32.png" type="image/png" sizes="32x32">
        <link rel="icon" href="../images/logo.png" type="image/png" sizes="256x256">
        <link rel="apple-touch-icon" href="../images/apple-touch-icon.png">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="../css/bootstrap.min.css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet" href="../css/style.css">
    </head>
    <body class="admin-body login-page">
        <div id="wrapper-admin" class="body-content">
            <div class="container">
                <div class="row">
                    <div class="col-xs-12 col-sm-8 col-sm-offset-2 col-md-4 col-md-offset-4">
                        <div class="login-card">
                        <img class="logo" src="../images/logo.png" alt="News Site">
                        <h3 class="heading"><i class="fa-solid fa-shield-halved"></i> Admin Login</h3>
                        <?php if ($error) { echo '<div class="alert alert-danger">' . e($error) . '</div>'; } ?>
                        <form class="login-form" action="<?php echo e($_SERVER['PHP_SELF']); ?>" method="POST">
                            <div class="form-group">
                                <label for="username"><i class="fa-solid fa-user"></i> Username</label>
                                <input type="text" id="username" name="username" class="form-control" placeholder="admin" required>
                            </div>
                            <div class="form-group">
                                <label for="password"><i class="fa-solid fa-key"></i> Password</label>
                                <input type="password" id="password" name="password" class="form-control" placeholder="••••••" required>
                            </div>
                            <button type="submit" name="login" class="btn btn-accent btn-block btn-lg">
                                <i class="fa-solid fa-right-to-bracket"></i> Login
                            </button>
                        </form>
                        <p class="help-text login-hint">
                            Default: <strong>admin</strong> / <strong>admin</strong>
                        </p>
                        <p class="login-back">
                            <a class="btn btn-soft btn-block" href="<?php echo $hostname; ?>"><i class="fa-solid fa-arrow-left"></i> Back to site</a>
                        </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
