<?php
    include "config.php";
    session_start();

    if(isset($_SESSION["username"])){
        header("Location: {$hostname}/admin/post.php");
    }

?>

<!doctype html>
<html>
   <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>ADMIN | Login</title>
        <link rel="stylesheet" href="../css/bootstrap.min.css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet" href="../css/style.css">
    </head>

    <body>
        <div id="wrapper-admin" class="body-content">
            <div class="container">
                <div class="row">
                    <div class="col-md-offset-4 col-md-4">
                        <img class="logo" src="images/news.jpg" alt="News Site">
                        <h3 class="heading"><i class="fa-solid fa-lock" aria-hidden="true"></i> Admin Login</h3>
                        <!-- Form Start -->
                        <form  action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" method ="POST">
                            <div class="form-group">
                                <label><i class="fa-solid fa-user" aria-hidden="true"></i> Username</label>
                                <input type="text" name="username" class="form-control" placeholder="admin" required>
                            </div>
                            <div class="form-group">
                                <label><i class="fa-solid fa-key" aria-hidden="true"></i> Password</label>
                                <input type="password" name="password" class="form-control" placeholder="••••••" required>
                            </div>
                            <input type="submit" name="login" class="btn btn-primary" value="Login" />
                        </form>
                        <!-- /Form  End -->
                        <?php

                        if(isset($_POST['login'])){
                            include "config.php";
                            $username = mysqli_real_escape_string($conn, $_POST['username']);
                            $password = md5($_POST['password']);

                            $sql = "SELECT id, username, role FROM users WHERE username = '{$username}' AND password = '{$password}'";
                            $result = mysqli_query($conn, $sql) or die("Query Failed");

                            if(mysqli_num_rows($result) > 0){
                                while($row = mysqli_fetch_assoc($result)){
                                    $_SESSION["username"] = $row['username'];
                                    $_SESSION["id"] = $row['id'];
                                    $_SESSION["role"] = $row['role'];
                                    header("Location: {$hostname}/admin/post.php");
                                    exit;
                                }
                            } else {
                                echo '<div class="alert alert-danger">Username and password do not match.</div>';
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
