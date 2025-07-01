<?php
session_start();
include '../database.php';
include 'dashbord.php';

$adminId = $_SESSION['admin_id'] ?? 0;
$message = '';

// Handle photo upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_FILES['profile_photo']['name'])) {
        $tmp = $_FILES['profile_photo']['tmp_name'];
        $fileName = basename($_FILES['profile_photo']['name']);
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($ext, $allowed)) {
            $uploadDir = '../uploads/admin/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

            $newName = time() . '_' . preg_replace("/[^a-zA-Z0-9.\-_]/", '', $fileName);
            $dest = $uploadDir . $newName;

            if (move_uploaded_file($tmp, $dest)) {
                $pathForDb = substr($dest, 3);
                $conn->query("UPDATE users SET profile_photo = '$pathForDb' ");
                $message = "✅ Profile photo updated!";
            } else {
                $message = "❌ Failed to upload.";
            }
        } else {
            $message = "❌ Invalid file type.";
        }
    } else {
        $message = "❌ No file selected.";
    }
}

// Fetch current photo
$res = $conn->query("SELECT username, profile_photo FROM users");
$row = $res->fetch_assoc();
$currentImg = $row['profile_photo'] ?? '';

?>

<div class="container mt-4">
    <h2>Update Profile Photo</h2>

    <?php if ($message): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if ($currentImg): ?>
        <p>Current Image:</p>
        <img src="../<?= htmlspecialchars($currentImg) ?>" class="img-thumbnail mb-3" style="max-width: 150px;">
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="profile_photo" class="form-label">Choose Profile Photo</label>
            <input type="file" name="profile_photo" class="form-control" accept="image/*" required>
        </div>
        <button class="btn btn-primary">Update Photo</button>
    </form>
</div>

</div> <!-- end main-content -->
</body>
</html>
