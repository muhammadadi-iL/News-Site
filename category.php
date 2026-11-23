<?php include 'header.php'; ?>
    <div id="main-content">
      <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="post-container">
                <?php
                    include "config.php";

                    $id = isset($_GET['cid']) ? (int) $_GET['cid'] : 0;

                    $sql1 = "SELECT * FROM category WHERE id = {$id}";
                    $result1 = mysqli_query($conn, $sql1) or die('Query Failed.');
                    $row1 = mysqli_fetch_assoc($result1);
                ?>
                  <h2 class="page-heading"><i class="fa-solid fa-folder-open" aria-hidden="true"></i> <?php echo $row1 ? htmlspecialchars($row1['category_name']) : 'Category'; ?></h2>
                  <?php
                        $limit = 5;

                        if (isset($_GET['page'])) {
                            $page = (int) $_GET['page'];
                        } else {
                            $page = 1;
                        }
                        $offset = ($page - 1) * $limit;

                        $sql = "SELECT posts.id, posts.post_title, posts.post_keywords, posts.post_date, posts.post_author, posts.post_image, category.category_name, users.username, posts.post_content FROM posts
                        LEFT JOIN category ON posts.post_content = category.id
                        LEFT JOIN users ON posts.post_author = users.id
                        WHERE posts.post_content = {$id}
                        ORDER BY posts.id DESC LIMIT {$offset}, {$limit}";

                        $result = mysqli_query($conn, $sql) or die("query failed.");
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $img = !empty($row['post_image']) ? "images/" . $row['post_image'] : "images/post-format.jpg";
                                if (!file_exists(__DIR__ . "/" . $img)) {
                                    $img = "images/post-format.jpg";
                                }
                        ?>
                        <div class="post-content">
                            <div class="row">
                                <div class="col-md-4">
                                    <a class="post-img" href="single.php?id=<?php echo $row['id']; ?>"><img src="<?php echo htmlspecialchars($img); ?>" alt=""/></a>
                                </div>
                                <div class="col-md-8">
                                    <div class="inner-content clearfix">
                                        <h3><a href="single.php?id=<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['post_title']); ?></a></h3>
                                        <div class="post-information">
                                            <span>
                                                <i class="fa-solid fa-tags" aria-hidden="true"></i>
                                                <a href="category.php?cid=<?php echo $row['post_content']; ?>"><?php echo htmlspecialchars($row['category_name']); ?></a>
                                            </span>
                                            <span>
                                                <i class="fa-solid fa-user" aria-hidden="true"></i>
                                                <a href="author.php?aid=<?php echo $row['post_author']; ?>"><?php echo htmlspecialchars($row['username']); ?></a>
                                            </span>
                                            <span>
                                                <i class="fa-regular fa-calendar-days" aria-hidden="true"></i>
                                                <?php echo htmlspecialchars($row['post_date']); ?>
                                            </span>
                                        </div>
                                        <p class="description">
                                        <?php echo htmlspecialchars(substr($row['post_keywords'], 0, 130)) . "..."; ?>
                                        </p>
                                        <a class="read-more pull-right" href="single.php?id=<?php echo $row['id']; ?>">
                                            read more <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
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

                        if ($row1 && (int) $row1['post'] > 0) {
                            $total_record = (int) $row1['post'];
                            $total_page = ceil($total_record / $limit);

                            echo "<ul class='pagination admin-pagination'>";

                            if ($page > 1) {
                                echo '<li><a href="category.php?cid=' . $id . '&page=' . ($page - 1) . '"><i class="fa-solid fa-angles-left" aria-hidden="true"></i></a></li>';
                            }

                            for ($i = 1; $i <= $total_page; $i++) {
                                $active = ($i == $page) ? "active" : "";
                                echo '<li class="' . $active . '"><a href="category.php?cid=' . $id . '&page=' . $i . '">' . $i . '</a></li>';
                            }
                            if ($total_page > $page) {
                                echo '<li><a href="category.php?cid=' . $id . '&page=' . ($page + 1) . '"><i class="fa-solid fa-angles-right" aria-hidden="true"></i></a></li>';
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
