<div id="sidebar" class="col-md-4">
    <div class="search-box-container">
        <h4><i class="fa-solid fa-magnifying-glass"></i> Search</h4>
        <form class="search-post" action="search.php" method="GET">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search news..." value="<?php echo isset($_GET['search']) ? e($_GET['search']) : ''; ?>">
                <span class="input-group-btn">
                    <button type="submit" class="btn btn-danger">
                        <i class="fa-solid fa-magnifying-glass"></i> Search
                    </button>
                </span>
            </div>
        </form>
    </div>

    <div class="recent-post-container">
        <h4><i class="fa-solid fa-clock-rotate-left"></i> Recent Posts</h4>
        <?php
        include "config.php";
        $limit = 4;
        $sql = "SELECT posts.id, posts.post_title, posts.post_date, posts.post_image, category.category_name, posts.post_content FROM posts
                LEFT JOIN category ON posts.post_content = category.id
                ORDER BY posts.id DESC LIMIT {$limit}";
        $result = mysqli_query($conn, $sql) or die("query failed : Recent Post.");
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $img = post_image_src($row['post_image']);
                ?>
        <div class="recent-post">
            <a class="post-img" href="single.php?id=<?php echo (int) $row['id']; ?>">
                <img src="<?php echo e($img); ?>" alt=""/>
            </a>
            <div class="post-content">
                <h5><a href="single.php?id=<?php echo (int) $row['id']; ?>"><?php echo e($row['post_title']); ?></a></h5>
                <span>
                    <i class="fa-solid fa-tags"></i>
                    <a href="category.php?cid=<?php echo (int) $row['post_content']; ?>"><?php echo e($row['category_name']); ?></a>
                </span>
                <span>
                    <i class="fa-regular fa-calendar-days"></i>
                    <?php echo e($row['post_date']); ?>
                </span>
                <a class="read-more pull-right" href="single.php?id=<?php echo (int) $row['id']; ?>">
                    read more <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>
                <?php
            }
        }
        ?>
    </div>
</div>
