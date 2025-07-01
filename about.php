<?php
include 'database.php'; // Include your database connection

// Fetch resume_link from about table
$resumeResult = $conn->query("SELECT resume_link FROM about WHERE id = 1");
$resumeLink = '';
if ($resumeResult && $row = $resumeResult->fetch_assoc()) {
    $resumeLink = $row['resume_link']; // e.g. 'uploads/resumes/123456_resume.pdf'
}

// Assuming $conn is your MySQL connection

// Fetch about info
$about_res = mysqli_query($conn, "SELECT * FROM about WHERE id = 1");
$about = mysqli_fetch_assoc($about_res);

// Fetch services
$services_res = mysqli_query($conn, "SELECT * FROM services ORDER BY id ASC");

// Fetch clients
$clients_res = mysqli_query($conn, "SELECT * FROM clients ORDER BY id ASC");
?>

<style>
/* Container for About + Sidebar */
.about-main-wrapper {
  display: flex;
  gap: 2rem;
  max-width: 1100px;
  margin: 3rem auto 2rem;
  padding: 0 1rem;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  color: #222;
}

/* Left side: About text */
.about-left {
  flex: 2;
}

.about-title {
  font-size: 2.8rem;
  margin-bottom: 1rem;
  color:  #0056b3;
}

.about-greeting {
  font-size: 1.2rem;
  margin-bottom: 1.5rem;
  color: #555;
  font-style: italic;
}

.about-description p {
  line-height: 1.7;
  margin-bottom: 1.2rem;
  font-size: 1.1rem;
}

/* Signature */
.signature-container {
  margin-top: 2.5rem;
  display: flex;
  align-items: center;
  gap: 1rem;
}

.signature-img {
  max-height: 80px;
  width: auto;
  user-select: none;
}

.signature-text {
  font-family: 'Brush Script MT', cursive;
  font-size: 2rem;
  color: #0056b3;
  user-select: none;
}

/* Right side: Personal Info sidebar */
.about-right {
  flex: 1;
  position: sticky;
  top: 100px;
  align-self: flex-start;
  background: #f9f9f9;
  padding: 1.8rem 1.5rem;
  border-radius: 12px;
  box-shadow: 0 0 15px rgba(0,0,0,0.05);
  height: fit-content;
}

.personal-info h3 {
  margin-bottom: 1rem;
  color: #0056b3;
  border-bottom: 2px solid #0056b3;
  padding-bottom: 0.3rem;
}

.personal-info ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.personal-info ul li {
  margin-bottom: 0.7rem;
  font-weight: 500;
}

.btn-resume {
  display: inline-block;
  margin-top: 1.5rem;
  background: #0056b3;
  color: white;
  padding: 0.7rem 1.5rem;
  border-radius: 6px;
  text-decoration: none;
  box-shadow: 0 0 10px rgba(40,167,69,0.4);
  transition: background 0.3s ease;
}

.btn-resume:hover {
  background: #0056b3;
}

/* Services Section */
.services-and-clients {
  max-width: 1100px;
  margin: 0 auto 4rem;
  padding: 0 1rem;
}

.services {
  margin-top: 2rem;
}

.services h3 {
  color: #333;
  margin-bottom: 1rem;
  border-bottom: 2px solid #333;
  padding-bottom: 0.3rem;
}

.service-list {
  display: flex;
  flex-wrap: wrap;
  gap: 1.5rem;
}

.service-item {
  flex: 1 1 250px;
  background: #e9f5e9;
  border-radius: 10px;
  padding: 1.5rem 1.2rem 2rem;
  box-shadow: 0 0 8px rgba(40,167,69,0.2);
  color: #0056b3;
  text-align: center;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
}

.service-icon {
  width: 60px;
  height: 60px;
  object-fit: contain;
}

.service-title {
  font-weight: 700;
  font-size: 1.2rem;
}

.service-desc {
  font-weight: 500;
  font-size: 1rem;
  line-height: 1.4;
  color:rgb(24, 67, 112);
}

/* Clients Section */
.clients {
  margin-top: 3rem;
}

.clients h3 {
  color: #333;
  margin-bottom: 1rem;
  border-bottom: 2px solid #333;
  padding-bottom: 0.3rem;
}

.client-logos {
  display: flex;
  
  flex-wrap: wrap;
  gap: 5rem;
  justify-content: flex-start;
  align-items: center;
}

.client-logo img {
  max-height: 150px;
  max-width: 120px;
  object-fit: contain;
  
}



/* Responsive */
@media(max-width: 900px) {
  .about-main-wrapper {
    flex-direction: column;
  }
  .about-right {
    position: relative;
    top: auto;
    margin-top: 2rem;
  }
}
</style>

<div class="about-main-wrapper">
  <div class="about-left">
    <h2 class="about-title">About Me</h2>
    <p class="about-greeting">
      Hello, I'm <?= htmlspecialchars($about['full_name']) ?> 
      (<?= htmlspecialchars($about['ethnicity'] ?? '') ?>), 
      <?= htmlspecialchars($about['tagline']) ?>.
    </p>

    <div class="about-description">
      <?php 
        $paras = preg_split('/\r\n|\r|\n/', $about['description']);
        foreach ($paras as $p) {
          echo "<p>" . htmlspecialchars($p) . "</p>";
        }
      ?>
    </div>

    <?php if (!empty($about['signature_img'])): ?>
    <div class="signature-container">
      <img src="<?= htmlspecialchars($about['signature_img']) ?>" alt="Signature" class="signature-img" />
    
      
    </div>
    <div class="signature-text"><?= htmlspecialchars($about['full_name']) ?></div>
    <?php endif; ?>
  </div>

  <aside class="about-right">
    <div class="personal-info">
      <h3>Personal Information</h3>
      <ul>
        <?php foreach ([
          'full_name' => 'Name', 
          'age' => 'Age', 
          'residence' => 'Residence', 
          'address' => 'Address', 
          'email' => 'Email', 
          'phone' => 'Phone', 
          'freelance' => 'Freelance'
          ] as $key => $label): ?>
          <?php if (!empty($about[$key])): ?>
            <li><strong><?= $label ?>:</strong> <?= htmlspecialchars($about[$key]) ?></li>
          <?php endif; ?>
        <?php endforeach; ?>
      </ul>
<?php if ($resumeLink): ?>
  <a href="<?= htmlspecialchars($resumeLink) ?>" class="btn-resume" download>Download Resume (PDF)</a>
<?php else: ?>
  <p>No resume uploaded yet.</p>
<?php endif; ?>
   
    </div>
  </aside>
</div>

<div class="services-and-clients">
  <!-- Services Section -->
  <div class="services">
    <h3>Services</h3>
    <div class="service-list">
      <?php while($service = mysqli_fetch_assoc($services_res)): ?>
        <div class="service-item">
          <?php if (!empty($service['icon_url'])): ?>
            <img src="<?= htmlspecialchars($service['icon_url']) ?>" alt="<?= htmlspecialchars($service['title']) ?>" class="service-icon" />
          <?php endif; ?>
          <div class="service-title"><?= htmlspecialchars($service['title']) ?></div>
          <div class="service-desc"><?= htmlspecialchars($service['description']) ?></div>
        </div>
      <?php endwhile; ?>
    </div>
  </div>

  <!-- Clients Section -->
  <div class="clients">
    <h3>Clients</h3>
    <div class="client-logos">
      <?php while($client = mysqli_fetch_assoc($clients_res)): ?>
        <div class="client-logo">
          <img src="<?= htmlspecialchars($client['logo_url']) ?>" alt="<?= htmlspecialchars($client['name']) ?>">
        </div>
      <?php endwhile; ?>
    </div>
  </div>
</div>
