<?php include "header.php"; ?>
<?php include "config.php"; ?>

<?php 

if($_SESSION["role"] == '0' ){
    header("Location: {$hostname}/admin/post.php");
}

if(isset($_POST['submit'])){

$id = mysqli_real_escape_string($conn,$_POST['id']);
$category_name = mysqli_real_escape_string($conn,$_POST['category_name']);

$sql = "UPDATE category SET category_name = '{$category_name}', WHERE id = '{$id}'";
 
if(mysqli_query($conn,$sql)){
    header ("Location: {$hostname}/admin/category.php");
}
}

?>

  <div id="admin-content">
      <div class="container">
          <div class="row">
              <div class="col-md-12">
                  <h1 class="adin-heading"> Update Category</h1>
              </div>
              <div class="col-md-offset-3 col-md-6">

              <?php
              
              include "config.php";
                
                $id = $_GET["id"];
                $sql = "SELECT category.category_name FROM category WHERE category.id = {$id}";
                // print_r("abr");
                $result = mysqli_query($conn ,$sql) or die ("query failed.");
                // print_r("abr");
                // print_r($result);
                // exit;
                if(mysqli_num_rows($result) > 0){
                while($row = mysqli_fetch_assoc($result)){
              ?>

                  <form action="<?php $_SERVER['PHP_SELF']; ?>" method ="POST">
                  <div class="form-group">
                <input type="hidden" name="id"  class="form-control" value="<?php echo  $row['id'] ?>" placeholder="">
            </div>
                      <div class="form-group">
                          <label>Category Name</label>
                          <select name="category_name" class="form-control">
                              <option disabled> Select Category</option>
                              <?php
                              include "config.php";
                                $sql1 = "SELECT * FROM category";
                                $result1 = mysqli_query($conn, $sql1) or die ("QUERY failed.");
                            
                                if(mysqli_num_rows($result1) > 0){
                                    while($row1 = mysqli_fetch_assoc($result1)){
                                        if($row['category_name'] == $row1['id']){
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
                      <input type="submit" name="submit" class="btn btn-primary" value="Update" required />
                  </form>
                  <?php
                  
                }
            } 
                  ?>
                </div>
              </div>
            </div>
          </div>
<?php include "footer.php"; ?>
