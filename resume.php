<?php
include 'database.php';


// Fetch resume_link from about table
$resumeResult = $conn->query("SELECT resume_link FROM about WHERE id = 1");
$resumeLink = '';
if ($resumeResult && $row = $resumeResult->fetch_assoc()) {
    $resumeLink = $row['resume_link']; // e.g. 'uploads/resumes/123456_resume.pdf'
}

$educationRes = $conn->query("SELECT * FROM education ORDER BY start_year DESC");
$experienceRes = $conn->query("SELECT * FROM experience ORDER BY start_year DESC");
$skillsRes = $conn->query("SELECT * FROM skills ORDER BY category, skill_name");
$certificationsRes = $conn->query("SELECT * FROM certifications ORDER BY issue_year DESC");
$projectsRes = $conn->query("SELECT * FROM projects ORDER BY year DESC");

$skillsByCategory = [];
while ($row = $skillsRes->fetch_assoc()) {
    $skillsByCategory[$row['category']][] = $row['skill_name'];
}

$technicalCategories = ['Frontend', 'Backend'];
$technicalSkills = [];
foreach ($technicalCategories as $cat) {
    $technicalSkills[$cat] = $skillsByCategory[$cat] ?? [];
    unset($skillsByCategory[$cat]);
}

$softSkills = $skillsByCategory['Soft Skills'] ?? [];
unset($skillsByCategory['Soft Skills']);

$otherCategories = $skillsByCategory;
?>

<style>
    body {
      margin: 0; padding: 0;
      font-family: 'Segoe UI', sans-serif;
      background-color: #fff;
      color: #222;
    }
    .resume-header {
      text-align: center;
      padding: 60px 20px 40px;
      background: #f8f9fa;
     border-bottom: 5px solid #e6e6e6;
    }
    .resume-header h1 {
      font-size: 2.8rem;
      color: #0056b3;
      margin: 0;
    }
    .resume-header p {
      font-size: 1.1rem;
      color: #666;
      margin-top: 10px;
    }
    .section {
      
      border-bottom: 5px solid #e6e6e6;
    
    margin-top: 30px
    }
    .section h2 {
      font-size: 1.8rem;
      color: #003366;
      margin-bottom: 30px;
      border-left: 5px solid #0077cc;
      padding-left: 15px;
      background: linear-gradient(to right, #f0f4f8, transparent);
      padding-top: 8px;
    }
    .item {
      margin-bottom: 25px;
      padding: 15px;
      background-color: #f9f9f9;
      border-radius: 6px;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }
    .item h3 {
      font-size: 1.2rem;
      margin: 10px 0 5px;
      color: #0073e6;
    }
    small {
      color: #777;
      font-style: italic;
      display: block;
      margin-bottom: 8px;
    }
    p {
      line-height: 1.6;
      white-space: pre-line;
      color: #444;
    }
    .skills-list {
      list-style: none;
      padding: 0;
      margin: 0 0 30px 0;
    }
    .skills-list li {
      display: inline-block;
      background-color: #f0f4fb;
      color: #0056b3;
      padding: 6px 12px;
      margin: 5px 6px 5px 0;
      border-radius: 3px;
      font-weight: 500;
    }
    .skills-flex {
      display: flex;
      flex-wrap: wrap;
      gap: 40px;
    }
    .skills-left, .skills-right {
      flex: 1 1 300px;
    }
    .skills-right {
      border-left: 2px solid #e0e0e0;
      padding-left: 30px;
    }
    .download-resume {
      display: inline-block;
      margin-top: 5px;
      padding: 10px 24px;
      border: 2px solid #0056b3;
      color: #0056b3;
      border-radius: 30px;
      text-decoration: none;
      font-weight: bold;
      transition: 0.3s;
    }
    .download-resume:hover {
      background-color: #0056b3;
      color: #fff;
    }
    @media (max-width: 768px) {
      .resume-header h1 {
        font-size: 2.2rem;
      }
      .section {
        padding: 30px 15px;
      }
      .skills-flex {
        flex-direction: column;
      }
      .skills-right {
        border-left: none;
        padding-left: 0;
      }
    }
    .soft-skills-list {
      list-style-type: none;
      counter-reset: softskill-counter;
      padding-left: 20px;
      margin: 0;
    }
    .soft-skills-list li {
      position: relative;
      margin-bottom: 8px;
      padding-left: 30px;
      font-weight: 500;
      color: #0056b3;
    }
    .soft-skills-list li::before {
      counter-increment: softskill-counter;
      content: counter(softskill-counter) ".";
      position: absolute;
      left: 0;
      top: 0;
      color: #0056b3;
      font-weight: bold;
    }
</style>

<header class="resume-header">
  <h1>Resume</h1>
  <p>My professional background, skills, and achievements</p>
</header>

<!-- Education -->
<div class="section" id="education">
  <h2>Education</h2>
  <?php while ($edu = $educationRes->fetch_assoc()): ?>
    <div class="item" style="margin-bottom: 20px;">
      <!-- Faculty / Degree -->
      <h3 style="margin-bottom: 5px;"><?= htmlspecialchars($edu['degree']) ?></h3>

      <!-- Institution and Address -->
      <div style="font-size: 0.9em; color: #555; margin-bottom: 5px;">
        <strong><?= htmlspecialchars($edu['institution']) ?></strong>
        <span style="font-style: italic; color: #888;"> — <?= htmlspecialchars($edu['address']) ?></span>
      </div>

      <!-- Year -->
      <small style="display: block; margin-bottom: 5px; color: #666;">
        <?= $edu['start_year'] ?> - <?= $edu['end_year'] ?: 'Present' ?>
      </small>

      <!-- Description -->
      <p style="margin: 0;"><?= nl2br(htmlspecialchars($edu['description'])) ?></p>
    </div>
  <?php endwhile; ?>
</div>


<!-- Experience -->
<div class="section" id="experience">
  <h2>Experience</h2>
  <?php while ($exp = $experienceRes->fetch_assoc()): ?>
    <div class="item" style="margin-bottom: 20px;">
      <!-- Job Title -->
      <h3 style="margin-bottom: 5px;"><?= htmlspecialchars($exp['job_title']) ?></h3>

      <!-- Company and Address -->
      <div style="font-size: 0.9em; color: #555; margin-bottom: 5px;">
        <strong><?= htmlspecialchars($exp['company']) ?></strong>
        <span style="font-style: italic; color: #888;"> — <?= htmlspecialchars($exp['address']) ?></span>
      </div>

      <!-- Period -->
      <small style="display: block; margin-bottom: 5px; color: #666;">
        <?= $exp['start_year'] ?> - <?= $exp['end_year'] ?: 'Present' ?>
      </small>

      <!-- Description -->
      <p style="margin: 0;"><?= nl2br(htmlspecialchars($exp['description'])) ?></p>
    </div>
  <?php endwhile; ?>
</div>

<!-- Make sure Bootstrap CSS is included in your <head> -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

<div class="section" id="skills">
  <h2> Skills</h2>
  <div class="row">
    <div class="col-md-8 skills-left">
      <h3> Technical Skills</h3>
      <?php foreach ($technicalSkills as $techCategory => $skills): ?>
        <?php if ($skills): ?>
          <h4 class="text-primary mt-3 mb-2"><?= htmlspecialchars($techCategory) ?></h4>
          <ul class="list-unstyled d-flex flex-wrap gap-2 p-0">
            <?php foreach ($skills as $skill): ?>
              <li>
                <span class="badge bg-info text-dark rounded-pill px-3 py-2 fw-semibold">
                  <?= htmlspecialchars($skill) ?>
                </span>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      <?php endforeach; ?>

      <?php foreach ($otherCategories as $cat => $skills): ?>
        <h4 class="text-primary mt-3 mb-2"><?= htmlspecialchars($cat) ?></h4>
        <ul class="list-unstyled d-flex flex-wrap gap-2 p-0">
          <?php foreach ($skills as $skill): ?>
            <li>
              <span class="badge bg-info text-dark rounded-pill px-3 py-2 fw-semibold">
                <?= htmlspecialchars($skill) ?>
              </span>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endforeach; ?>
    </div>

    <div class="col-md-4 skills-right">
      <h3> Soft Skills</h3>
      <ol class="soft-skills-list list-unstyled">
        <?php foreach ($softSkills as $skill): ?>
          <li><?= htmlspecialchars($skill) ?></li>
        <?php endforeach; ?>
      </ol>
    </div>
  </div>
</div>


<!-- Projects -->
<div class="section" id="projects">
  <h2> Projects</h2>
  <?php while ($project = $projectsRes->fetch_assoc()): ?>
    <div class="item">
      <h3><?= htmlspecialchars($project['title']) ?><?php if ($project['year']) echo ' — ' . $project['year']; ?></h3>
      <small><?= htmlspecialchars($project['technologies']) ?></small>
      <p><?= nl2br(htmlspecialchars($project['description'])) ?></p>
    </div>
  <?php endwhile; ?>
</div>

<!-- Certifications -->
<div class="section" id="certifications">
  <h2> Certifications</h2>
  <?php while ($cert = $certificationsRes->fetch_assoc()): ?>
    <div class="item">
      <h3><?= htmlspecialchars($cert['title']) ?></h3>
      <small><?= htmlspecialchars($cert['issuer']) ?> — <?= $cert['issue_year'] ?></small>
    </div>
  <?php endwhile; ?>
</div>

<!-- Resume Download -->
<div class="section" style="text-align: center;">
  <?php if ($resumeLink): ?>
  <a href="<?= htmlspecialchars($resumeLink) ?>" class="download-resume" download>⬇️ Download Resume (PDF)</a>
<?php else: ?>
  <p>No resume uploaded yet.</p>
<?php endif; ?>

</div>
