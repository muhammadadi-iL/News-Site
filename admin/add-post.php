<?php
include "header.php";
include "config.php";
?>
  <div id="admin-content">
      <div class="container">
         <div class="row">
             <div class="col-md-12">
                 <h1 class="admin-heading"><i class="fa-solid fa-plus"></i> Add New Post</h1>
                 <p class="admin-sub">Create a news article with title, story, category and cover image.</p>
             </div>
              <div class="col-md-offset-2 col-md-8">
                  <form class="admin-form" action="save-post.php" method="POST" enctype="multipart/form-data">
                      <div class="form-group">
                          <label for="post_title"><i class="fa-solid fa-heading"></i> Title</label>
                          <input type="text" name="post_title" id="post_title" class="form-control" autocomplete="off" required>
                      </div>
                      <div class="form-group">
                          <label for="post_keywords"><i class="fa-solid fa-align-left"></i> Description</label>
                          <textarea name="post_keywords" id="post_keywords" class="form-control" rows="6" required></textarea>
                      </div>
                      <div class="form-group">
                          <label for="post_content"><i class="fa-solid fa-folder-open"></i> Category</label>
                          <select name="post_content" id="post_content" class="form-control" required>
                              <option value="">Select Category</option>
                              <?php
                                $sql = "SELECT * FROM category ORDER BY category_name ASC";
                                $result = mysqli_query($conn, $sql) or die("query failed.");
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<option value='" . (int) $row['id'] . "'>" . e($row['category_name']) . "</option>";
                                }
                              ?>
                          </select>
                      </div>
                      <div class="form-group">
                          <label for="filetoupload"><i class="fa-solid fa-image"></i> Post image</label>
                          <input type="file" name="filetoupload" id="filetoupload" accept=".jpg,.jpeg,.png,.webp,.gif" required>
                          <p class="help-text">JPG, PNG, WEBP or GIF. Recommended landscape photo.</p>
                      </div>
                      <div class="form-actions">
                          <a href="post.php" class="btn btn-default">Cancel</a>
                          <button type="submit" name="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Save Post</button>
                      </div>
                  </form>
              </div>
          </div>
      </div>
  </div>
<?php include "footer.php"; ?>
