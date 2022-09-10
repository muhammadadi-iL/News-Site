<?php include "header.php"; ?>
<div id="admin-content">
  <div class="container">
  <div class="row">
    <div class="col-md-12">
        <h1 class="admin-heading">Update Post</h1>
    </div>
    <div class="col-md-offset-3 col-md-6">
        <?php
            include "config.php";

            $id = $_GET['id']; 
            $sql = "SELECT posts.id, posts.post_title, posts.post_keywords, posts.post_image, category.category_name, posts.post_content FROM posts  
                    LEFT JOIN category ON posts.post_content = category.id
                    LEFT JOIN users ON posts.post_author = users.id
                    WHERE posts.id = {$id}";
                    
            $result = mysqli_query($conn ,$sql) or die ("query failed.");
            if(mysqli_num_rows($result) > 0){
                while($row = mysqli_fetch_assoc($result)){        
        ?>
        <!-- Form for show edit-->
        <form action="save-update-post.php" method="POST" enctype="multipart/form-data" autocomplete="off">
            <div class="form-group">
                <input type="hidden" name="id"  class="form-control" value="<?php echo  $row['id'] ?>" placeholder="">
            </div>
            <div class="form-group">
                <label for="exampleInputTile">Title</label>
                <input type="text" name="post_title"  class="form-control" id="exampleInputUsername" value="<?php echo $row['post_title'] ?>">
            </div>
            <div class="form-group">
                <label for="exampleInputPassword1"> Description</label>
                <textarea name="post_keywords" class="form-control"  required rows="5">
                <?php echo $row['post_keywords'] ?>
                </textarea>
            </div>
            <div class="form-group">
                <label for="exampleInputCategory">Category</label>
                <select name="post_content" class="form-control">
                              <option disabled> Select Category</option>
                              <?php
                              include "config.php";
                                $sql1 = "SELECT * FROM category";
                                $result1 = mysqli_query($conn, $sql1) or die ("query failed.");
                            
                                if(mysqli_num_rows($result1) > 0){
                                    while($row1 = mysqli_fetch_assoc($result1)){
                                        if($row['post_content'] == $row1['id']){
                                            $selected = "selected";
                                        }else{
                                            $selected = "";
                                        }
                                        echo "<option {$selected} value='{$row1['id']}'>{$row1['category_name']}</option>";
                                    }
                                }
                              ?>
                          </select>
            </div>
            <div class="form-group">
                <label for="">Post image</label>
                <input type="file" name="new_image">
                <img  src="upload/<?php echo $row['post_image'] ?>" height="150px">
                <input type="hidden" name="old_image" value="<?php echo $row['post_image'] ?>">
            </div>
            <input type="submit" name="submit" class="btn btn-primary" value="Update" />
        </form>
        <!-- Form End -->
        <?php
                }
            }
            else{
                echo "Result Not Found.";
            }
        ?>
      </div>
    </div>
  </div>
</div>
<?php include "footer.php"; ?>
