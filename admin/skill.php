<?php
include '../database.php';
include 'dashbord.php'; // Ensure this is included for admin check

$message = '';

// DELETE handler
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $conn->query("DELETE FROM skills WHERE id = $delete_id");
    header("Location: skill.php");
    exit;
}

// EDIT handler - fetch skill to edit
$edit_mode = false;
$edit_skill = null;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $result = $conn->query("SELECT * FROM skills WHERE id = $edit_id");
    if ($result && $result->num_rows > 0) {
        $edit_skill = $result->fetch_assoc();
        $edit_mode = true;
    } else {
        $message = "Skill not found.";
    }
}

// POST handler - Add or Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category = $conn->real_escape_string($_POST['category']);
    $skill_name = $conn->real_escape_string($_POST['skill_name']);

    if (!empty($_POST['id'])) {
        // Update
        $id = intval($_POST['id']);
        $conn->query("UPDATE skills SET category='$category', skill_name='$skill_name' WHERE id = $id");
        $message = "Skill updated successfully.";
    } else {
        // Insert new
        $conn->query("INSERT INTO skills (category, skill_name) VALUES ('$category', '$skill_name')");
        $message = "Skill added successfully.";
    }
    header("Location:skill.php");
    exit;
}

// Fetch all skills for listing
$skillsRes = $conn->query("SELECT * FROM skills ORDER BY category, skill_name");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Manage Skills</title>
    <style>
        body { font-family: Arial, sans-serif;  }
        h2 { color: #0056b3; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f0f4fb; }
        input[type=text] { width: 100%; padding: 6px; box-sizing: border-box; }
        button { background-color: #0056b3; color: white; padding: 8px 16px; border: none; cursor: pointer; border-radius: 4px; }
        button:hover { background-color: #003f7d; }
        a { color: #0056b3; text-decoration: none; }
        a:hover { text-decoration: underline; }
        .message { margin: 10px 0; padding: 10px; border-radius: 4px; background-color: #e6f0ff; color: #003f7d; }
        .form-group { margin-bottom: 15px; }
        .actions a { margin-right: 10px; }
    </style>
</head>
<body>

<h2><?= $edit_mode ? "Edit Skill" : "Add Skill" ?></h2>

<?php if ($message): ?>
    <div class="message"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<form method="post" action="skill.php">
    <?php if ($edit_mode): ?>
        <input type="hidden" name="id" value="<?= $edit_skill['id'] ?>">
    <?php endif; ?>
    <div class="form-group">
        <label>Category:</label><br>
        <input type="text" name="category" required value="<?= $edit_mode ? htmlspecialchars($edit_skill['category']) : '' ?>">
    </div>
    <div class="form-group">
        <label>Skill Name:</label><br>
        <input type="text" name="skill_name" required value="<?= $edit_mode ? htmlspecialchars($edit_skill['skill_name']) : '' ?>">
    </div>
    <button type="submit"><?= $edit_mode ? "Update Skill" : "Add Skill" ?></button>
    <?php if ($edit_mode): ?>
        <a href="skill.php" style="margin-left: 10px;">Cancel</a>
    <?php endif; ?>
</form>

<hr>

<h2>All Skills</h2>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Category</th>
            <th>Skill Name</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php while ($skill = $skillsRes->fetch_assoc()): ?>
        <tr>
            <td><?= $skill['id'] ?></td>
            <td><?= htmlspecialchars($skill['category']) ?></td>
            <td><?= htmlspecialchars($skill['skill_name']) ?></td>
            <td class="actions">
                <a href="skill.php?edit=<?= $skill['id'] ?>">Edit</a> | 
                <a href="skill.php?delete=<?= $skill['id'] ?>" onclick="return confirm('Are you sure to delete this skill?');">Delete</a>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>

</body>
</html>
