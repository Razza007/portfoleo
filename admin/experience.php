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
    $id = $_POST['id'] ?? null;
    $job_title = $_POST['job_title'] ?? '';
    $company = $_POST['company'] ?? '';
    $address = $_POST['address'] ?? '';
    $start_year = $_POST['start_year'] ?? '';
    $end_year = $_POST['end_year'] ?? '';
    $description = $_POST['description'] ?? '';

    if ($id) {
        $stmt = $conn->prepare("UPDATE experience SET job_title=?, company=?, address=?, start_year=?, end_year=?, description=? WHERE id=?");
        $stmt->bind_param("ssssisi", $job_title, $company, $address, $start_year, $end_year, $description, $id);
        $stmt->execute();
        $message = "Experience updated.";
    } else {
        $stmt = $conn->prepare("INSERT INTO experience (job_title, company, address, start_year, end_year, description) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssiss", $job_title, $company, $address, $start_year, $end_year, $description);
        $stmt->execute();
        $message = "Experience added.";
    }
}

if (isset($_GET['delete'])) {
    $delId = intval($_GET['delete']);
    $conn->query("DELETE FROM experience WHERE id=$delId");
    $message = "Experience deleted.";
}

$experienceRes = $conn->query("SELECT * FROM experience ORDER BY start_year DESC");

$editId = $_GET['edit'] ?? null;
$editData = null;
if ($editId) {
    $editId = intval($editId);
    $res = $conn->query("SELECT * FROM experience WHERE id=$editId");
    if ($res) $editData = $res->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Admin Experience</title>
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

<h2><?= $editData ? "Edit Experience" : "Add New Experience" ?></h2>

<?php if ($message): ?>
  <div class="message"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<form method="post" action="">
    <input type="hidden" name="id" value="<?= $editData['id'] ?? '' ?>" />

    <label>Job Title</label>
    <input type="text" name="job_title" required value="<?= htmlspecialchars($editData['job_title'] ?? '') ?>" />

    <label>Company</label>
    <input type="text" name="company" required value="<?= htmlspecialchars($editData['company'] ?? '') ?>" />

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
      <a href="experience.php" style="margin-left:10px;">Cancel</a>
    <?php endif; ?>
</form>

<h2>Existing Experience</h2>
<table>
  <thead>
    <tr>
      <th>Job Title</th>
      <th>Company</th>
      <th>Address</th>
      <th>Period</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php while ($exp = $experienceRes->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($exp['job_title']) ?></td>
        <td><?= htmlspecialchars($exp['company']) ?></td>
        <td><?= htmlspecialchars($exp['address']) ?></td>
        <td><?= $exp['start_year'] ?> - <?= $exp['end_year'] ?: 'Present' ?></td>
        <td>
          <a href="?edit=<?= $exp['id'] ?>">Edit</a> |
          <a href="?delete=<?= $exp['id'] ?>" onclick="return confirm('Delete this record?')">Delete</a>
        </td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>

</body>
</html>
