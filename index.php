<?php
include 'database.php';

// Determine which page to load, default to 'home'
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Whitelist allowed pages for security
$allowed_pages = ['home', 'about', 'resume', 'contact'];

if (!in_array($page, $allowed_pages)) {
    $page = '404'; // Optional: create 404.php to handle not found
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>My Portfolio - <?= ucfirst($page) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="css/style.css" rel="stylesheet" />
</head>
<body>

<?php include 'navbar.php'; ?>

<div style="margin-left: 260px;" class="p-4">
  <?php
    $file = "$page.php";
    if (file_exists($file)) {
        include $file;
    } else {
        echo "<h2>404 Page Not Found</h2>";
    }
  ?>
</div>

</body>
</html>
