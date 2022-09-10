<?php
include "config.php";

if(empty($_FILES['new_image']['name'])){
    $post_image = $_POST['old-image'];
}
else{
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
    $post_image = $_FILES['new_image'] ['name'];
    $image_tmp = $_FILES['new_image'] ['tmp_name'];
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

    $sql = "UPDATE posts SET post_title = '{$_POST["post_title"]}', post_keywords = '{$_POST["post_keywords"]}', post_content = '{$_POST["post_content"]}', post_image = '{$post_image}' WHERE id = {$_POST["id"]}";

    $result = mysqli_query($conn, $sql);

    if($result){
        header("Location: {$hostname}/admin/post.php");
    }
    else{
        echo "Query Failed";
    }






?>