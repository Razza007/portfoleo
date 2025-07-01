<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

include '../database.php';
include 'dashbord.php';

$message = '';

// Delete client
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $deleteId = (int) $_GET['delete'];
    $res = $conn->query("SELECT logo_url FROM clients WHERE id = $deleteId");
    if ($res && $row = $res->fetch_assoc()) {
        $filePath = '../' . $row['logo_url'];
        if (file_exists($filePath)) unlink($filePath);
    }
    $conn->query("DELETE FROM clients WHERE id = $deleteId");
    header("Location: clients.php");
    exit;
}

// Add new client
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $logoPath = '';

    if (!empty($_FILES['logo']['name'])) {
        $tmp = $_FILES['logo']['tmp_name'];
        $fileName = basename($_FILES['logo']['name']);
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'svg'];

        if (in_array($ext, $allowed)) {
            $uploadDir = '../uploads/clients/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $newName = time() . '_' . preg_replace("/[^a-zA-Z0-9.\-_]/", '', $fileName);
            $dest = $uploadDir . $newName;

            if (move_uploaded_file($tmp, $dest)) {
                $logoPath = substr($dest, 3);
            } else {
                $message = "❌ Failed to upload logo.";
            }
        } else {
            $message = "❌ Invalid file type.";
        }
    }

    if ($name && $logoPath) {
        $stmt = $conn->prepare("INSERT INTO clients (name, logo_url) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $logoPath);
        if ($stmt->execute()) {
            $message = "✅ Client added.";
        } else {
            $message = "❌ DB error: " . $conn->error;
        }
    } elseif (!$message) {
        $message = "❌ All fields required.";
    }
}

$clients = $conn->query("SELECT * FROM clients ORDER BY id DESC");
?>

<div class="container mt-4">
  <h2>Add New Client</h2>

  <?php if ($message): ?>
    <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
  <?php endif; ?>

  <form method="post" enctype="multipart/form-data" class="mb-4">
    <div class="mb-3">
      <label class="form-label">Client Name</label>
      <input type="text" name="name" class="form-control" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Upload Logo</label>
      <input type="file" name="logo" accept="image/*" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-primary">➕ Add Client</button>
  </form>

  <hr>

  <h3>Existing Clients</h3>
  <div class="row">
    <?php while ($row = $clients->fetch_assoc()): ?>
      <div class="col-md-3 col-sm-4 col-6 mb-4 text-center">
        <div class="border p-3 rounded shadow-sm bg-white position-relative">
          <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1" onclick="return confirm('Delete this client?')">✖</a>
          <img src="../<?= htmlspecialchars($row['logo_url']) ?>" alt="Client Logo" class="img-fluid mb-2" style="max-height: 100px; object-fit: contain;">
          <h6><?= htmlspecialchars($row['name']) ?></h6>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
</div>

</div> <!-- end main-content -->
</body>
</html>
