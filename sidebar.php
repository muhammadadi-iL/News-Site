<div id="sidebar" class="col-xs-12 col-sm-12 col-md-4">
    <div class="search-box-container side-card">
        <h4><i class="fa-solid fa-magnifying-glass"></i> Search</h4>
        <form class="search-post" action="search.php" method="GET">
            <div class="search-row">
                <input type="text" name="search" class="form-control" placeholder="Search news..." value="<?php echo isset($_GET['search']) ? e($_GET['search']) : ''; ?>">
                <button type="submit" class="btn btn-brand">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <span>Search</span>
                </button>
            </div>
        </form>
    </div>

    <div class="recent-post-container side-card">
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
                $icon = category_icon($row['category_name']);
                ?>
        <div class="recent-post">
            <a class="post-img" href="single.php?id=<?php echo (int) $row['id']; ?>">
                <img src="<?php echo e($img); ?>" alt=""/>
            </a>
            <div class="post-content">
                <h5><a href="single.php?id=<?php echo (int) $row['id']; ?>"><?php echo e($row['post_title']); ?></a></h5>
                <div class="meta-row">
                    <span class="meta-pill">
                        <i class="fa-solid <?php echo $icon; ?>"></i>
                        <a href="category.php?cid=<?php echo (int) $row['post_content']; ?>"><?php echo e($row['category_name']); ?></a>
                    </span>
                    <span class="meta-pill">
                        <i class="fa-regular fa-calendar-days"></i>
                        <?php echo e($row['post_date']); ?>
                    </span>
                </div>
                <a class="btn btn-soft btn-xs" href="single.php?id=<?php echo (int) $row['id']; ?>">
                    Read more <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>
                <?php
            }
        }
        ?>
    </div>
</div>
