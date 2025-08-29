<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>About Us - RBTkaFiles</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap');

    .custom-navbar {
      background: rgba(0, 0, 0, 0.9);
      padding: 15px 30px;
      box-shadow: 0 4px 10px rgba(0, 255, 255, 0.2);
      transition: background 0.3s ease-in-out;
    }
    .custom-navbar:hover {
      background: rgba(0, 0, 0, 1);
    }
    .glitch-text {
      font-family: 'Orbitron', sans-serif;
      font-size: 24px;
      font-weight: 700;
      color: #0ff;
      position: relative;
    }
    @keyframes glitch {
      0% {
        text-shadow: -2px -2px 0px rgba(255, 0, 0, 0.8),
          2px 2px 0px rgba(0, 255, 0, 0.8);
      }
      50% {
        text-shadow: 2px -2px 0px rgba(255, 0, 0, 0.8),
          -2px 2px 0px rgba(0, 255, 0, 0.8);
      }
      100% {
        text-shadow: -2px 2px 0px rgba(255, 0, 0, 0.8),
          2px -2px 0px rgba(0, 255, 0, 0.8);
      }
    }
    .glitch-text:hover {
      color: #ff4b2b;
      text-shadow: 0 0 12px rgba(255, 75, 75, 0.9);
      transition: color 0.3s ease-in-out;
    }
    body {
      background: linear-gradient(to right, #e3f2fd, #e3f2fd);
    }
    :root {
      --primary-color: #007bff;
      --secondary-color: #28a745;
      --background-color: #f8f9fa;
      --text-color: #2c3e50;
    }
    body {
      font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
      background-color: var(--background-color);
      color: var(--text-color);
      min-height: 100vh;
      padding: 2rem;
    }
    .main-container {
      max-width: 1200px;
      background: white;
      border-radius: 1.25rem;
      box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.1);
      margin: 2rem auto;
      padding: 2.5rem;
      opacity: 0;
      transform: translateY(20px);
      animation: fadeIn 0.5s ease-out forwards;
    }
    @keyframes fadeIn {
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    .title {
      font-size: 2.5rem;
      font-weight: 700;
      color: var(--primary-color);
      margin-bottom: 2rem;
      position: relative;
      padding-bottom: 0.5rem;
    }
    .title::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 50%;
      transform: translateX(-50%);
      width: 4rem;
      height: 3px;
      background: var(--secondary-color);
    }
    .metrics-card {
      background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
      border-radius: 0.75rem;
      padding: 1.5rem;
      margin: 2rem auto;
      width: fit-content;
      box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05);
    }
    .metric-row {
      display: flex;
      gap: 2rem;
      justify-content: center;
      flex-wrap: wrap;
    }
    .metric-tile {
      min-width: 120px;
    }
    .source-code-wrapper {
      background: #1e1e1e;
      border-radius: 0.75rem;
      padding: 1.5rem;
      position: relative;
      margin: 2rem 0;
    }
    .source-code-container {
      max-height: 60vh;
      overflow: auto;
      font-family: 'Fira Code', 'Courier New', monospace;
      font-size: 0.875rem;
      line-height: 1.6;
      color: #d4d4d4;
      scrollbar-width: thin;
      scrollbar-color: #555 #333;
    }
    .copy-btn {
      position: absolute;
      top: 1rem;
      right: 1rem;
      background: var(--primary-color);
      color: white;
      border: none;
      padding: 0.5rem 1rem;
      border-radius: 0.5rem;
      transition: all 0.2s ease;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    .copy-btn:hover {
      background: #0056b3;
      transform: translateY(-2px);
    }
    .copy-btn:active {
      transform: translateY(0);
    }
    .toast {
      position: fixed;
      bottom: 1rem;
      right: 1rem;
      background: var(--secondary-color);
      color: white;
      padding: 1rem 2rem;
      border-radius: 0.5rem;
      display: none;
      animation: slideIn 0.3s ease-out;
    }
    @media (max-width: 768px) {
      body {
        padding: 1rem;
      }
      .main-container {
        padding: 1.5rem;
        margin: 1rem auto;
      }
      .title {
        font-size: 2rem;
      }
    }
  </style>
</head>
<body>
 
<?php
/**
 * About Page Template
 */
$this->assign('title', 'About - RBTkaFiles');
?>

<main class="main-container">
    <h1 class="title text-center">Why RBTkaFiles?</h1>

    <div class="lead text-center mb-5">
        <p class="mb-4">
            Sharing files through WhatsApp or Onedrive was too tiring as we always have to
            login. And sometimes even forget to logout.
        </p>
        <p class="mb-4">
            <em>
                Not to mention the unfortunate incident Anish experienced when he received an
                email from his own account to his inbox. This happened because he forgot to log
                out, and some menace decided to troll the poor guy in this manner.
            </em>
        </p>
        <p class="text-muted small">
            *Some sources say the troll was Richie but Nahhh that cant be true<br />
            <span class="text-danger">**It infact was true</span>
        </p>
    </div>

    <!-- Metrics Card -->
    <div class="metrics-card text-center">
        <div class="h5 mb-3">
            <i class="fas fa-chart-line me-2"></i>Site Analytics
        </div>
        <?php
        // Get total files count from database
        $totalFiles = $this->getRequest()->getAttribute('fileCount');
        
        // Get print statistics from users table
        $printStats = $this->getRequest()->getAttribute('printStats');
        $totalPrints = $printStats['totalPrints'] ?? 0;
        $topPrinters = $printStats['topPrinters'] ?? [];
        
        // Display metrics
        echo '<div class="metric-row mb-3">';
        echo '<div class="h4 text-warning fw-bold metric-tile">';
        echo number_format($totalFiles) . ' Files';
        echo '</div>';

        echo '<div class="h4 text-info fw-bold metric-tile">';
        echo number_format($totalPrints) . ' Prints Taken';
        echo '</div>';
        echo '</div>';
        ?>
    </div>

    <!-- Leaderboard Card -->
    <div class="card mt-4 shadow-sm border-0 rounded-3 overflow-hidden">
        <div class="card-header bg-primary bg-gradient text-center py-3">
            <h4 class="mb-0 text-white fw-semibold">🏆 Print Leaderboard</h4>
        </div>
        <div class="card-body p-0">
            <ul class="list-group list-group-flush">
                <?php if (!empty($topPrinters)): ?>
                    <?php foreach ($topPrinters as $index => $user): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-3 hover-effect">
                            <div class="d-flex align-items-center">
                                <span class="fw-bold text-primary me-2">#<?= $index + 1; ?></span>
                                <span class="text-dark"><?= h($user['name']); ?></span>
                            </div>
                            <span class="badge bg-purple rounded-pill px-3 py-2">
                                <?= count($user['prints']); ?> prints
                            </span>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="list-group-item text-center py-4">
                        <div class="d-flex flex-column align-items-center">
                            <i class="bi bi-exclamation-triangle fs-2 text-muted mb-2"></i>
                            <span class="text-muted">No print data available</span>
                        </div>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</main>

<style>
.bg-purple {
    background-color: #6366f1 !important;
}
.hover-effect:hover {
    background-color: #f8f9fa;
    transform: translateX(5px);
    transition: all 0.3s ease;
}
.rounded-3 {
    border-radius: 1rem !important;
}
.card-header {
    border-bottom: 2px solid rgba(255, 255, 255, 0.1);
}
.main-container {
    max-width: 1200px;
    background: white;
    border-radius: 1.25rem;
    box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.1);
    margin: 2rem auto;
    padding: 2.5rem;
    opacity: 0;
    transform: translateY(20px);
    animation: fadeIn 0.5s ease-out forwards;
}
@keyframes fadeIn {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
.title {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--primary-color);
    margin-bottom: 2rem;
    position: relative;
    padding-bottom: 0.5rem;
}
.title::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 4rem;
    height: 3px;
    background: var(--secondary-color);
}
.metrics-card {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 0.75rem;
    padding: 1.5rem;
    margin: 2rem auto;
    width: fit-content;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05);
}
.metric-row {
    display: flex;
    gap: 2rem;
    justify-content: center;
    flex-wrap: wrap;
}
.metric-tile {
    min-width: 120px;
}
</style>
<?php 
// We don't need any JavaScript since we removed the storage chart
?>
</body>
</html>
