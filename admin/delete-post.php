

<?php 
include "config.php"; 

$id = $_GET['id'];
// $id = $_GET['id'];

$sql2 = "SELECT * FROM posts WHERE id = {$id}";
$result = mysqli_query($conn, $sql2) or die("Query Failed : Select");
$row = mysqli_fetch_assoc($result);

// echo "<pre>";
// print_r($row);
// echo "</pre>";
// die();
unlink("upload/".$row['post_image']);

$sql = "DELETE FROM posts WHERE id = {$id}";
// print_r($sql);
$sql1 = "UPDATE category SET post = post - 1 WHERE id = {$id}";

if(mysqli_multi_query($conn, $sql) or ($sql1)){
    header("Location: {$hostname}/admin/post.php");
}
else{
    echo "Query Failed";
}

?>