<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

include '../database.php';
include 'dashbord.php'; // loads sidebar and topbar

$message = '';

// Delete service
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $deleteId = (int) $_GET['delete'];
    $res = $conn->query("SELECT icon_url FROM services WHERE id = $deleteId");
    if ($res && $row = $res->fetch_assoc()) {
        $filePath = '../' . $row['icon_url'];
        if (file_exists($filePath)) unlink($filePath);
    }
    $conn->query("DELETE FROM services WHERE id = $deleteId");
    header("Location: services.php");
    exit;
}

// Add new service
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $iconPath = '';

    if (!empty($_FILES['icon']['name'])) {
        $fileTmpPath = $_FILES['icon']['tmp_name'];
        $fileName = basename($_FILES['icon']['name']);
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'svg'];

        if (in_array($fileExt, $allowed)) {
            $uploadDir = '../uploads/services/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $newFileName = time() . '_' . preg_replace("/[^a-zA-Z0-9\.\-_]/", '', $fileName);
            $destPath = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $iconPath = substr($destPath, 3);
            } else {
                $message = "❌ Failed to move uploaded file.";
            }
        } else {
            $message = "❌ Invalid icon type.";
        }
    }

    if ($title && $description && $iconPath) {
        $stmt = $conn->prepare("INSERT INTO services (title, description, icon_url) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $title, $description, $iconPath);
        if ($stmt->execute()) {
            $message = "✅ Service added successfully.";
        } else {
            $message = "❌ DB error: " . $conn->error;
        }
    } elseif (!$message) {
        $message = "❌ All fields are required.";
    }
}

$services = $conn->query("SELECT * FROM services ORDER BY id DESC");
?>

<div class="container mt-4">
  <h2>Add New Service</h2>

  <?php if ($message): ?>
    <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
  <?php endif; ?>

  <form method="post" enctype="multipart/form-data" class="mb-4">
    <div class="mb-3">
      <label class="form-label">Title</label>
      <input type="text" name="title" class="form-control" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Description</label>
      <textarea name="description" class="form-control" rows="4" required></textarea>
    </div>

    <div class="mb-3">
      <label class="form-label">Upload Icon</label>
      <input type="file" name="icon" accept="image/*" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-primary">➕ Add Service</button>
  </form>

  <hr>

  <h3>Existing Services</h3>
  <div class="row">
    <?php while ($row = $services->fetch_assoc()): ?>
      <div class="col-md-6 mb-4">
        <div class="card shadow-sm">
          <div class="card-body">
            <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger float-end" onclick="return confirm('Delete this service?')">Delete</a>
            <img src="../<?= htmlspecialchars($row['icon_url']) ?>" class="mb-2" style="width:60px; height:60px; object-fit:contain;" alt="Icon">
            <h5><?= htmlspecialchars($row['title']) ?></h5>
            <p><?= nl2br(htmlspecialchars($row['description'])) ?></p>
          </div>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
</div>

</div> <!-- end main-content -->
</body>
</html>
