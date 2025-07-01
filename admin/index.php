<?php
session_start();
include '../database.php';
include 'dashbord.php';
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$adminName = $_SESSION['username'] ?? 'Admin';
$userCount = $conn->query("SELECT COUNT(*) AS c FROM users")->fetch_assoc()['c'] ?? 0;
$projectCount = $conn->query("SELECT COUNT(*) AS c FROM projects")->fetch_assoc()['c'] ?? 0;
$educationCount = $conn->query("SELECT COUNT(*) AS c FROM education")->fetch_assoc()['c'] ?? 0;

$latestProjects = $conn->query("SELECT id, title, year FROM projects ORDER BY id DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background-color: #f8f9fa;
      min-height: 100vh;
      margin: 0;
      padding: 0;
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    }

    /* Assuming your fixed sidebar is 250px wide */
    .main-content {
      margin-left: 150px; /* space for fixed sidebar */
     
      min-height: 100vh;
    }

    .card-summary {
      border-left: 5px solid #0d6efd;
      box-shadow: 0 4px 6px rgb(0 0 0 / 0.1);
      transition: transform 0.2s ease-in-out;
    }
    .card-summary:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 12px rgb(0 0 0 / 0.15);
    }
    .quick-links a {
      min-width: 160px;
    }
  </style>
</head>
<body>
  <!-- Your fixed left sidebar should be outside this main-content div -->

  <div class="main-content">
    <h1 class="mb-4 text-primary">Welcome, <?= htmlspecialchars($adminName) ?>!</h1>

    <div class="row g-4 mb-5">
      <div class="col-md-4">
        <div class="card card-summary p-3 bg-white">
          <h5 class="text-secondary mb-2">Total Users</h5>
          <h2 class="fw-bold text-dark"><?= $userCount ?></h2>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card card-summary p-3 bg-white">
          <h5 class="text-secondary mb-2">Total Projects</h5>
          <h2 class="fw-bold text-dark"><?= $projectCount ?></h2>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card card-summary p-3 bg-white">
          <h5 class="text-secondary mb-2">Education Records</h5>
          <h2 class="fw-bold text-dark"><?= $educationCount ?></h2>
        </div>
      </div>
    </div>

    <section class="mb-5">
      <h3 class="mb-3 text-primary">Latest Projects</h3>
      <?php if ($latestProjects && $latestProjects->num_rows > 0): ?>
        <ul class="list-group shadow-sm">
          <?php while ($project = $latestProjects->fetch_assoc()): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <?= htmlspecialchars($project['title']) ?>
              <span class="badge bg-primary rounded-pill"><?= htmlspecialchars($project['year']) ?></span>
            </li>
          <?php endwhile; ?>
        </ul>
      <?php else: ?>
        <p class="text-muted">No projects found.</p>
      <?php endif; ?>
    </section>

    <section class="quick-links">
      <h3 class="mb-3 text-primary">Quick Actions</h3>
      <div class="d-flex flex-wrap gap-3">
        <a href="information.php" class="btn btn-primary">Edit About Info</a>
        <a href="role.php" class="btn btn-secondary">update roles</a>
        <a href="profile.php" class="btn btn-info text-white">update profile</a>
        <a href="user.php" class="btn btn-success">Manage Users</a>
        <a href="username.php" class="btn btn-success">change username</a>
         <a href="background.php" class="btn  btn-info text-white">change background image</a>
            <a href="signature.php" class="btn btn-primary">update signature</a>
          
      </div>
    </section>
  </div>

</body>
</html>
