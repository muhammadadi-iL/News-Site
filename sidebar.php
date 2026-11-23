<div id="sidebar" class="col-xs-12 col-sm-12 col-md-4">
    <div class="side-card search-box-container">
        <h4>Search the archive</h4>
        <div
            data-vue-search
            data-initial="<?php echo isset($_GET['search']) ? e($_GET['search']) : ''; ?>"
        ></div>
        <p class="help-text">Autocomplete mixes local posts + live web headlines (Vue.js).</p>
    </div>

    <div class="side-card recent-post-container">
        <h4>Recent Posts</h4>
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
                <p class="story-eyebrow"><i class="fa-solid <?php echo $icon; ?>"></i> <?php echo e($row['category_name']); ?></p>
                <h5><a href="single.php?id=<?php echo (int) $row['id']; ?>"><?php echo e($row['post_title']); ?></a></h5>
                <span class="recent-date"><i class="fa-regular fa-calendar-days"></i> <?php echo e($row['post_date']); ?></span>
            </div>
        </div>
                <?php
            }
        }
        ?>
    </div>
</div>
