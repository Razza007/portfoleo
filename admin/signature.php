<?php
session_start();
include '../database.php';
include 'dashbord.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['signature_image']) && $_FILES['signature_image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['signature_image']['tmp_name'];
        $fileName = basename($_FILES['signature_image']['name']);
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'svg'];

        if (!in_array($fileExt, $allowed)) {
            $message = '❌ Invalid file type. Allowed: JPG, PNG, GIF, SVG.';
        } else {
            $uploadDir = '../uploads/signature/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

            $newFileName = time() . '_' . preg_replace('/[^a-zA-Z0-9_\-\.]/', '', $fileName);
            $destPath = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $imagePathForDB = substr($destPath, 3); // remove ../

                $stmt = $conn->prepare("UPDATE about SET signature_img = ? WHERE id = 1");
                $stmt->bind_param("s", $imagePathForDB);

                if ($stmt->execute()) {
                    $message = '✅ Signature image updated successfully.';
                } else {
                    $message = '❌ Database update failed: ' . $conn->error;
                }
            } else {
                $message = '❌ Failed to move uploaded file.';
            }
        }
    } else {
        $message = '❌ Please select an image to upload.';
    }
}

// Preview current signature
$signaturePath = '';
$res = $conn->query("SELECT signature_img FROM about WHERE id = 1");
if ($res && $row = $res->fetch_assoc()) {
    $signaturePath = $row['signature_img'];
}
?>

<div class="container mt-4">
  <h2>Upload Signature</h2>

  <?php if ($message): ?>
    <div class="alert <?= strpos($message, '❌') !== false ? 'alert-danger' : 'alert-success' ?>">
        <?= htmlspecialchars($message) ?>
    </div>
  <?php endif; ?>

  <?php if ($signaturePath): ?>
    <p>Current Signature Preview:</p>
    <img src="../<?= htmlspecialchars($signaturePath) ?>" alt="Signature" class="img-thumbnail mb-3" style="max-width: 300px;">
  <?php endif; ?>

  <form method="post" enctype="multipart/form-data">
    <div class="mb-3">
      <label for="signature_image" class="form-label">Select signature image</label>
      <input type="file" name="signature_image" id="signature_image" accept="image/*" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Upload</button>
  </form>
</div>

</div> <!-- end main-content -->
</body>
</html>
