<?php include 'header.php'; ?>
    <div id="main-content">
        <div class="container page-shell">
            <div class="row content-grid">
                <div class="col-xs-12 col-sm-12 col-md-8">
                    <div class="post-container">
                    <?php
                        include "config.php";
                        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

                        $sql = "SELECT posts.id, posts.post_title, posts.post_keywords, posts.post_date, posts.post_author, posts.post_image, category.category_name, users.username, posts.post_content FROM posts
                        LEFT JOIN category ON posts.post_content = category.id
                        LEFT JOIN users ON posts.post_author = users.id
                        WHERE posts.id = {$id}";

                        $result = mysqli_query($conn, $sql) or die("query failed.");
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $img = post_image_src($row['post_image']);
                                $icon = category_icon($row['category_name']);
                                $cid = (int) $row['post_content'];
                        ?>
                        <article class="post-content single-post">
                            <p class="story-eyebrow"><i class="fa-solid <?php echo $icon; ?>"></i> <?php echo e($row['category_name']); ?> briefing</p>
                            <h1 class="single-title"><?php echo e($row['post_title']); ?></h1>
                            <div class="post-information meta-row">
                                <span class="meta-pill">
                                    <i class="fa-solid fa-user"></i>
                                    <a href="author.php?aid=<?php echo (int) $row['post_author']; ?>"><?php echo e($row['username']); ?></a>
                                </span>
                                <span class="meta-pill">
                                    <i class="fa-regular fa-calendar-days"></i>
                                    <?php echo e($row['post_date']); ?>
                                </span>
                                <span class="meta-pill">
                                    <i class="fa-solid fa-folder-open"></i>
                                    <a href="category.php?cid=<?php echo $cid; ?>"><?php echo e($row['category_name']); ?></a>
                                </span>
                            </div>
                            <img class="single-feature-image" src="<?php echo e($img); ?>" alt="<?php echo e($row['post_title']); ?>"/>
                            <div class="single-body">
                              <?php echo nl2br(e($row['post_keywords'])); ?>
                            </div>
                            <div class="article-actions">
                                <a class="btn btn-ghost" href="<?php echo $hostname; ?>"><i class="fa-solid fa-arrow-left"></i> Back to home</a>
                                <a class="btn btn-soft" href="stories.php"><i class="fa-solid fa-satellite-dish"></i> Live stories</a>
                                <a class="btn btn-accent" href="category.php?cid=<?php echo $cid; ?>"><i class="fa-solid <?php echo $icon; ?>"></i> More in <?php echo e($row['category_name']); ?></a>
                            </div>
                        </article>
                        <?php
                            $related = mysqli_query($conn, "SELECT posts.id, posts.post_title, posts.post_date, category.category_name FROM posts LEFT JOIN category ON posts.post_content = category.id WHERE posts.post_content = {$cid} AND posts.id <> {$id} ORDER BY posts.id DESC LIMIT 3");
                            if ($related && mysqli_num_rows($related) > 0) {
                                echo '<div class="related-block"><h3>Related desk notes</h3><ul class="related-list">';
                                while ($rel = mysqli_fetch_assoc($related)) {
                                    echo '<li><a href="single.php?id=' . (int) $rel['id'] . '">' . e($rel['post_title']) . '</a><span>' . e($rel['post_date']) . '</span></li>';
                                }
                                echo '</ul></div>';
                            }
                            }
                        } else {
                            echo "<p class='empty-state'><i class='fa-regular fa-circle-xmark'></i> No Record Found</p>";
                        }
                        ?>
                    </div>

                    <div class="single-live-wrap">
                      <div class="live-feed-wrap" data-vue-live data-limit="6" data-layout="compact"></div>
                    </div>
                </div>
                <?php include 'sidebar.php'; ?>
            </div>
        </div>
    </div>
<?php include 'footer.php'; ?>
