<?php
include "header.php";
include "config.php";
require_admin_role($hostname);

$error = '';
if (isset($_POST['save'])) {
    $first_name = mysqli_real_escape_string($conn, trim($_POST['first_name'] ?? ''));
    $last_name = mysqli_real_escape_string($conn, trim($_POST['last_name'] ?? ''));
    $username = mysqli_real_escape_string($conn, trim($_POST['username'] ?? ''));
    $password = mysqli_real_escape_string($conn, md5($_POST['password'] ?? ''));
    $role = (int) ($_POST['role'] ?? 0);

    if ($first_name === '' || $last_name === '' || $username === '' || empty($_POST['password'])) {
        $error = 'All fields are required.';
    } else {
        $sql = "SELECT id FROM users WHERE username = '{$username}'";
        $result = mysqli_query($conn, $sql) or die("query failed.");
        if (mysqli_num_rows($result) > 0) {
            $error = 'Username already exists.';
        } else {
            $insert = "INSERT INTO users (first_name, last_name, username, password, role)
                       VALUES ('{$first_name}', '{$last_name}', '{$username}', '{$password}', {$role})";
            if (mysqli_query($conn, $insert)) {
                header("Location: {$hostname}/admin/users.php");
                exit;
            }
            $error = 'Could not create user.';
        }
    }
}
?>
  <div id="admin-content">
      <div class="container">
          <div class="row">
              <div class="col-xs-12">
                  <h1 class="admin-heading"><i class="fa-solid fa-user-plus"></i> Add User</h1>
              </div>
              <div class="col-xs-12 col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3">
                  <?php if ($error) { echo "<div class='alert alert-danger'>" . e($error) . "</div>"; } ?>
                  <form class="admin-form" action="<?php echo e($_SERVER['PHP_SELF']); ?>" method="POST" autocomplete="off">
                      <div class="form-group">
                          <label>First Name</label>
                          <input type="text" name="first_name" class="form-control" placeholder="First Name" required>
                      </div>
                      <div class="form-group">
                          <label>Last Name</label>
                          <input type="text" name="last_name" class="form-control" placeholder="Last Name" required>
                      </div>
                      <div class="form-group">
                          <label>Username</label>
                          <input type="text" name="username" class="form-control" placeholder="Username" required>
                      </div>
                      <div class="form-group">
                          <label>Password</label>
                          <input type="password" name="password" class="form-control" placeholder="Password" required>
                      </div>
                      <div class="form-group">
                          <label>User Role</label>
                          <select class="form-control" name="role">
                              <option value="0">Editor</option>
                              <option value="1">Admin</option>
                          </select>
                      </div>
                      <div class="form-actions">
                          <a href="users.php" class="btn btn-default">Cancel</a>
                          <button type="submit" name="save" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Save</button>
                      </div>
                  </form>
               </div>
           </div>
       </div>
   </div>
<?php include "footer.php"; ?>
