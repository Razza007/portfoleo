<?php
session_start();
include '../database.php';
include 'dashbord.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$message = "";

// Handle Add User
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if ($username && $email && filter_var($email, FILTER_VALIDATE_EMAIL) && $password) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $hashed);
        $stmt->execute();
        $message = $stmt->affected_rows > 0 ? "✅ User added successfully." : "❌ Failed to add user.";
    } else {
        $message = "❌ Fill all fields with valid data.";
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM users WHERE id = $id");
    header("Location: user.php");
    exit;
}

// Fetch all users
$users = $conn->query("SELECT id, username, email FROM users ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin - Manage Users</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f5f7fa;
    }
    .main-content {
      margin-left: 180px;
      padding: 30px;
    }
    .password-toggle {
      position: absolute;
      top: 50%;
      right: 12px;
      transform: translateY(-50%);
      cursor: pointer;
    }
  </style>
</head>
<body>

<div class="main-content">
  <h2 class="text-primary mb-4">👥 Manage Users</h2>

  <?php if ($message): ?>
    <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
  <?php endif; ?>

  <!-- Add New User -->
  <div class="card mb-5 shadow-sm">
    <div class="card-header bg-primary text-white">➕ Add New User</div>
    <div class="card-body">
      <form method="POST" class="row g-3 position-relative">
        <div class="col-md-4">
          <input type="text" name="username" class="form-control" placeholder="Username" required>
        </div>
        <div class="col-md-4">
          <input type="email" name="email" class="form-control" placeholder="Email" required>
        </div>
        <div class="col-md-4 position-relative">
          <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
          <input type="checkbox" id="togglePassword" class="password-toggle">
        </div>
        <div class="col-12 text-end">
          <button type="submit" name="add_user" class="btn btn-success px-4">Add User</button>
        </div>
      </form>
    </div>
  </div>

  <!-- User List -->
  <div class="card shadow-sm">
    <div class="card-header bg-secondary text-white">📋 User List</div>
    <div class="card-body p-0">
      <table class="table table-bordered table-hover m-0">
        <thead class="table-light">
          <tr>
            <th style="width: 50px;">ID</th>
            <th>Username</th>
            <th>Email</th>
            <th style="width: 100px;">Action</th>
          </tr>
        </thead>
        <tbody>
        <?php if ($users->num_rows > 0): ?>
          <?php while ($u = $users->fetch_assoc()): ?>
            <tr>
              <td><?= $u['id'] ?></td>
              <td><?= htmlspecialchars($u['username']) ?></td>
              <td><?= htmlspecialchars($u['email']) ?></td>
              <td>
                <a href="?delete=<?= $u['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure to delete this user?')">Delete</a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="4" class="text-center text-muted">No users found.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
document.getElementById('togglePassword').addEventListener('change', function () {
  const pwd = document.getElementById('password');
  pwd.type = this.checked ? 'text' : 'password';
});
</script>

</body>
</html>
