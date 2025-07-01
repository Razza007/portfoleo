<?php

include 'database.php'; // Include your database connection


$res = $conn->query("SELECT background_image FROM homepage_settings WHERE id = 1");
$row = $res->fetch_assoc();


$bgImage = 'upload/homepage/../' . $row['background_image'];
   


 



// Fetch user data
$sql = "SELECT * FROM users LIMIT 1";
$res = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($res);

// Fetch about data
$about_res = mysqli_query($conn, "SELECT * FROM about WHERE id = 1");
$about = mysqli_fetch_assoc($about_res);

// Fetch roles for typing effect
$roles_res = mysqli_query($conn, "SELECT role_name FROM roles");
$roles = [];
while ($row = mysqli_fetch_assoc($roles_res)) {
    $roles[] = $row['role_name'];
}
$roles_json = json_encode($roles);

// Get max role length for width reservation
$maxRoleLength = 0;
foreach ($roles as $role) {
    if (strlen($role) > $maxRoleLength) $maxRoleLength = strlen($role);
}
?>

<style>
.home-background {
  position: fixed;
  inset: 0;
background: url('<?= $bgImage ?>') no-repeat center center fixed;


  /* background-image: url('images/2.jpg'); */
  background-size: cover;
  background-position: center;
  filter: brightness(0.55);
  z-index: -1;
}

.home-container {
  min-height: 80vh;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  text-align: center;
  padding: 2rem 1rem;
  color: #f5f5f5;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  text-shadow: 0 0 10px rgba(0,0,0,0.8);
}

.home-photo {
  width: 180px;
  height: 180px;
  border-radius: 50%;
  border: 4px solid #fff;
  object-fit: cover;
  margin-bottom: 2rem;
  box-shadow: 0 6px 20px rgba(0,0,0,0.7);
  transition: transform 0.3s ease;
}
.home-photo:hover {
  transform: scale(1.05);
}

.home-name {
  font-size: 3rem;
  font-weight: 700;
  margin-bottom: 0.25rem;
  letter-spacing: 1.2px;
  color: #fff;
}

/* Role wrapper */
.home-role {
  margin-bottom: 2rem;
  user-select: none;
  font-size: 2rem;
  white-space: nowrap;
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 3.3rem;
  line-height: 3.3rem;
}

/* Fixed part */
#static-text {
  font-size: 1.7rem;
  color: white;
  font-weight: 600;
  margin-right: 10px;
}

/* Dynamic role */
#role {
  font-size: 2.3rem;
  font-weight: 800;
  color: #0056b3;
  min-width: calc(0.65ch * <?= $maxRoleLength ?>);
  overflow: hidden;
  white-space: nowrap;
}

/* Blinking cursor */


@keyframes blink {
  0%, 50% { opacity: 1; }
  51%, 100% { opacity: 0; }
}

.home-description {
  max-width: 650px;
  font-size: 1.15rem;
  line-height: 1.7;
  color: #ddd;
  margin-bottom: 3rem;
  font-weight: 400;
}


</style>

<div class="home-background"></div>

<div class="home-container">


  <h1 class="home-name">Hello, I'm <?= htmlspecialchars($user['username'] ?? 'Your Name') ?></h1>

  <div class="home-role">
    <span id="static-text">I am a</span>
    <div>
      <span id="role"></span><span id="cursor"></span>
    </div>
  </div>

 

  </div>

<script>
const roles = <?= $roles_json ?>;
const roleEl = document.getElementById('role');

let roleIndex = 0;
let charIndex = 0;
let typing = true;
let currentText = '';
const typingSpeed = 100;
const deletingSpeed = 50;
const delayBetweenRoles = 1500;

function type() {
  if (typing) {
    if (charIndex < roles[roleIndex].length) {
      currentText += roles[roleIndex][charIndex];
      roleEl.textContent = currentText;
      charIndex++;
      setTimeout(type, typingSpeed);
    } else {
      typing = false;
      setTimeout(type, delayBetweenRoles);
    }
  } else {
    if (charIndex > 0) {
      currentText = currentText.slice(0, -1);
      roleEl.textContent = currentText;
      charIndex--;
      setTimeout(type, deletingSpeed);
    } else {
      typing = true;
      roleIndex = (roleIndex + 1) % roles.length;
      setTimeout(type, typingSpeed);
    }
  }
}
type();
</script>
