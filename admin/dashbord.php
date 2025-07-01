<?php

if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}
$adminName = $_SESSION['username'] ?? 'Admin';

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Panel</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    /* same sidebar, topbar, and layout CSS from before */
    body, html { margin: 0; height: 100%; font-family: sans-serif; }
    .sidebar {
      position: fixed; top: 0; left: 0;
      width: 260px; height: 100vh;
      background-color: #212529; color: white;
      padding-top: 20px;
    }
    .sidebar img { width: 100px; height: 100px; border-radius: 50%; border: 2px solid #0d6efd; }
    .sidebar h4 { margin-top: 10px; }
    .sidebar nav a {
      display: block; color: #adb5bd;
      padding: 10px 20px; text-decoration: none;
    }
    .sidebar nav a.active, .sidebar nav a:hover { background: #0d6efd; color: white; }

    .topbar {
      margin-left: 260px; height: 50px;
      background: #fff; border-bottom: 1px solid #ccc;
      display: flex; justify-content: flex-end; align-items: center;
      padding: 0 20px;
    }
    .main-content {
      margin-left: 260px; padding: 30px;
    }
    .topbar form button {
      background: none; border: none; color: #dc3545; font-weight: bold;
    }
  </style>
</head>
<body>

<div class="sidebar">
  <div class="text-center">
    
    <h4><?= htmlspecialchars($adminName) ?></h4>
  </div>
  <nav>
    <a href="index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">Dashboard</a>
    <a href="services.php" class="<?= basename($_SERVER['PHP_SELF']) == 'services.php' ? 'active' : '' ?>">Services</a>
    <a href="clients.php" class="<?= basename($_SERVER['PHP_SELF']) == 'clients.php' ? 'active' : '' ?>">Clients</a>
    
    <a href="experience.php" class="<?= basename($_SERVER['PHP_SELF']) == 'experience.php' ? 'active' : '' ?>">experience</a>
       
        <a href="education.php" class="<?= basename($_SERVER['PHP_SELF']) == 'education.php' ? 'active' : '' ?>">education</a>
        <a href="skill.php" class="<?= basename($_SERVER['PHP_SELF']) == 'skill.php' ? 'active' : '' ?>">skills</a>
        <a href="project.php" class="<?= basename($_SERVER['PHP_SELF']) == 'project.php' ? 'active' : '' ?>">project</a>
        <a href="certificate.php" class="<?= basename($_SERVER['PHP_SELF']) == 'certificate.php' ? 'active' : '' ?>">certificate</a>
          <a href="information.php" class="<?= basename($_SERVER['PHP_SELF']) == 'information.php' ? 'active' : '' ?>">about</a>
         

          
  </nav>
</div>

<div class="topbar">
  <form action="logout.php" method="post">
    <button type="submit">Logout</button>
  </form>
</div>

<div class="main-content">
