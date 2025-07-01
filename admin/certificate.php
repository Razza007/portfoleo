<?php
// certificate.php
include '../database.php'; // your DB connection
include 'dashbord.php';
// Initialize message for feedback
$message = '';

// Handle Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM certifications WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = "Certification deleted successfully.";
    } else {
        $message = "Delete failed: " . $conn->error;
    }
    header("Location: certificate.php?msg=" . urlencode($message));
    exit;
}

// Handle Add or Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $issuer = $_POST['issuer'] ?? '';
    $issue_year = $_POST['issue_year'] ?? '';

    if (isset($_POST['id']) && $_POST['id']) {
        // Update existing
        $id = intval($_POST['id']);
        $stmt = $conn->prepare("UPDATE certifications SET title = ?, issuer = ?, issue_year = ? WHERE id = ?");
        $stmt->bind_param("ssii", $title, $issuer, $issue_year, $id);
        if ($stmt->execute()) {
            $message = "Certification updated successfully.";
        } else {
            $message = "Update failed: " . $conn->error;
        }
    } else {
        // Insert new
        $stmt = $conn->prepare("INSERT INTO certifications (title, issuer, issue_year) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $title, $issuer, $issue_year);
        if ($stmt->execute()) {
            $message = "Certification added successfully.";
        } else {
            $message = "Insert failed: " . $conn->error;
        }
    }
    header("Location: certificate.php?msg=" . urlencode($message));
    exit;
}

// Fetch certification to edit if edit param exists
$edit_cert = null;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $res = $conn->query("SELECT * FROM certifications WHERE id = $edit_id");
    if ($res && $res->num_rows > 0) {
        $edit_cert = $res->fetch_assoc();
    }
}

// Fetch all certifications
$certifications = $conn->query("SELECT * FROM certifications ORDER BY issue_year DESC");

// Get message from URL param (redirect feedback)
if (isset($_GET['msg'])) {
    $message = htmlspecialchars($_GET['msg']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Manage Certifications</title>
<style>
    body { font-family: Arial, sans-serif;  }
    h1 { color: #0056b3; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
    th { background-color: #f0f0f0; }
    form { margin-top: 20px; background: #f9f9f9; padding: 20px; border-radius: 8px; }
    label { display: block; margin-top: 10px; font-weight: bold; }
    input[type="text"], input[type="number"] { width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px; }
    button { margin-top: 15px; padding: 10px 20px; background-color: #0056b3; color: white; border: none; border-radius: 4px; cursor: pointer; }
    button:hover { background-color: #003d80; }
    .message { margin: 20px 0; color: green; font-weight: bold; }
    .actions a { margin-right: 10px; color: #0056b3; text-decoration: none; }
    .actions a:hover { text-decoration: underline; }
</style>
</head>
<body>

<h1>Manage Certifications</h1>

<?php if ($message): ?>
    <div class="message"><?= $message ?></div>
<?php endif; ?>

<table>
    <thead>
        <tr>
            <th>Title</th>
            <th>Issuer</th>
            <th>Issue Year</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($certifications && $certifications->num_rows > 0): ?>
            <?php while ($cert = $certifications->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($cert['title']) ?></td>
                    <td><?= htmlspecialchars($cert['issuer']) ?></td>
                    <td><?= htmlspecialchars($cert['issue_year']) ?></td>
                    <td class="actions">
                        <a href="certificate.php?edit=<?= $cert['id'] ?>">Edit</a> |
                        <a href="certificate.php?delete=<?= $cert['id'] ?>" onclick="return confirm('Are you sure you want to delete this certification?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="4">No certifications found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<h2><?= $edit_cert ? 'Edit Certification' : 'Add New Certification' ?></h2>

<form method="post" action="certificate.php">
    <input type="hidden" name="id" value="<?= $edit_cert['id'] ?? '' ?>" />
    
    <label for="title">Title</label>
    <input type="text" id="title" name="title" required value="<?= htmlspecialchars($edit_cert['title'] ?? '') ?>" />
    
    <label for="issuer">Issuer</label>
    <input type="text" id="issuer" name="issuer" required value="<?= htmlspecialchars($edit_cert['issuer'] ?? '') ?>" />
    
    <label for="issue_year">Issue Year</label>
    <input type="number" id="issue_year" name="issue_year" required min="1900" max="<?= date('Y') ?>" value="<?= htmlspecialchars($edit_cert['issue_year'] ?? '') ?>" />
    
    <button type="submit"><?= $edit_cert ? 'Update Certification' : 'Add Certification' ?></button>
    <?php if ($edit_cert): ?>
        <a href="certificate.php" style="margin-left:15px; color:#555;">Cancel</a>
    <?php endif; ?>
</form>

</body>
</html>
