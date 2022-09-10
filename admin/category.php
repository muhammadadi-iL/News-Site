<?php include "header.php"; 
      include "config.php";
?>
<div id="admin-content">
    <div class="container">
        <div class="row">
            <div class="col-md-10">
                <h1 class="admin-heading">All Categories</h1>
            </div>
            <div class="col-md-2">
                <a class="add-new" href="add-category.php">add category</a>
            </div>
            <div class="col-md-12">
                <table class="content-table">
                <?php include "config.php";
                 
                 $limit = 10;
                 
                 if(isset($_GET ['page'])){
                    $page = $_GET ['page'];
                 }
                 else{
                    $page = 1;
                 }
                 $offset = ($page - 1) * $limit; 
                 $sql = "SELECT * FROM category ORDER BY id DESC LIMIT {$offset}, {$limit}";
                 $result = mysqli_query($conn ,$sql) or die ("query failed.");
                 if(mysqli_num_rows($result) > 0){
                  ?>
                    <thead>
                        <th>S.No.</th>
                        <th>Category Name</th>
                        <th>No. of Posts</th>
                        <th>Edit</th>
                        <th>Delete</th>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($result)){ ?>
                            <tr>
                              <td class='id'><?php echo $row['id']; ?></td>
                              <td><?php echo $row['category_name']; ?></td>
                              <td><?php echo $row['post']; ?></td>
                             <td class='edit'><a href='update-category.php?id=<?php echo $row["id"];?>'><i class='fa fa-edit'></i></a></td>
                            <td class='delete'><a href='delete-category.php?id=<?php echo $row["id"];?>'><i class='fa fa-trash-o'></i></a></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php
                 }

                 $sql1 = "SELECT * FROM category";
                 $result1 = mysqli_query($conn,$sql1) or die ('Query Failed.');

                 if(mysqli_num_rows($result1) > 0){
                    $total_record = mysqli_num_rows($result1);
                    
                    $total_page = ceil($total_record / $limit);

                    echo"<ul class='pagination admin-pagination'>";

                    if($page > 1){
                        echo '<li><a href="category.php?page='.($page - 1).'"><i class="fa fa-angle-double-left" aria-hidden="true"></i></a></li>';
                    }

                    for($i = 1; $i <= $total_page; $i++){
                        if($i == $page){
                            $active = "active";
                        }
                        else{
                            $active = null;
                        }
                        echo'<li class="'.$active.'"><a href="category.php?page='.$i.'">'.$i.'</a></li>';
                    }
                    if($total_page > $page){
                        echo '<li><a href="category.php?page='.($page + 1).'"><i class="fa fa-angle-double-right" aria-hidden="true"></i></i></a></li>';
                    }
                    
                    echo "</ul>";
                 }
                  ?>
            </div>
        </div>
    </div>
</div>
<?php include "footer.php"; ?>
