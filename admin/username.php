<?php
include '../database.php'; // your DB connection file
include 'dashbord.php'; // Include admin dashboard layout
// Handle username update
if (isset($_POST['update_user'])) {
    $user_id = (int)$_POST['user_id'];
    $new_username = trim(mysqli_real_escape_string($conn, $_POST['username']));
    if ($new_username !== '') {
        mysqli_query($conn, "UPDATE users SET username='$new_username' WHERE id=$user_id");
    } else {
        $error = "Username cannot be empty.";
    }
}

// Fetch all users
$result = mysqli_query($conn, "SELECT * FROM users ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Users Admin Panel</title>
<style>
  body { font-family: Arial, sans-serif;  }
  table { width: 100%; border-collapse: collapse; margin-top: 20px; }
  th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
  form { margin: 0; }
  input[type=text] { padding: 5px; width: 300px; }
  button { padding: 5px 10px; }
  .error { color: red; margin-top: 10px; }
</style>
</head>
<body>

<h2>Manage Users</h2>

<?php if (!empty($error)): ?>
  <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<table>
  <thead>
    <tr>
      <th>ID</th>
      <th>Username</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
    <?php while ($user = mysqli_fetch_assoc($result)): ?>
      <tr>
        <td><?= $user['id'] ?></td>
        <td>
          <form method="post" action="">
            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
            <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
        </td>
        <td>
            <button type="submit" name="update_user">Update</button>
          </form>
        </td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>

</body>
</html>
