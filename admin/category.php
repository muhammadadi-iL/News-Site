<?php
include "header.php";
include "config.php";
require_admin_role($hostname);
?>
<div id="admin-content">
    <div class="container">
        <div class="row">
            <div class="col-md-10">
                <h1 class="admin-heading"><i class="fa-solid fa-folder-tree"></i> All Categories</h1>
                <p class="admin-sub">Organize posts into topics readers can browse.</p>
            </div>
            <div class="col-md-2">
                <a class="add-new" href="add-category.php"><i class="fa-solid fa-plus"></i> Add Category</a>
            </div>
            <div class="col-md-12">
                <?php
                 $limit = 10;
                 $page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
                 $offset = ($page - 1) * $limit;
                 $sql = "SELECT * FROM category ORDER BY id DESC LIMIT {$offset}, {$limit}";
                 $result = mysqli_query($conn, $sql) or die("query failed.");
                 if (mysqli_num_rows($result) > 0) {
                  ?>
                <div class="table-wrap">
                <table class="content-table">
                    <thead>
                        <tr>
                        <th>ID</th>
                        <th>Category Name</th>
                        <th>No. of Posts</th>
                        <th>Edit</th>
                        <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                            <tr>
                              <td class="id"><?php echo (int) $row['id']; ?></td>
                              <td><?php echo e($row['category_name']); ?></td>
                              <td><?php echo (int) $row['post']; ?></td>
                             <td class="edit">
                               <a class="action-btn edit-btn" href="update-category.php?id=<?php echo (int) $row['id']; ?>" title="Edit">
                                 <i class="fa-solid fa-pen-to-square"></i>
                               </a>
                             </td>
                            <td class="delete">
                              <a class="action-btn delete-btn" href="delete-category.php?id=<?php echo (int) $row['id']; ?>" title="Delete" onclick="return confirm('Delete this category?');">
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
                    echo "<p class='empty-state'>No categories yet.</p>";
                 }

                 $result1 = mysqli_query($conn, "SELECT COUNT(*) AS total FROM category") or die('Query Failed.');
                 $total_record = (int) mysqli_fetch_assoc($result1)['total'];
                 $total_page = max(1, (int) ceil($total_record / $limit));

                 if ($total_record > $limit) {
                    echo "<ul class='pagination admin-pagination'>";
                    if ($page > 1) {
                        echo '<li><a href="category.php?page=' . ($page - 1) . '"><i class="fa-solid fa-angles-left"></i></a></li>';
                    }
                    for ($i = 1; $i <= $total_page; $i++) {
                        $active = ($i === $page) ? "active" : "";
                        echo '<li class="' . $active . '"><a href="category.php?page=' . $i . '">' . $i . '</a></li>';
                    }
                    if ($total_page > $page) {
                        echo '<li><a href="category.php?page=' . ($page + 1) . '"><i class="fa-solid fa-angles-right"></i></a></li>';
                    }
                    echo "</ul>";
                 }
                  ?>
            </div>
        </div>
    </div>
</div>
<?php include "footer.php"; ?>
