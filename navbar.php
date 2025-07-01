<?php
// Show all errors for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Connect to MySQL
$conn = mysqli_connect("localhost", "root", "", "portfoleo");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
<!DOCTYPE html>
<html>
<head>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>

<nav class="bg-dark text-white vh-100 position-fixed p-4 d-flex flex-column justify-content-between" style="width: 250px; top: 0; left: 0;">
  
  <?php
  $sql = "SELECT * FROM users LIMIT 1";
  $res = mysqli_query($conn, $sql);
  $user = mysqli_fetch_assoc($res);

  if ($user) {
      $username = htmlspecialchars($user['username']);
      $photoPath = htmlspecialchars($user['profile_photo']);
  } 
  ?>
  
  <div>
    <div class="text-center mb-1">
      <img src="<?= htmlspecialchars($photoPath) ?>" alt="Profile Photo"
           class="rounded-circle" width="170" height="190"
           style="object-fit: cover; border: 3px solid #0d6efd;">
      <h3 class="mt-3"><?= $username ?? 'Your Name' ?></h3>
    </div>

    <ul class="nav flex-column mt-4">
      <li class="nav-item"><a class="nav-link text-white" href="index.php?page=home">Home</a></li>
      <li class="nav-item"><a class="nav-link text-white" href="index.php?page=about">About</a></li>
      <li class="nav-item"><a class="nav-link text-white" href="index.php?page=resume">Resume</a></li>
      <li class="nav-item"><a class="nav-link text-white" href="index.php?page=contact">Contact</a></li>
    </ul>
  </div>

  <!-- Social Media Icons -->
  <div class="text-center mt-4">
     <a href="https://github.com/Razza007" target="_blank" class="text-white me-3">
  <i class="bi bi-github fs-4"></i>
</a>
    <a href="https://www.facebook.com/gaming.razza" target="_blank" class="text-white me-3"><i class="bi bi-facebook fs-4"></i></a>
    <a href="https://www.instagram.com/rajkumar_xetteri?igsh=MTF6N29kejVlNGd5OA==" target="_blank" class="text-white me-3"><i class="bi bi-instagram fs-4"></i></a>
    <a href="https://www.linkedin.com/in/rajkumar-raya/" target="_blank" class="text-white"><i class="bi bi-linkedin fs-4"></i></a>
  </div>

</nav>

</body>
</html>
