<?php include 'header.php'; ?>
    <div id="main-content">
      <div class="container">
        <div class="row">
            <div class="col-md-8">
                <!-- post-container -->
                <div class="post-container">

                <?php
                    include "config.php";

                    if(isset($_GET['cid'])){
                        $id = $_GET['cid'];
                    }

                    $sql1 = "SELECT * FROM category WHERE id = {$id}";
                    $result1 = mysqli_query($conn,$sql1) or die ('Query Failed.');
                    $row1 = mysqli_fetch_assoc($result1);
                ?>
                  <h2 class="page-heading"><?php echo $row1['category_name']; ?></h2>
                  <?php

                        $limit = 5;
                 
                        if(isset($_GET ['page'])){
                           $page = $_GET ['page'];
                        }
                        else{
                           $page = 1;
                        }
                        $offset = ($page - 1) * $limit;

                        $sql = "SELECT posts.id, posts.post_title, posts.post_keywords, posts.post_date, posts.post_author, category.category_name, users.username, posts.post_content FROM posts  
                        LEFT JOIN category ON posts.post_content = category.id
                        LEFT JOIN users ON posts.post_author = users.id
                        WHERE posts.post_content = {$id}
                        ORDER BY posts.id DESC LIMIT {$offset}, {$limit}";

                        $result = mysqli_query($conn ,$sql) or die ("query failed.");
                        if(mysqli_num_rows($result) > 0){
                            while($row = mysqli_fetch_assoc($result)){

                        ?>

                        <div class="post-content">
                            <div class="row">
                                <div class="col-md-4">
                                    <a class="post-img" href="single.php?id=<?php echo $row['id']; ?>"><img src="images/post-format.jpg" alt=""/></a>
                                </div>
                                <div class="col-md-8">
                                    <div class="inner-content clearfix">
                                        <h3><a href='single.php?id=<?php echo $row['id']; ?>'><?php echo $row['post_title']; ?></a></h3>
                                        <div class="post-information">
                                            <span>
                                                <i class="fa fa-tags" aria-hidden="true"></i>
                                                <a href='category.php'><?php echo $row['category_name']; ?></a>
                                            </span>
                                            <span>
                                                <i class="fa fa-user" aria-hidden="true"></i>
                                                <a href='author.php?aid=<?php echo $row['post_author']; ?>'><?php echo $row['username']; ?></a>
                                            </span>
                                            <span>
                                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                                <?php echo $row['post_date']; ?>
                                            </span>
                                        </div>
                                        <p class="description">
                                        <?php echo substr($row['post_keywords'],0,130) . "..."; ?>
                                        </p>
                                        <a class='read-more pull-right' href='single.php?id=<?php echo $row['id']; ?>'>read more</a>
                                    </div>
                                </div>
                            </div>
                            </div>
                            
                        <?php
                            }
                        }
                        else{
                            echo "No Record Found";
                        }
                        
                        
       
                        if(mysqli_num_rows($result1) > 0){
                           $total_record = $row1['post'];
                           
                           $total_page = ceil($total_record / $limit);
       
                           echo"<ul class='pagination admin-pagination'>";
       
                           if($page > 1){
                               echo '<li><a href="index.php?cid='.$id.'&page='.($page - 1).'"><i class="fa fa-angle-double-left" aria-hidden="true"></i></a></li>';
                           }
       
                           for($i = 1; $i <= $total_page; $i++){
                               if($i == $page){
                                   $active = "active";
                               }
                               else{
                                   $active = null;
                               }
                               echo'<li class="'.$active.'"><a href="index.php?cid='.$id.'&page='.$i.'">'.$i.'</a></li>';
                           }
                           if($total_page > $page){
                               echo '<li><a href="index.php?cid='.$id.'&page='.($page + 1).'"><i class="fa fa-angle-double-right" aria-hidden="true"></i></i></a></li>';
                           }
                           
                           echo "</ul>";
                        }
                        ?>
                </div><!-- /post-container -->
            </div>
            <?php include 'sidebar.php'; ?>
        </div>
      </div>
    </div>
<?php include 'footer.php'; ?>
