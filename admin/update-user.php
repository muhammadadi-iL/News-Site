<?php
include "header.php";
include "config.php";
require_admin_role($hostname);

$error = '';
if (isset($_POST['submit'])) {
    $id = (int) ($_POST['id'] ?? 0);
    $first_name = mysqli_real_escape_string($conn, trim($_POST['first_name'] ?? ''));
    $last_name = mysqli_real_escape_string($conn, trim($_POST['last_name'] ?? ''));
    $username = mysqli_real_escape_string($conn, trim($_POST['username'] ?? ''));
    $role = (int) ($_POST['role'] ?? 0);

    $sql = "UPDATE users SET first_name = '{$first_name}', last_name = '{$last_name}', username = '{$username}', role = '{$role}' WHERE id = {$id}";
    if (mysqli_query($conn, $sql)) {
        header("Location: {$hostname}/admin/users.php");
        exit;
    }
    $error = 'Update failed.';
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : (int) ($_POST['id'] ?? 0);
$result = mysqli_query($conn, "SELECT * FROM users WHERE id = {$id}") or die("query failed.");
$row = mysqli_fetch_assoc($result);
if (!$row) {
    echo "<div class='container'><p class='empty-state'>User not found.</p></div>";
    include "footer.php";
    exit;
}
?>
  <div id="admin-content">
      <div class="container">
          <div class="row">
              <div class="col-xs-12">
                  <h1 class="admin-heading"><i class="fa-solid fa-user-pen"></i> Modify User Details</h1>
              </div>
              <div class="col-xs-12 col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3">
                  <?php if ($error) { echo "<div class='alert alert-danger'>" . e($error) . "</div>"; } ?>
                  <form class="admin-form" action="<?php echo e($_SERVER['PHP_SELF']); ?>?id=<?php echo (int) $row['id']; ?>" method="POST">
                      <input type="hidden" name="id" value="<?php echo (int) $row['id']; ?>">
                      <div class="form-group">
                          <label>First Name</label>
                          <input type="text" name="first_name" class="form-control" value="<?php echo e($row['first_name']); ?>" required>
                      </div>
                      <div class="form-group">
                          <label>Last Name</label>
                          <input type="text" name="last_name" class="form-control" value="<?php echo e($row['last_name']); ?>" required>
                      </div>
                      <div class="form-group">
                          <label>Username</label>
                          <input type="text" name="username" class="form-control" value="<?php echo e($row['username']); ?>" required>
                      </div>
                      <div class="form-group">
                          <label>User Role</label>
                          <select class="form-control" name="role">
                          <?php if ((int) $row['role'] === 1) { ?>
                            <option value="0">Editor</option>
                            <option value="1" selected>Admin</option>
                          <?php } else { ?>
                            <option value="0" selected>Editor</option>
                            <option value="1">Admin</option>
                          <?php } ?>
                          </select>
                      </div>
                      <div class="form-actions">
                          <a href="users.php" class="btn btn-default">Cancel</a>
                          <button type="submit" name="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Update</button>
                      </div>
                  </form>
              </div>
          </div>
      </div>
  </div>
<?php include "footer.php"; ?>
