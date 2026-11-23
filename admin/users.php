<?php
include "header.php";
include "config.php";
require_admin_role($hostname);
?>
  <div id="admin-content">
      <div class="container">
          <div class="row">
              <div class="col-xs-12 col-sm-8 col-md-10">
                  <h1 class="admin-heading"><i class="fa-solid fa-users"></i> All Users</h1>
                  <p class="admin-sub">Admins and editors who can publish content.</p>
              </div>
              <div class="col-xs-12 col-sm-4 col-md-2">
                  <a class="add-new btn btn-brand" href="add-user.php"><i class="fa-solid fa-user-plus"></i> Add User</a>
              </div>
              <div class="col-xs-12">
                  <?php
                 $limit = 10;
                 $page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
                 $offset = ($page - 1) * $limit;
                 $sql = "SELECT * FROM users ORDER BY id DESC LIMIT {$offset}, {$limit}";
                 $result = mysqli_query($conn, $sql) or die("query failed.");
                 if (mysqli_num_rows($result) > 0) {
                  ?>
                  <div class="table-wrap">
                  <table class="content-table">
                      <thead>
                          <tr>
                          <th>ID</th>
                          <th>Full Name</th>
                          <th>Username</th>
                          <th>Role</th>
                          <th>Edit</th>
                          <th>Delete</th>
                          </tr>
                      </thead>
                      <tbody>
                      <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                          <tr>
                              <td class="id"><?php echo (int) $row['id']; ?></td>
                              <td><?php echo e($row['first_name'] . ' ' . $row['last_name']); ?></td>
                              <td><?php echo e($row['username']); ?></td>
                              <td>
                                <span class="badge-role <?php echo ((int) $row['role'] === 1) ? 'is-admin' : 'is-editor'; ?>">
                                  <?php echo ((int) $row['role'] === 1) ? 'Admin' : 'Editor'; ?>
                                </span>
                              </td>
                              <td class="edit">
                                <a class="action-btn edit-btn" href="update-user.php?id=<?php echo (int) $row['id']; ?>" title="Edit">
                                  <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                              </td>
                              <td class="delete">
                                <a class="action-btn delete-btn" href="delete-user.php?id=<?php echo (int) $row['id']; ?>" title="Delete" onclick="return confirm('Delete this user?');">
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
                    echo "<p class='empty-state'>No users found.</p>";
                 }

                 $result1 = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users") or die('Query Failed.');
                 $total_record = (int) mysqli_fetch_assoc($result1)['total'];
                 $total_page = max(1, (int) ceil($total_record / $limit));

                 if ($total_record > $limit) {
                    echo "<ul class='pagination admin-pagination'>";
                    if ($page > 1) {
                        echo '<li><a href="users.php?page=' . ($page - 1) . '"><i class="fa-solid fa-angles-left"></i></a></li>';
                    }
                    for ($i = 1; $i <= $total_page; $i++) {
                        $active = ($i === $page) ? "active" : "";
                        echo '<li class="' . $active . '"><a href="users.php?page=' . $i . '">' . $i . '</a></li>';
                    }
                    if ($total_page > $page) {
                        echo '<li><a href="users.php?page=' . ($page + 1) . '"><i class="fa-solid fa-angles-right"></i></a></li>';
                    }
                    echo "</ul>";
                 }
                  ?>
              </div>
          </div>
      </div>
  </div>
<?php include "footer.php"; ?>
