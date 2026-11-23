<?php
include "header.php";
include "config.php";

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$sql = "SELECT posts.id, posts.post_title, posts.post_keywords, posts.post_image, posts.post_author, posts.post_content, category.category_name
        FROM posts
        LEFT JOIN category ON posts.post_content = category.id
        WHERE posts.id = {$id}";
$result = mysqli_query($conn, $sql) or die("query failed.");
$row = mysqli_fetch_assoc($result);

if (!$row) {
    echo "<div class='container'><p class='empty-state'>Post not found.</p></div>";
    include "footer.php";
    exit;
}

if ((string) $_SESSION['role'] !== '1' && (int) $row['post_author'] !== (int) $_SESSION['id']) {
    echo "<div class='container'><p class='empty-state'>Not allowed.</p></div>";
    include "footer.php";
    exit;
}
?>
<div id="admin-content">
  <div class="container">
  <div class="row">
    <div class="col-md-12">
        <h1 class="admin-heading"><i class="fa-solid fa-pen-to-square"></i> Update Post</h1>
        <p class="admin-sub">Edit story details and optionally replace the cover image.</p>
    </div>
    <div class="col-md-offset-2 col-md-8">
        <form class="admin-form" action="save-update-post.php" method="POST" enctype="multipart/form-data" autocomplete="off">
            <input type="hidden" name="id" value="<?php echo (int) $row['id']; ?>">
            <div class="form-group">
                <label for="post_title"><i class="fa-solid fa-heading"></i> Title</label>
                <input type="text" name="post_title" id="post_title" class="form-control" value="<?php echo e($row['post_title']); ?>" required>
            </div>
            <div class="form-group">
                <label for="post_keywords"><i class="fa-solid fa-align-left"></i> Description</label>
                <textarea name="post_keywords" id="post_keywords" class="form-control" required rows="6"><?php echo e(trim($row['post_keywords'])); ?></textarea>
            </div>
            <div class="form-group">
                <label for="post_content"><i class="fa-solid fa-folder-open"></i> Category</label>
                <select name="post_content" id="post_content" class="form-control" required>
                              <?php
                                $sql1 = "SELECT * FROM category ORDER BY category_name ASC";
                                $result1 = mysqli_query($conn, $sql1) or die("query failed.");
                                while ($row1 = mysqli_fetch_assoc($result1)) {
                                    $selected = ((int) $row['post_content'] === (int) $row1['id']) ? "selected" : "";
                                    echo "<option {$selected} value='" . (int) $row1['id'] . "'>" . e($row1['category_name']) . "</option>";
                                }
                              ?>
                </select>
            </div>
            <div class="form-group">
                <label for="new_image"><i class="fa-solid fa-image"></i> Post image</label>
                <div class="image-preview">
                    <img src="<?php echo e(post_image_src($row['post_image'], '../')); ?>" alt="Current cover" height="150">
                </div>
                <input type="file" name="new_image" id="new_image" accept=".jpg,.jpeg,.png,.webp,.gif">
                <input type="hidden" name="old_image" value="<?php echo e($row['post_image']); ?>">
                <p class="help-text">Leave empty to keep the current image.</p>
            </div>
            <div class="form-actions">
                <a href="post.php" class="btn btn-default">Cancel</a>
                <button type="submit" name="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Update</button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php include "footer.php"; ?>
