<?php
session_start();
include '../database.php';
include 'dashbord.php';

// Admin check
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$message = '';

// Handle Add or Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $degree = $_POST['degree'] ?? '';
    $institution = $_POST['institution'] ?? '';
    $address = $_POST['address'] ?? '';
    $start_year = $_POST['start_year'] ?? '';
    $end_year = $_POST['end_year'] ?? '';
    $description = $_POST['description'] ?? '';

    if ($id) {
        // Update
        $stmt = $conn->prepare("UPDATE education SET degree=?, institution=?, address=?, start_year=?, end_year=?, description=? WHERE id=?");
        $stmt->bind_param("ssssisi", $degree, $institution, $address, $start_year, $end_year, $description, $id);
        $stmt->execute();
        $message = "Education updated.";
    } else {
        // Insert
        $stmt = $conn->prepare("INSERT INTO education (degree, institution, address, start_year, end_year, description) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssiss", $degree, $institution, $address, $start_year, $end_year, $description);
        $stmt->execute();
        $message = "Education added.";
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $delId = intval($_GET['delete']);
    $conn->query("DELETE FROM education WHERE id=$delId");
    $message = "Education deleted.";
}

// Fetch all records
$educationRes = $conn->query("SELECT * FROM education ORDER BY start_year DESC");

// For editing
$editId = $_GET['edit'] ?? null;
$editData = null;
if ($editId) {
    $editId = intval($editId);
    $res = $conn->query("SELECT * FROM education WHERE id=$editId");
    if ($res) $editData = $res->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Admin Education</title>
<style>
  body { font-family: Arial, sans-serif;  }
  label { display: block; margin: 10px 0 5px; }
  input[type=text], input[type=number], textarea {
    width: 100%; padding: 8px; box-sizing: border-box;
  }
  textarea { resize: vertical; height: 80px; }
  button { padding: 10px 15px; margin-top: 15px; }
  table { width: 100%; border-collapse: collapse; margin-top: 30px; }
  th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
  th { background: #f2f2f2; }
  a { text-decoration: none; color: blue; }
  .message { margin: 15px 0; color: green; }
</style>
</head>
<body>

<h2><?= $editData ? "Edit Education" : "Add New Education" ?></h2>

<?php if ($message): ?>
  <div class="message"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<form method="post" action="">
    <input type="hidden" name="id" value="<?= $editData['id'] ?? '' ?>" />

    <label>Degree</label>
    <input type="text" name="degree" required value="<?= htmlspecialchars($editData['degree'] ?? '') ?>" />

    <label>Institution</label>
    <input type="text" name="institution" required value="<?= htmlspecialchars($editData['institution'] ?? '') ?>" />

    <label>Address</label>
    <input type="text" name="address" required value="<?= htmlspecialchars($editData['address'] ?? '') ?>" />

    <label>Start Year</label>
    <input type="number" name="start_year" required value="<?= htmlspecialchars($editData['start_year'] ?? '') ?>" />

    <label>End Year (Leave empty if present)</label>
    <input type="number" name="end_year" value="<?= htmlspecialchars($editData['end_year'] ?? '') ?>" />

    <label>Description</label>
    <textarea name="description"><?= htmlspecialchars($editData['description'] ?? '') ?></textarea>

    <button type="submit"><?= $editData ? "Update" : "Add" ?></button>
    <?php if ($editData): ?>
      <a href="education.php" style="margin-left:10px;">Cancel</a>
    <?php endif; ?>
</form>

<h2>Existing Education</h2>
<table>
  <thead>
    <tr>
      <th>Degree</th>
      <th>Institution</th>
      <th>Address</th>
      <th>Period</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php while ($edu = $educationRes->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($edu['degree']) ?></td>
        <td><?= htmlspecialchars($edu['institution']) ?></td>
        <td><?= htmlspecialchars($edu['address']) ?></td>
        <td><?= $edu['start_year'] ?> - <?= $edu['end_year'] ?: 'Present' ?></td>
        <td>
          <a href="?edit=<?= $edu['id'] ?>">Edit</a> |
          <a href="?delete=<?= $edu['id'] ?>" onclick="return confirm('Delete this record?')">Delete</a>
        </td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>

</body>
</html>
