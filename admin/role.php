<?php
include '../database.php'; // Connect to DB
include 'dashbord.php'; // Include admin dashboard layout
// Handle Add Role
if (isset($_POST['add_role'])) {
    $role_name = trim(mysqli_real_escape_string($conn, $_POST['role_name']));
    if ($role_name !== '') {
        $check = mysqli_query($conn, "SELECT * FROM roles WHERE role_name='$role_name'");
        if (mysqli_num_rows($check) == 0) {
            mysqli_query($conn, "INSERT INTO roles (role_name) VALUES ('$role_name')");
        } else {
            $error = "Role already exists.";
        }
    } else {
        $error = "Role name cannot be empty.";
    }
}

// Handle Edit Role
if (isset($_POST['edit_role'])) {
    $role_id = (int)$_POST['role_id'];
    $role_name = trim(mysqli_real_escape_string($conn, $_POST['role_name']));
    if ($role_name !== '') {
        mysqli_query($conn, "UPDATE roles SET role_name='$role_name' WHERE id=$role_id");
    } else {
        $error = "Role name cannot be empty.";
    }
}

// Handle Delete Role
if (isset($_GET['delete'])) {
    $role_id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM roles WHERE id=$role_id");
}

// Fetch all roles
$result = mysqli_query($conn, "SELECT * FROM roles ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Roles Admin Panel</title>
<style>
    body { font-family: Arial, sans-serif;  }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
    form { margin-top: 20px; }
    input[type=text] { padding: 5px; width: 300px; }
    button { padding: 5px 10px; }
    .error { color: red; }
</style>
</head>
<body>

<h2>Manage Roles</h2>

<?php if (!empty($error)): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<!-- Add New Role -->
<form method="post" action="">
    <label>Add New Role:</label><br>
    <input type="text" name="role_name" required>
    <button type="submit" name="add_role">Add Role</button>
</form>

<!-- Roles Table -->
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Role Name</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php while ($role = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?= $role['id'] ?></td>
            <td>
                <form method="post" action="" style="display:inline;">
                    <input type="hidden" name="role_id" value="<?= $role['id'] ?>">
                    <input type="text" name="role_name" value="<?= htmlspecialchars($role['role_name']) ?>" required>
                    <button type="submit" name="edit_role">Save</button>
                </form>
            </td>
            <td>
                <a href="?delete=<?= $role['id'] ?>" onclick="return confirm('Delete this role?')">Delete</a>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>

</body>
</html>
