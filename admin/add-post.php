<?php include "header.php";
include 'config.php';

// if($_SESSION["role"] == '0' ){
//     header("Location: {$hostname}/admin/post.php");
// }

if(isset($_POST['save'])){
    
    $post_title = mysqli_real_escape_string($conn,$_POST['post_title']);
    $post_keywords = mysqli_real_escape_string($conn,$_POST['post_keywords']);
    $post_content = mysqli_real_escape_string($conn,$_POST['post_content']);
    $post_date = mysqli_real_escape_string($conn,md5($_POST['post_date']));
    $post_author = mysqli_real_escape_string($conn,$_POST['post_author']);
    $post_image = mysqli_real_escape_string($conn, $_POST["post_image"]);

    $sql = "SELECT post_title FROM posts WHERE post_title = '$post_title'";
    $result = mysqli_query($conn ,$sql) or die ("query failed.");

    if(mysqli_num_rows($result) > 0){
        echo "<p style= 'color: red; text-align: center;'>Username Already Exist.</p>";
    }
    else{
    $insertquery = "INSERT INTO post (`post_title`, `post_keywords`, `post_content`, `post_date`, `post_author`, `post_image`) VALUES ('$post_title', '$post_keywords', '$post_content', '$post_date', '$post_author', '$post_image')";

    if(mysqli_query($conn, $insertquery)){
        header("Location: {$hostname}/admin/post.php");
    }
  }
}
?>
  <div id="admin-content">
      <div class="container">
         <div class="row">
             <div class="col-md-12">
                 <h1 class="admin-heading">Add New Post</h1>
             </div>
              <div class="col-md-offset-3 col-md-6">
                  <!-- Form -->
                  <form  action="save-post.php" method="POST" enctype="multipart/form-data">
                      <div class="form-group">
                          <label for="post_title">Title</label>
                          <input type="text" name="post_title" class="form-control" autocomplete="off" required>
                      </div>
                      <div class="form-group">
                          <label for="exampleInputPassword1"> Description</label>
                          <textarea name="post_keywords" class="form-control"  rows="5"  required></textarea>
                      </div>

                      <div class="form-group">
                          <label for="exampleInputPassword1">Category</label>
                          <select name="post_content" class="form-control">
                              <option disabled> Select Category</option>
                              <?php
                              include "config.php";
                                $sql = "SELECT * FROM category";
                                $result = mysqli_query($conn, $sql) or die ("query failed.");
                            
                                if(mysqli_num_rows($result) > 1){
                                    while($row = mysqli_fetch_assoc($result)){
                                        echo "<option value='{$row['id']}'>{$row['category_name']}</option>";
                                    }
                                }
                              ?>
                          </select>
                      </div>

                      <div class="form-group">
                          <label for="exampleInputPassword1">Post image</label>
                          <input type="file" name="filetoupload" required>
                      </div>
                      <input type="submit" name="submit" class="btn btn-primary" value="Save" required />
                  </form>
                  <!--/Form -->
              </div>
          </div>
      </div>
  </div>
<?php include "footer.php"; ?>
