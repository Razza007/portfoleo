<?php
session_start();
include '../database.php';
include 'dashbord.php';
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$message = '';

// Fetch existing about info (assumed single record with id=1)
$about = [
    'full_name' => '', 'ethnicity' => '', 'tagline' => '', 'description' => '',
    'signature_img' => '', 'age' => '', 'residence' => '', 'address' => '',
    'email' => '', 'phone' => '', 'freelance' => '', 'resume_link' => ''
];

$result = $conn->query("SELECT * FROM about WHERE id = 1");
if ($result && $row = $result->fetch_assoc()) {
    $about = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $ethnicity = trim($_POST['ethnicity']);
    $tagline = trim($_POST['tagline']);
    $description = trim($_POST['description']);
    $age = intval($_POST['age']);
    $residence = trim($_POST['residence']);
    $address = trim($_POST['address']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $freelance = trim($_POST['freelance']);
    
    // Keep current resume link unless new file uploaded
    $resume_link = $about['resume_link'];

    // Handle resume file upload
    if (!empty($_FILES['resume']['name'])) {
        $uploadDir = '../uploads/resumes/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $resumeName = time() . '_' . basename($_FILES['resume']['name']);
        $resumePath = $uploadDir . $resumeName;

        $fileType = mime_content_type($_FILES['resume']['tmp_name']);
        if ($fileType === 'application/pdf') {
            if (move_uploaded_file($_FILES['resume']['tmp_name'], $resumePath)) {
                // Save path relative to project root for use in links
                $resume_link = str_replace('../', '', $resumePath);
            } else {
                $message = "❌ Failed to upload resume.";
            }
        } else {
            $message = "❌ Only PDF files are allowed for resume.";
        }
    }

    // Validate required fields and email
    if ($full_name && $tagline && $description && filter_var($email, FILTER_VALIDATE_EMAIL) && empty($message)) {
        $stmt = $conn->prepare("UPDATE about SET full_name=?, ethnicity=?, tagline=?, description=?, age=?, residence=?, address=?, email=?, phone=?, freelance=?, resume_link=? WHERE id=1");
        $stmt->bind_param("ssssissssss", $full_name, $ethnicity, $tagline, $description, $age, $residence, $address, $email, $phone, $freelance, $resume_link);

        if ($stmt->execute()) {
            $message = "✅ About information updated successfully.";
            // Refresh $about with new data
            $about = [
                'full_name' => $full_name, 'ethnicity' => $ethnicity, 'tagline' => $tagline, 'description' => $description,
                'age' => $age, 'residence' => $residence, 'address' => $address, 'email' => $email,
                'phone' => $phone, 'freelance' => $freelance, 'resume_link' => $resume_link
            ];
        } else {
            $message = "❌ DB error: " . $conn->error;
        }
    } elseif (empty($message)) {
        $message = "❌ Please fill all required fields with valid data.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Update About Information</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2>Update About Information</h2>

    <?php if ($message): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" novalidate>
        <div class="mb-3">
            <label class="form-label">Full Name *</label>
            <input type="text" name="full_name" class="form-control" required value="<?= htmlspecialchars($about['full_name']) ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Ethnicity</label>
            <input type="text" name="ethnicity" class="form-control" value="<?= htmlspecialchars($about['ethnicity']) ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Tagline *</label>
            <input type="text" name="tagline" class="form-control" required value="<?= htmlspecialchars($about['tagline']) ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Description *</label>
            <textarea name="description" class="form-control" rows="6" required><?= htmlspecialchars($about['description']) ?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Age</label>
            <input type="number" name="age" class="form-control" value="<?= htmlspecialchars($about['age']) ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Residence</label>
            <input type="text" name="residence" class="form-control" value="<?= htmlspecialchars($about['residence']) ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Address</label>
            <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($about['address']) ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Email *</label>
            <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($about['email']) ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($about['phone']) ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Freelance Status</label>
            <input type="text" name="freelance" class="form-control" value="<?= htmlspecialchars($about['freelance']) ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Upload Resume (PDF)</label>
            <input type="file" name="resume" accept="application/pdf" class="form-control">
          
        </div>
        <button type="submit" class="btn btn-success">Save Changes</button>
    </form>
</div>
</body>
</html>
