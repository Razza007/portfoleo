<?php
include '../database.php';
include 'dashbord.php';

// Initialize variables
$editData = null;
$title = $description = $technologies = $year = '';
$editing = false;

// Handle edit request (fetch data)
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $res = $conn->query("SELECT * FROM projects WHERE id=$id");
    if ($res && $res->num_rows > 0) {
        $editData = $res->fetch_assoc();
        $editing = true;
        $title = $editData['title'];
        $description = $editData['description'];
        $technologies = $editData['technologies'];
        $year = $editData['year'];
    }
}

// Handle add or update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $technologies = $_POST['technologies'];
    $year = $_POST['year'];

    if (isset($_POST['update']) && isset($_POST['id'])) {
        $id = intval($_POST['id']);
        $stmt = $conn->prepare("UPDATE projects SET title=?, description=?, technologies=?, year=? WHERE id=?");
        $stmt->bind_param("sssii", $title, $description, $technologies, $year, $id);
        $stmt->execute();
        $stmt->close();
        header("Location: ".$_SERVER['PHP_SELF']); // Redirect to avoid resubmission
        exit;
    } elseif (isset($_POST['add'])) {
        $stmt = $conn->prepare("INSERT INTO projects (title, description, technologies, year) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $title, $description, $technologies, $year);
        $stmt->execute();
        $stmt->close();
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM projects WHERE id=$id");
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// Fetch all projects
$projects = $conn->query("SELECT * FROM projects ORDER BY year DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Project Admin Panel</title>
    <style>
        body { font-family: Arial; margin: 40px; }
        table { width: 100%; border-collapse: collapse; margin-top: 30px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background-color: #f4f4f4; }
        input, textarea { width: 100%; padding: 8px; margin-top: 5px; }
        button { padding: 8px 16px; background-color: #007bff; color: white; border: none; cursor: pointer; margin-top: 10px; }
        button:hover { background-color: #0056b3; }
        .delete-btn { background: red; padding: 6px 10px; color: #fff; text-decoration: none; }
        .edit-btn { background: green; padding: 6px 10px; color: #fff; text-decoration: none; }
        .form-title { margin-top: 20px; }
    </style>
</head>
<body>

<h2 class="form-title"><?= $editing ? 'Edit Project' : 'Add New Project' ?></h2>

<form method="POST">
    <?php if ($editing): ?>
        <input type="hidden" name="id" value="<?= $editData['id'] ?>">
    <?php endif; ?>

    <label>Project Title</label>
    <input type="text" name="title" required value="<?= htmlspecialchars($title) ?>">

    <label>Description</label>
    <textarea name="description" rows="4" required><?= htmlspecialchars($description) ?></textarea>

    <label>Technologies</label>
    <input type="text" name="technologies" value="<?= htmlspecialchars($technologies) ?>">

    <label>Year</label>
    <input type="number" name="year" min="2000" max="2099" value="<?= htmlspecialchars($year) ?>">

    <button type="submit" name="<?= $editing ? 'update' : 'add' ?>">
        <?= $editing ? 'Update Project' : 'Add Project' ?>
    </button>

    <?php if ($editing): ?>
        <a href="<?= $_SERVER['PHP_SELF'] ?>" style="margin-left: 10px;">Cancel</a>
    <?php endif; ?>
</form>

<h3>Existing Projects</h3>
<table>
    <tr>
        <th>Title</th>
        <th>Description</th>
        <th>Technologies</th>
        <th>Year</th>
        <th>Action</th>
    </tr>
    <?php while($row = $projects->fetch_assoc()): ?>
    <tr>
        <td><?= htmlspecialchars($row['title']) ?></td>
        <td><?= nl2br(htmlspecialchars($row['description'])) ?></td>
        <td><?= htmlspecialchars($row['technologies']) ?></td>
        <td><?= $row['year'] ?></td>
        <td>
            <a class="edit-btn" href="?edit=<?= $row['id'] ?>">Edit</a>
            &nbsp;
            <a class="delete-btn" href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this project?')">Delete</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

</body>
</html>
