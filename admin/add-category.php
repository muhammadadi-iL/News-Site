<?php
include "header.php";
include "config.php";
require_admin_role($hostname);

$error = '';
if (isset($_POST['save'])) {
    $category_name = mysqli_real_escape_string($conn, trim($_POST['category_name'] ?? ''));
    if ($category_name === '') {
        $error = 'Category name is required.';
    } else {
        $sql = "SELECT id FROM category WHERE category_name = '{$category_name}'";
        $result = mysqli_query($conn, $sql) or die("query failed.");
        if (mysqli_num_rows($result) > 0) {
            $error = 'Category already exists.';
        } else {
            if (mysqli_query($conn, "INSERT INTO category (category_name, post) VALUES ('{$category_name}', 0)")) {
                header("Location: {$hostname}/admin/category.php");
                exit;
            }
            $error = 'Could not save category.';
        }
    }
}
?>
  <div id="admin-content">
      <div class="container">
          <div class="row">
              <div class="col-md-12">
                  <h1 class="admin-heading"><i class="fa-solid fa-plus"></i> Add New Category</h1>
              </div>
              <div class="col-md-offset-3 col-md-6">
                  <?php if ($error) { echo "<div class='alert alert-danger'>" . e($error) . "</div>"; } ?>
                  <form class="admin-form" action="<?php echo e($_SERVER['PHP_SELF']); ?>" method="POST" autocomplete="off">
                      <div class="form-group">
                          <label><i class="fa-solid fa-tag"></i> Category Name</label>
                          <input type="text" name="category_name" class="form-control" placeholder="e.g. Health" required>
                      </div>
                      <div class="form-actions">
                          <a href="category.php" class="btn btn-default">Cancel</a>
                          <button type="submit" name="save" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Save</button>
                      </div>
                  </form>
              </div>
          </div>
      </div>
  </div>
<?php include "footer.php"; ?>
