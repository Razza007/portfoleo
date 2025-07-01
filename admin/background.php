<?php
include '../database.php';
include 'dashbord.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['bg_image'])) {
    $imageName = time() . '_' . basename($_FILES['bg_image']['name']);
    $target = '../uploads/homepage/' . $imageName;
    if (move_uploaded_file($_FILES['bg_image']['tmp_name'], $target)) {
        $conn->query("UPDATE homepage_settings SET background_image = '$target' WHERE id = 1");
        $success = "Background updated successfully!";
    } else {
        $error = "Image upload failed.";
    }
}

$result = $conn->query("SELECT background_image FROM homepage_settings WHERE id = 1");
$row = $result->fetch_assoc();
$currentImage = $row['background_image'];
?>

<h2>Update Homepage Background</h2>
<?php if (!empty($success)) echo "<p style='color:green;'>$success</p>"; ?>
<?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
<form method="POST" enctype="multipart/form-data">
  <label>Select Background Image:</label><br>
  <input type="file" name="bg_image" accept="image/*" required><br><br>
  <button type="submit">Upload</button>
</form>

<p>Current Image:</p>
<img src="<?= $currentImage ?>" style="max-width:300px; border:1px solid #ccc;">
