<?php
include "header.php";
include "config.php";
require_admin_role($hostname);

$error = '';
if (isset($_POST['submit'])) {
    $id = (int) ($_POST['id'] ?? 0);
    $category_name = mysqli_real_escape_string($conn, trim($_POST['category_name'] ?? ''));
    if ($id < 1 || $category_name === '') {
        $error = 'Invalid category data.';
    } else {
        $sql = "UPDATE category SET category_name = '{$category_name}' WHERE id = {$id}";
        if (mysqli_query($conn, $sql)) {
            header("Location: {$hostname}/admin/category.php");
            exit;
        }
        $error = 'Update failed.';
    }
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : (int) ($_POST['id'] ?? 0);
$result = mysqli_query($conn, "SELECT * FROM category WHERE id = {$id}") or die("query failed.");
$row = mysqli_fetch_assoc($result);
if (!$row) {
    echo "<div class='container'><p class='empty-state'>Category not found.</p></div>";
    include "footer.php";
    exit;
}
?>
  <div id="admin-content">
      <div class="container">
          <div class="row">
              <div class="col-xs-12">
                  <h1 class="admin-heading"><i class="fa-solid fa-pen-to-square"></i> Update Category</h1>
              </div>
              <div class="col-xs-12 col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3">
                  <?php if ($error) { echo "<div class='alert alert-danger'>" . e($error) . "</div>"; } ?>
                  <form class="admin-form" action="<?php echo e($_SERVER['PHP_SELF']); ?>?id=<?php echo (int) $row['id']; ?>" method="POST">
                      <input type="hidden" name="id" value="<?php echo (int) $row['id']; ?>">
                      <div class="form-group">
                          <label><i class="fa-solid fa-tag"></i> Category Name</label>
                          <input type="text" name="category_name" class="form-control" value="<?php echo e($row['category_name']); ?>" required>
                      </div>
                      <div class="form-actions">
                          <a href="category.php" class="btn btn-default">Cancel</a>
                          <button type="submit" name="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Update</button>
                      </div>
                  </form>
              </div>
          </div>
      </div>
  </div>
<?php include "footer.php"; ?>
