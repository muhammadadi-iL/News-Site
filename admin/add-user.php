<?php include "header.php";
include 'config.php';

if($_SESSION["role"] == '0' ){
    header("Location: {$hostname}/admin/post.php");
}

if(isset($_POST['save'])){
    
    $first_name = mysqli_real_escape_string($conn,$_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn,$_POST['last_name']);
    $username = mysqli_real_escape_string($conn,$_POST['username']);
    $password = mysqli_real_escape_string($conn,md5($_POST['password']));
    $role = mysqli_real_escape_string($conn,$_POST['role']);

    $sql = "SELECT username FROM users WHERE username = '$username'";
    $result = mysqli_query($conn ,$sql) or die ("query failed.");

    if(mysqli_num_rows($result) > 0){
        echo "<p style= 'color: red; text-align: center;'>Username Already Exist.</p>";
    }
    else{
    $insertquery = "INSERT INTO users (`first_name`, `last_name`, `username`, `password`, `role`) VALUES ('$first_name', '$last_name', '$username', '$password', '$role')";

    if(mysqli_query($conn, $insertquery)){
        header("Location: {$hostname}/admin/users.php");
    }
  }
}



?>
  <div id="admin-content">
      <div class="container">
          <div class="row">
              <div class="col-md-12">
                  <h1 class="admin-heading">Add User</h1>
              </div>
              <div class="col-md-offset-3 col-md-6">
                  <!-- Form Start -->
                  <form  action="<?php $_SERVER['PHP_SELF']; ?>" method ="POST" autocomplete="off">
                      <div class="form-group">
                          <label>First Name</label>
                          <input type="text" name="first_name" class="form-control" placeholder="First Name" required>
                      </div>
                          <div class="form-group">
                          <label>Last Name</label>
                          <input type="text" name="last_name" class="form-control" placeholder="Last Name" required>
                      </div>
                      <div class="form-group">
                          <label>User Name</label>
                          <input type="text" name="username" class="form-control" placeholder="Username" required>
                      </div>

                      <div class="form-group">
                          <label>Password</label>
                          <input type="password" name="password" class="form-control" placeholder="Password" required>
                      </div>
                      <div class="form-group">
                          <label>User Role</label>
                          <select class="form-control" name="role" >
                              <option value="0">Normal User</option>
                              <option value="1">Admin</option>
                          </select>
                      </div>
                      <input type="submit" name="save" class="btn btn-primary" value="Save" required />
                  </form>
                   <!-- Form End-->
               </div>
           </div>
       </div>
   </div>
<?php include "footer.php"; ?>
