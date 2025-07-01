<?php
include'config.php'; // Include your configuration file
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
require 'phpmailer/src/Exception.php';
require 'database.php'; // Your DB connection file that sets $conn

// Fetch info from DB
$info = ['address' => 'N/A', 'email' => 'N/A', 'phone' => 'N/A'];
$sql = "SELECT address, email, phone FROM about LIMIT 1";
$res = $conn->query($sql);
if ($res && $res->num_rows > 0) {
    $info = $res->fetch_assoc();
}

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = htmlspecialchars(trim($_POST['name'] ?? ''));
    $email   = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $subject = htmlspecialchars(trim($_POST['subject'] ?? ''));
    $message = htmlspecialchars(trim($_POST['message'] ?? ''));

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
       $mail->Username   = EMAIL_USER;
$mail->Password   = EMAIL_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('rajkumarraya.cs@gmail.com', 'Portfolio Contact Form');
        $mail->addReplyTo($email, $name);
        $mail->addAddress('rajkumarraya.cs@gmail.com', 'Portfolio Owner');

        $mail->Subject = $subject;
        $mail->Body    = "Name: $name\nEmail: $email\n\nMessage:\n$message";
        $mail->AltBody = $mail->Body;

        $mail->send();
        $success = "Message sent successfully!";
    } catch (Exception $e) {
        $error = "Mailer Error: " . $mail->ErrorInfo;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Contact Me</title>

<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

<style>
  body {
   
    background: #f5f7fa;
    margin: 0; padding: 0;
    color: #333;
  }
  .container {
    max-width: 1100px;
    margin: 40px auto;
    background: #fff;
    box-shadow: 0 0 15px rgba(0,0,0,0.1);
    border-radius: 8px;
    display: flex;
    flex-wrap: nowrap;
    overflow: hidden;
  }
  .contact-form {
    flex: 0 0 70%;
    padding: 30px 40px;
    box-sizing: border-box;
  }
  .contact-info {
    flex: 0 0 30%;
    padding: 30px 40px;
    box-sizing: border-box;
    border-left: 1px solid #ddd;
    background-color: #f9faff;
  }
  h2 {
    margin-top: 0;
    color: #2c3e50;
    font-weight: 700;
    border-bottom: 3px solid #2980b9;
    padding-bottom: 10px;
    margin-bottom: 25px;
  }
  form label {
    font-weight: 600;
    display: block;
    margin-bottom: 6px;
  }
  form input[type="text"],
  form input[type="email"],
  form textarea {
    width: 100%;
    padding: 12px 15px;
    margin-bottom: 18px;
    border-radius: 5px;
    border: 1px solid #ccc;
    font-size: 1em;
    transition: border-color 0.3s;
    resize: vertical;
  }
  form input[type="text"]:focus,
  form input[type="email"]:focus,
  form textarea:focus {
    border-color: #2980b9;
    outline: none;
  }
  form button {
    background-color: #2980b9;
    color: white;
    font-weight: 700;
    border: none;
    padding: 14px 30px;
    cursor: pointer;
    border-radius: 5px;
    font-size: 1.1em;
    transition: background-color 0.3s;
  }
  form button:hover {
    background-color: #1c5980;
  }
  .message {
    margin-bottom: 20px;
    font-weight: 600;
  }
  .success {
    color: green;
  }
  .error {
    color: red;
  }

  /* Contact Info with icons and labels */
  .contact-info .info-item {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
  }
  .info-icon {
    color: #2980b9;
    font-size: 1.5em;
    width: 30px;
  }
  .info-content {
    margin-left: 12px;
  }
  .info-label {
    font-weight: 700;
    color: #2980b9;
    font-size: 1.05em;
  }
  .info-value {
    color: #444;
    font-size: 1em;
  }

  /* Social Media */
  .social-links {
    margin-top: 40px;
    font-weight: 600;
    color: #2980b9;
    font-size: 1.1em;
  }
  .social-links span {
    display: block;
    margin-bottom: 12px;
  }
  .social-links a {
    display: inline-block;
    text-decoration: none;
    margin-right: 20px;
    font-size: 1.8em;
    color: #2980b9;
    transition: color 0.3s;
  }
  .social-links a:hover {
    color: #1c5980;
  }

  /* Responsive */
  @media (max-width: 768px) {
    .container {
      flex-direction: column;
    }
    .contact-form, .contact-info {
      flex: 1 1 100%;
      padding: 25px;
      border-left: none;
    }
  }
</style>
</head>
<body>

  <div class="container">
    <!-- Contact Form -->
    <div class="contact-form">
      <h2>WRITE TO US </h2>
      <?php if ($success) echo "<p class='message success'>{$success}</p>"; ?>
      <?php if ($error) echo "<p class='message error'>{$error}</p>"; ?>

      <form method="POST" novalidate>
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" placeholder="Enter your full name" required />

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" placeholder="Enter your email address" required />

        <label for="subject">Subject:</label>
        <input type="text" id="subject" name="subject" placeholder="Subject of your message" required />

        <label for="message">Message:</label>
        <textarea id="message" name="message" rows="6" placeholder="Write your message here..." required></textarea>

        <button type="submit">Send</button>
      </form>
    </div>

    <!-- Contact Information -->
    <div class="contact-info">
      <h2>CONTACT</h2>

      <div class="info-item">
        <i class="fas fa-map-marker-alt info-icon" aria-hidden="true"></i>
        <div class="info-content">
          <div class="info-label">Address</div>
          <div class="info-value"><?=htmlspecialchars($info['address'])?></div>
        </div>
      </div>

      <div class="info-item">
        <i class="fas fa-envelope info-icon" aria-hidden="true"></i>
        <div class="info-content">
          <div class="info-label">Email Address</div>
          <div class="info-value"><?=htmlspecialchars($info['email'])?></div>
        </div>
      </div>

      <div class="info-item">
        <i class="fas fa-phone info-icon" aria-hidden="true"></i>
        <div class="info-content">
          <div class="info-label">Phone</div>
          <div class="info-value"><?=htmlspecialchars($info['phone'])?></div>
        </div>
      </div>

      <div class="social-links">
        <span>Follow me on social media:</span>
        <a href="https://www.facebook.com/gaming.razza" target="_blank" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
  <a href="https://github.com/Razza007" target="_blank" aria-label="GitHub">
  <i class="fab fa-github"></i>
</a>

        <a href="https://www.linkedin.com/in/rajkumar-raya/" target="_blank" aria-label="LinkedIn"><i class="fab fa-linkedin"></i></a>
        <a href="https://www.instagram.com/rajkumar_xetteri?igsh=MTF6N29kejVlNGd5OA==" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
      </div>
    </div>
  </div>

</body>
</html>
