<div id="sidebar" class="col-md-4">
    <!-- search box -->
    <div class="search-box-container">
        <h4>Search</h4>
        <form class="search-post" action="search.php" method ="GET">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search .....">
                <span class="input-group-btn">
                    <button type="submit" class="btn btn-danger">Search</button>
                </span>
            </div>
        </form>
    </div>
    <!-- /search box -->
    <!-- recent posts box -->
    <div class="recent-post-container">
        <h4>Recent Posts</h4>
        <?php
                        include "config.php";

                        $limit = 3;
                 
                        $sql = "SELECT posts.id, posts.post_title, posts.post_date, category.category_name, posts.post_content FROM posts  
                        LEFT JOIN category ON posts.post_content = category.id
                        ORDER BY posts.id DESC LIMIT {$limit}";

                        $result = mysqli_query($conn ,$sql) or die ("query failed : Recent Post.");
                        if(mysqli_num_rows($result) > 0){
                            while($row = mysqli_fetch_assoc($result)){

        ?>
        <div class="recent-post">
            <a class="post-img" href=href="single.php?id=<?php echo $row['id']; ?>">
                <img src="Assets/pexels.jpeg" alt=""/>
            </a>
            <div class="post-content">
                <h5><a href='single.php?id=<?php echo $row['id']; ?>'><?php echo $row['post_title']; ?></a></h5>
                <span>
                    <i class="fa fa-tags" aria-hidden="true"></i>
                    <a href='category.php?cid=<?php echo $row['post_content']; ?>'><?php echo $row['category_name']; ?></a>
                </span>
                <span>
                    <i class="fa fa-calendar" aria-hidden="true"></i>
                    <?php echo $row['post_date']; ?>
                </span>
                <a class='read-more pull-right' href='single.php?id=<?php echo $row['id']; ?>'>read more</a>
            </div>
        </div>
        <?php
                            }
                        }
        ?>
    </div>
    <!-- /recent posts box -->
</div>
