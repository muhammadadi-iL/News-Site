<?php include 'header.php'; ?>
    <div id="main-content">
        <div class="container">
            <?php
            include "config.php";
            $featured_sql = "SELECT posts.id, posts.post_title, posts.post_keywords, posts.post_date, posts.post_author, posts.post_image, category.category_name, users.username, posts.post_content
                             FROM posts
                             LEFT JOIN category ON posts.post_content = category.id
                             LEFT JOIN users ON posts.post_author = users.id
                             ORDER BY posts.id DESC LIMIT 1";
            $featured_res = mysqli_query($conn, $featured_sql);
            $featured = $featured_res ? mysqli_fetch_assoc($featured_res) : null;
            if ($featured) {
                $fimg = post_image_src($featured['post_image']);
            ?>
            <div class="hero-feature" style="--hero-image: url('<?php echo e($fimg); ?>')">
                <div class="hero-overlay">
                    <span class="hero-kicker"><i class="fa-solid fa-bolt"></i> Top Story</span>
                    <h1><a href="single.php?id=<?php echo (int) $featured['id']; ?>"><?php echo e($featured['post_title']); ?></a></h1>
                    <p><?php echo e(substr($featured['post_keywords'], 0, 160)); ?>...</p>
                    <div class="hero-meta">
                        <span><i class="fa-solid fa-tags"></i> <?php echo e($featured['category_name']); ?></span>
                        <span><i class="fa-solid fa-user"></i> <?php echo e($featured['username']); ?></span>
                        <span><i class="fa-regular fa-calendar-days"></i> <?php echo e($featured['post_date']); ?></span>
                    </div>
                    <a class="hero-cta" href="single.php?id=<?php echo (int) $featured['id']; ?>">
                        Read full story <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>
            <?php } ?>

            <div class="row">
                <div class="col-md-8">
                    <div class="post-container">
                        <h2 class="page-heading"><i class="fa-solid fa-newspaper"></i> Latest News</h2>
                        <?php
                        $limit = 3;
                        $page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
                        $offset = ($page - 1) * $limit;

                        $sql = "SELECT posts.id, posts.post_title, posts.post_keywords, posts.post_date, posts.post_author, posts.post_image, category.category_name, users.username, posts.post_content FROM posts
                        LEFT JOIN category ON posts.post_content = category.id
                        LEFT JOIN users ON posts.post_author = users.id
                        ORDER BY posts.id DESC LIMIT {$offset}, {$limit}";

                        $result = mysqli_query($conn, $sql) or die("query failed.");
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $img = post_image_src($row['post_image']);
                        ?>
                        <div class="post-content">
                            <div class="row">
                                <div class="col-md-4">
                                    <a class="post-img" href="single.php?id=<?php echo (int) $row['id']; ?>">
                                        <img src="<?php echo e($img); ?>" alt=""/>
                                    </a>
                                </div>
                                <div class="col-md-8">
                                    <div class="inner-content clearfix">
                                        <h3><a href="single.php?id=<?php echo (int) $row['id']; ?>"><?php echo e($row['post_title']); ?></a></h3>
                                        <div class="post-information">
                                            <span>
                                                <i class="fa-solid fa-tags"></i>
                                                <a href="category.php?cid=<?php echo (int) $row['post_content']; ?>"><?php echo e($row['category_name']); ?></a>
                                            </span>
                                            <span>
                                                <i class="fa-solid fa-user"></i>
                                                <a href="author.php?aid=<?php echo (int) $row['post_author']; ?>"><?php echo e($row['username']); ?></a>
                                            </span>
                                            <span>
                                                <i class="fa-regular fa-calendar-days"></i>
                                                <?php echo e($row['post_date']); ?>
                                            </span>
                                        </div>
                                        <p class="description">
                                            <?php echo e(substr($row['post_keywords'], 0, 130)) . "..."; ?>
                                        </p>
                                        <a class="read-more pull-right" href="single.php?id=<?php echo (int) $row['id']; ?>">
                                            read more <i class="fa-solid fa-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                            }
                        } else {
                            echo "<p class='empty-state'><i class='fa-regular fa-folder-open'></i> No Record Found</p>";
                        }

                        $sql1 = "SELECT COUNT(*) AS total FROM posts";
                        $result1 = mysqli_query($conn, $sql1) or die('Query Failed.');
                        $total_record = (int) mysqli_fetch_assoc($result1)['total'];
                        $total_page = (int) ceil($total_record / $limit);

                        if ($total_page > 1) {
                           echo "<ul class='pagination admin-pagination'>";
                           if ($page > 1) {
                               echo '<li><a href="index.php?page=' . ($page - 1) . '"><i class="fa-solid fa-angles-left"></i></a></li>';
                           }
                           for ($i = 1; $i <= $total_page; $i++) {
                               $active = ($i === $page) ? "active" : "";
                               echo '<li class="' . $active . '"><a href="index.php?page=' . $i . '">' . $i . '</a></li>';
                           }
                           if ($total_page > $page) {
                               echo '<li><a href="index.php?page=' . ($page + 1) . '"><i class="fa-solid fa-angles-right"></i></a></li>';
                           }
                           echo "</ul>";
                        }
                        ?>
                    </div>
                </div>
                <?php include 'sidebar.php'; ?>
            </div>
        </div>
    </div>
<?php include 'footer.php'; ?>
