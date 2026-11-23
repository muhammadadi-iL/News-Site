<?php include "header.php"; ?>
  <div id="admin-content">
      <div class="container">
          <div class="row">
              <div class="col-md-10">
                  <h1 class="admin-heading"><i class="fa-solid fa-file-lines"></i> All Posts</h1>
                  <p class="admin-sub">Manage news articles — create, edit or delete.</p>
              </div>
              <div class="col-md-2">
                  <a class="add-new" href="add-post.php"><i class="fa-solid fa-plus"></i> Add Post</a>
              </div>
              <div class="col-md-12">
                  <?php
                 include "config.php";

                 $limit = 5;
                 $page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
                 $offset = ($page - 1) * $limit;

                 if ((string) $_SESSION["role"] === '1') {
                    $sql = "SELECT posts.id, posts.post_title, posts.post_date, posts.post_image, category.category_name, users.username, posts.post_content
                            FROM posts
                            LEFT JOIN category ON posts.post_content = category.id
                            LEFT JOIN users ON posts.post_author = users.id
                            ORDER BY posts.id DESC LIMIT {$offset}, {$limit}";
                    $count_sql = "SELECT COUNT(*) AS total FROM posts";
                 } else {
                    $uid = (int) $_SESSION['id'];
                    $sql = "SELECT posts.id, posts.post_title, posts.post_date, posts.post_image, category.category_name, users.username, posts.post_content
                            FROM posts
                            LEFT JOIN category ON posts.post_content = category.id
                            LEFT JOIN users ON posts.post_author = users.id
                            WHERE posts.post_author = {$uid}
                            ORDER BY posts.id DESC LIMIT {$offset}, {$limit}";
                    $count_sql = "SELECT COUNT(*) AS total FROM posts WHERE post_author = {$uid}";
                 }

                 $result = mysqli_query($conn, $sql) or die("query failed.");
                 if (mysqli_num_rows($result) > 0) {
                  ?>
                  <div class="table-wrap">
                  <table class="content-table">
                      <thead>
                          <tr>
                          <th>Cover</th>
                          <th>Title</th>
                          <th>Category</th>
                          <th>Date</th>
                          <th>Author</th>
                          <th>Edit</th>
                          <th>Delete</th>
                          </tr>
                      </thead>
                      <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                          <tr>
                              <td class="thumb-cell">
                                <img class="table-thumb" src="<?php echo e(post_image_src($row['post_image'], '../')); ?>" alt="">
                              </td>
                              <td class="title-cell"><?php echo e($row['post_title']); ?></td>
                              <td><span class="badge-cat"><?php echo e($row['category_name']); ?></span></td>
                              <td><?php echo e($row['post_date']); ?></td>
                              <td><?php echo e($row['username']); ?></td>
                              <td class="edit">
                                <a class="action-btn edit-btn" href="update-post.php?id=<?php echo (int) $row['id']; ?>" title="Edit">
                                  <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                              </td>
                              <td class="delete">
                                <a class="action-btn delete-btn" href="delete-post.php?id=<?php echo (int) $row['id']; ?>" title="Delete" onclick="return confirm('Delete this post?');">
                                  <i class="fa-solid fa-trash"></i>
                                </a>
                              </td>
                          </tr>
                        <?php } ?>
                      </tbody>
                  </table>
                  </div>
                  <?php
                 } else {
                    echo "<p class='empty-state'><i class='fa-regular fa-folder-open'></i> No posts yet. <a href='add-post.php'>Add the first post</a></p>";
                 }

                 $result1 = mysqli_query($conn, $count_sql) or die('Query Failed.');
                 $total_record = (int) mysqli_fetch_assoc($result1)['total'];
                 $total_page = max(1, (int) ceil($total_record / $limit));

                 if ($total_record > $limit) {
                    echo "<ul class='pagination admin-pagination'>";
                    if ($page > 1) {
                        echo '<li><a href="post.php?page=' . ($page - 1) . '"><i class="fa-solid fa-angles-left"></i></a></li>';
                    }
                    for ($i = 1; $i <= $total_page; $i++) {
                        $active = ($i === $page) ? "active" : "";
                        echo '<li class="' . $active . '"><a href="post.php?page=' . $i . '">' . $i . '</a></li>';
                    }
                    if ($total_page > $page) {
                        echo '<li><a href="post.php?page=' . ($page + 1) . '"><i class="fa-solid fa-angles-right"></i></a></li>';
                    }
                    echo "</ul>";
                 }
                  ?>
              </div>
          </div>
      </div>
  </div>
<?php include "footer.php"; ?>
