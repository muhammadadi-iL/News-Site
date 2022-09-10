<?php 
include "config.php"; 

if($_SESSION["role"] == '0' ){
    header("Location: {$hostname}/admin/post.php");
}

$id = $_GET['id'];

$sql = "DELETE FROM users WHERE id = {$id}";
if(mysqli_query($conn,$sql)){
    header ("Location: {$hostname}/admin/users.php");
}
else{
    echo "<p style = 'color: red; margin: 10px 0;'>Can\'t Delete User Record</p>";
}
mysqli_close($conn);

?>