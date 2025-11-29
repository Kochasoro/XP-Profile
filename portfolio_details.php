<?php
$portfolioId = isset($_GET['id']) ? intval($_GET['id']) : null;

if (!$portfolioId) {
    header("Location: error.php?message=No portfolio ID provided");
    exit;
}

function fetchApiData($endpoint) {
    $apiUrl = "http://localhost/JsonProj/api/" . $endpoint;
    
    try {
        $context = stream_context_create([
            'http' => [
                'ignore_errors' => true,
                'timeout' => 10 
            ]
        ]);
        
        $response = file_get_contents($apiUrl, false, $context);
        
        if ($response === FALSE) {
            throw new Exception("Failed to fetch data from API endpoint: " . $endpoint);
        }
        
        $data = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Invalid JSON response from API endpoint: " . $endpoint);
        }
        
        return $data;
    } catch (Exception $e) {
        error_log($e->getMessage());
        return ['error' => $e->getMessage()];
    }
}

$apiEndpoints = [
    'portfolio' => 'Portfolio_api.php',
    'users' => 'User_api.php',
    'skills' => 'Skills_api.php',
    'experience' => 'Experience_api.php',
    'education' => 'Education_api.php',
    'projects' => 'Projects_api.php'
];

$portfolioData = fetchApiData($apiEndpoints['portfolio'] . "?id=$portfolioId");
$usersData = fetchApiData($apiEndpoints['users']);
$skillsData = fetchApiData($apiEndpoints['skills']);
$experienceData = fetchApiData($apiEndpoints['experience']);
$educationData = fetchApiData($apiEndpoints['education']);
$projectsData = fetchApiData($apiEndpoints['projects']);

function isValidResponse($data, $key = null) {
    if (isset($data['error'])) {
        return false;
    }
    return $key ? isset($data[$key]) : true;
}

$portfolio = null;
$owner = null;
$skills = [];
$experiences = [];
$educations = [];
$projects = [];
if (isValidResponse($portfolioData, 'portfolio')) {
    $portfolio = $portfolioData['portfolio'];
}


if ($portfolio) {
    if (isValidResponse($usersData, 'users')) {
        foreach ($usersData['users'] as $user) {
            if ($user['id'] == $portfolio['user_id']) {
                $owner = $user;
                break;
            }
        }
    }

    if (isValidResponse($skillsData, 'skills')) {
        $skills = array_filter($skillsData['skills'], function($skill) use ($portfolio) {
            return isset($skill['portfolio_id']) && $skill['portfolio_id'] == $portfolio['id'];
        });
    }

    if (isValidResponse($experienceData, 'experiences')) {
        $experiences = array_filter($experienceData['experiences'], function($exp) use ($portfolio) {
            return isset($exp['portfolio_id']) && $exp['portfolio_id'] == $portfolio['id'];
        });
    }

    if (isValidResponse($educationData, 'education')) {
        $educations = array_filter($educationData['education'], function($edu) use ($portfolio) {
            return isset($edu['portfolio_id']) && $edu['portfolio_id'] == $portfolio['id'];
        });
    }

    if (isValidResponse($projectsData, 'projects')) {
        $projects = array_filter($projectsData['projects'], function($proj) use ($portfolio) {
            return isset($proj['portfolio_id']) && $proj['portfolio_id'] == $portfolio['id'];
        });
    }
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= html_entity_decode($portfolio['title']) ?> | Portfolio</title>
  <link rel="stylesheet" href="styles.css" />
</head>
<body>
<div class="page-wrapper">    
<button id="darkModeToggle" class="dark-mode-toggle">
  🌙
</button>

    <nav class="side-nav">
        <ul>
            <li>
            <a href="#about" class="nav-link active" title="About">
                <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M5.121 17.804A9 9 0 1117.805 5.121 9 9 0 015.12 17.804z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 16v-4m0-4h.01" />
                </svg>
                <span class="link-text">About</span>
            </a>
            </li>
            <li>
            <a href="#education" class="nav-link" title="Education">
                <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 14l9-5-9-5-9 5 9 5z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 14l6.16-3.422A12.083 12.083 0 0118 20H6a12.083 12.083 0 01-.16-9.422L12 14z" />
                </svg>
                <span class="link-text">Education</span>
            </a>
            </li>
            <li>
                <a href="#experience" class="nav-link" title="Experience">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 7h16M4 7v10a2 2 0 002 2h12a2 2 0 002-2V7M16 3v4M8 3v4m4 4h.01" />
                    </svg>
                    <span class="link-text">Experience</span>
                </a>
            </li>

            <li>
            <a href="#skills" class="nav-link" title="Skills">
                <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9.75 17L5.25 12.5 9.75 8m4.5 9L18.75 12.5 14.25 8" />
                </svg>
                <span class="link-text">Skills</span>
            </a>
            </li>
            <li>
            <a href="#projects" class="nav-link" title="Projects">
                <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 7h18M3 12h18M3 17h18" />
                </svg>
                <span class="link-text">Projects</span>
            </a>
            </li>
        </ul>
    </nav>


  <main>
    <!-- ABOUT SECTION -->
    <section id="about" class="section">
        <div class="profile-header">
            <p><strong><?= htmlspecialchars($portfolio['title']) ?></strong></p>

            <img src="<?= !empty($portfolio['profile_image']) ? 'api/' . htmlspecialchars($portfolio['profile_image']) : 'resources/images/default.jpg' ?>" 
                alt="Profile Image" class="profile-image">

            <div class="profile-info">
            <h1>
                <?= $owner ? htmlspecialchars($owner['firstname'] . ' ' . $owner['middlename'] . ' ' . $owner['lastname']) : 'Unknown User' ?>
            </h1>

            <?php if (!empty($portfolio['summary'])): ?>
                <p class="italic"><?= htmlspecialchars($portfolio['summary']) ?></p>
            <?php endif; ?>

            <?php if (!empty($owner['mobile'])): ?>
                <p><strong>Contact:</strong> <?= htmlspecialchars($owner['mobile']) ?></p>
            <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($portfolio['description'])): ?>
            <div class="bio-description">
            <p><?= nl2br(htmlspecialchars($portfolio['description'])) ?></p>
            </div>
        <?php endif; ?>
    </section>


    <!-- EDUCATION SECTION -->
    <section id="education" class="section">
        <h2>Education</h2>
        <?php if (!empty($educations)): ?>
            <div class="timeline">
            <?php foreach ($educations as $edu): ?>
            <div class="timeline-item">
                <div class="timeline-dot"></div>
                <div class="timeline-content">
                <h3><?= htmlspecialchars($edu['degree']) ?> at <?= htmlspecialchars($edu['school_name']) ?></h3>
                <span class="education-date">
                    <?= date('M Y', strtotime($edu['start_year'])) ?> -
                    <?= $edu['end_year'] ? date('M Y', strtotime($edu['end_year'])) : 'Present' ?>
                </span>
                <?php if (!empty($edu['description'])): ?>
                <p><?= nl2br(htmlspecialchars($edu['description'])) ?></p>
                <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>No education details provided.</p>
        <?php endif; ?>
    </section>


    <section id="experience" class="section">
        <h2>Experience</h2>
        <?php if (!empty($experiences)): ?>
            <?php foreach ($experiences as $exp): ?>
            <div class="experience-entry">
                <h3><?= htmlspecialchars($exp['job_title']) ?> at <?= htmlspecialchars($exp['company_name']) ?></h3>
                <span><?= date('M Y', strtotime($exp['start_date'])) ?> - 
                        <?= $exp['end_date'] ? date('M Y', strtotime($exp['end_date'])) : 'Present' ?></span>
                <?php if (!empty($exp['description'])): ?>
                <p><?= nl2br(htmlspecialchars($exp['description'])) ?></p>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No experience added yet.</p>
        <?php endif; ?>
    </section>


    <!-- SKILLS SECTION -->
    <section id="skills" class="section">
        <h2>Skills</h2>
        <?php foreach ($skills as $skill): ?>
        <div class="progress-box">
            <span>
            <?= htmlspecialchars($skill['skill_name']) ?>
            <span><?= intval($skill['proficiency']) ?>%</span>
            </span>
            <div class="progress-bar">
            <div style="width: <?= intval($skill['proficiency']) ?>%;"></div>
            </div>
        </div>
        <?php endforeach; ?>
    </section>





    <!-- PROJECTS SECTION -->
    <section id="projects" class="section">
        <h2>Projects</h2>
        <?php if (!empty($projects)): ?>
        <div class="carousel-container">
            <button class="carousel-btn prev">◀</button>
            <div class="carousel-track">
            <?php foreach ($projects as $proj): ?>
            <div class="carousel-item">
                <div class="app-card">
                <img src="api/<?= htmlspecialchars($proj['image_url'] ?? 'resources/images/placeholder.png') ?>" alt="Project Image" class="img-fluid">
                <h3><?= htmlspecialchars($proj['project_name']) ?></h3>
                <?php if (!empty($proj['project_url'])): ?>
                <p><a href="<?= htmlspecialchars($proj['project_url']) ?>" target="_blank" class="btn">View Project</a></p>
                <?php endif; ?>
                <?php if (!empty($proj['description'])): ?>
                <p><?= nl2br(html_entity_decode($proj['description'])) ?></p>
                <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
            </div>
            <button class="carousel-btn next">▶</button>
        </div>
        <?php else: ?>
        <p>No projects to display.</p>
        <?php endif; ?>
    </section>



  </main>
</div>

<script src="script.js"></script>
</body>
</html>
