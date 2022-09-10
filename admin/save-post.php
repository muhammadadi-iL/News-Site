<?php 
include "config.php";
// include "index.php";
// include "add-post.php";

if (isset($_POST['filetoupload'])){
    $errors = array();

    // $statement=$db->prepare("show table status like 'tbl_std_info'");
    // $statement->execute();
    // $result=$statement->fetchAll();
    // foreach($result as $row)
    // $new_id=$row[10];

    $post_title = $_FILES['filetoupload']['post_title'];
    // print_r($post_title);
    $post_keywords = $_POST['post_keywords'];
    $post_content = $_POST['post_content'];
    $post_date = date('d-m-y');
    $post_author = $_POST['post_author'];
    $post_image = $_FILES['filetoupload'] ['name'];
    $image_tmp = $_FILES['image'] ['tmp_name'];
    $extentions = array("jpeg", "png", "jpg");
    // $file_name = $_FILES['filetoupload']['name'];
    $file_ext = end(explode('.', $post_image));
    // $f1="$new_id".$file_ext;
    // $document_root = $_SERVER ['DOCUMENT_ROOT'];
    // print_r($image_tmp);
    
    if(in_array($post_image, $extentions) === false){
        $errors[] = "this ext file is not alowed. please choose jpg or png.";
    }

    if(empty($errors) == true){
        move_uploaded_file($image_tmp, "upload/".$post_image);
        // move_uploaded_file($_FILES["image"]["tmp_name"],"../upload/".$f1);
    }else{
        print_r($errors);
        die();
    }

    // if($post_title=='' or  $post_keywords=='' or $post_content=='' or $post_author==''){
    
    //     echo "<script>alert('any of the field is empty')</script>";
    
    //     exit();
    // }
    
    //     else{
    
    //         move_uploaded_file($image_tmp, "upload/$post_image");
    
    //         $insert_query = "INSERT INTIO POST (post_title,post_date,post_author,post_image,post_keywords,post_content) VALUES ('$post_title','$post_date','$post_author','$post_image', '$post_keywords', '$post_content')";
    
    //         if (mysql_query($insert_query)) {
    //             // print_r($insert_query);   
    //         echo "<center><h1>Post Published succesfully!</h1></center>";
    
    //     }
    
    // }
    
    }    
session_start();
// $post_id = mysqli_real_escape_string($conn, $_POST["post_id"]);
$post_title = mysqli_real_escape_string($conn, $_POST["post_title"]);
// print_r($post_title);
$post_keywords = mysqli_real_escape_string($conn, $_POST["post_keywords"]);
// print_r($post_keywords);
$post_content = mysqli_real_escape_string($conn, $_POST["post_content"]);
// print_r($post_content);
$post_date = date("d, M, Y");
$post_author = $_SESSION["id"];
$post_image = mysqli_real_escape_string($conn, $_POST["post_image"]);
// print_r($post_author);   
// $document_root = $_SERVER ['DOCUMENT_ROOT'];
// exit(); 
// $conn = mysqli_connect("localhost", "root", "", "record");
// $conn = new mysqli("localhost", "root", "", "myDB","3308");


$insert_query = "INSERT INTO POSTS (post_title,post_date,post_author,post_keywords,post_content,post_image) VALUES ('$post_title','$post_date','$post_author', '$post_keywords', '$post_content', '$post_image')";
// print_r($insert_query);


$sql = "UPDATE category SET post = post + 1 WHERE category_name = {$category}";
// echo $sql;
// die();
// if($conn === false){
// die("ERROR: Could not connect. " . mysqli_connect_error());
// }

if(mysqli_multi_query($conn, $insert_query)){
    header("Location:{$hostname}/admin/post.php");
}
else{
    echo "<div class='alert alert-danger'>Query Failed.</div>";
}
?>