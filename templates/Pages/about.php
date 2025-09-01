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
      background: white;
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
      padding: 0;
    }
    .main-container {
      max-width: 1200px;
      margin: 0 auto;
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

    /* Minimalistic Analytics */
    .analytics-minimal {
      text-align: center;
      margin: 3rem 0;
    }

    .analytics-heading {
      font-size: 1.5rem;
      font-weight: 600;
      color: #6c757d;
      margin-bottom: 2rem;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .metrics-grid {
      display: flex;
      justify-content: center;
      gap: 3rem;
      flex-wrap: wrap;
    }

    .metric-item {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 0.5rem;
    }

    .metric-number {
      font-size: 2.5rem;
      font-weight: 700;
      color: #2c3e50;
      line-height: 1;
    }

    .metric-text {
      font-size: 0.85rem;
      color: #6c757d;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      font-weight: 500;
    }

    /* Minimalistic Chart Container */
    .chart-container {
      text-align: center;
      margin: 3rem 0;
    }

    .chart-heading {
      font-size: 1.5rem;
      font-weight: 600;
      color: #6c757d;
      margin-bottom: 2rem;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .chart-wrapper {
      max-width: 800px;
      margin: 0 auto;
      padding: 1rem;
    }

    /* Minimalistic Leaderboard */
    .leaderboard-container {
      text-align: center;
      margin: 3rem 0;
    }

    .leaderboard-heading {
      font-size: 1.5rem;
      font-weight: 600;
      color: #6c757d;
      margin-bottom: 2rem;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .leaderboard-list {
      max-width: 600px;
      margin: 0 auto;
    }

    .leaderboard-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1rem 0;
      border-bottom: 1px solid #e9ecef;
    }

    .leaderboard-item:last-child {
      border-bottom: none;
    }

    .rank {
      font-size: 1.2rem;
      font-weight: 700;
      color: #007bff;
      min-width: 60px;
      text-align: left;
    }

    .user-name {
      flex: 1;
      font-size: 1.1rem;
      color: #2c3e50;
      font-weight: 500;
      text-align: left;
      margin-left: 1rem;
    }

    .print-count {
      font-size: 0.9rem;
      color: #6c757d;
      font-weight: 500;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      min-width: 100px;
      text-align: right;
    }

    .no-data {
      padding: 2rem;
      color: #6c757d;
      font-style: italic;
    }

    /* Minimalistic Contribute Section */
    .contribute-section {
      text-align: center;
      margin: 3rem 0;
    }

    .contribute-heading {
      font-size: 1.5rem;
      font-weight: 600;
      color: #6c757d;
      margin-bottom: 1rem;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .contribute-subtitle {
      color: #6c757d;
      font-size: 1rem;
      margin-bottom: 2rem;
      max-width: 600px;
      margin-left: auto;
      margin-right: auto;
    }

    .contribute-links {
      display: flex;
      flex-direction: column;
      gap: 1rem;
      max-width: 400px;
      margin: 0 auto;
    }

    .contribute-link {
      display: flex;
      align-items: center;
      gap: 1rem;
      padding: 1rem 1.5rem;
      background: #f8f9fa;
      border-radius: 0.5rem;
      text-decoration: none;
      color: #2c3e50;
      font-weight: 500;
      transition: all 0.2s ease;
      border: 1px solid #e9ecef;
    }

    .contribute-link:hover {
      background: #e9ecef;
      transform: translateY(-2px);
      box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
      color: #2c3e50;
      text-decoration: none;
    }

    .contribute-link i {
      font-size: 1.2rem;
      color: #007bff;
      width: 20px;
      text-align: center;
    }

    .contribute-link span {
      flex: 1;
      text-align: left;
    }

    /* Responsive */
    @media (max-width: 768px) {
      body {
        padding: 1rem;
      }
      .main-container {
        margin: 0 auto;
      }
      .title {
        font-size: 2rem;
      }
      .metrics-grid {
        gap: 2rem;
      }
      .metric-number {
        font-size: 2rem;
      }
      .chart-wrapper {
        padding: 0.5rem;
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

        <!-- Minimalistic Site Analytics -->
    <div class="analytics-minimal">
        <h3 class="analytics-heading">Site Analytics</h3>
        
        <div class="metrics-grid">
            <?php
            // Get total files count from database
            $totalFiles = $this->getRequest()->getAttribute('fileCount');
            
            // Get print statistics from users table
            $printStats = $this->getRequest()->getAttribute('printStats');
            $totalPrints = $printStats['totalPrints'] ?? 0;
            $topPrinters = $printStats['topPrinters'] ?? [];
            
            // Get visit statistics
            $totalVisits = ($this->getRequest()->getAttribute('totalVisits') ?? 0) + 1650; //Adding visits from old website: rbt.free.nf
            $todayVisits = $this->getRequest()->getAttribute('todayVisits') ?? 0;
            $thisWeekVisits = $this->getRequest()->getAttribute('thisWeekVisits') ?? 0;
            ?>
            
            <div class="metric-item">
                <span class="metric-number"><?= number_format($totalFiles) ?></span>
                <span class="metric-text">Files Hosted</span>
            </div>
            
            <div class="metric-item">
                <span class="metric-number"><?= number_format($totalPrints) ?></span>
                <span class="metric-text">Prints Taken</span>
            </div>
            
            <div class="metric-item">
                <span class="metric-number"><?= number_format($todayVisits) ?></span>
                <span class="metric-text">Visits Today</span>
            </div>
            
            <div class="metric-item">
                <span class="metric-number"><?= number_format($thisWeekVisits) ?></span>
                <span class="metric-text">Visits This Week</span>
            </div>
            
            <div class="metric-item">
                <span class="metric-number"><?= number_format($totalVisits) ?></span>
                <span class="metric-text">Total Visits</span>
            </div>
        </div>
    </div>

    <!-- Weekly Visits Chart -->
    <div class="chart-container">
        <h3 class="chart-heading">
            Weekly Visits Trend 
            <?php 
            $weeklyData = $this->getRequest()->getAttribute('weeklyVisitsData') ?? [];
            if (!empty($weeklyData['labels'])) {
                $endDate = new \DateTime('today');
                $startDate = clone $endDate;
                $startDate->sub(new \DateInterval('P6D'));
                echo '<small class="text-muted d-block mt-2">' . 
                     $startDate->format('M j') . ' - ' . $endDate->format('M j, Y') . 
                     '</small>';
            }
            ?>
        </h3>
        <div class="chart-wrapper">
            <canvas id="weeklyVisitsChart" width="400" height="200"></canvas>
        </div>
    </div>

    <!-- Print Leaderboard -->
    <div class="leaderboard-container">
        <h3 class="leaderboard-heading">Print Leaderboard</h3>
        <div class="leaderboard-list">
            <?php if (!empty($topPrinters)): ?>
                <?php foreach ($topPrinters as $index => $user): ?>
                    <div class="leaderboard-item">
                        <div class="rank">#<?= $index + 1; ?></div>
                        <div class="user-name"><?= h($user['name']); ?></div>
                        <div class="print-count"><?= $user['print_count']; ?> prints</div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-data">
                    <span>No print data available</span>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Contribute to RBTkaFiles -->
    <div class="contribute-section">
        <h3 class="contribute-heading">Contribute to RBTkaFiles</h3>
        <p class="contribute-subtitle">
            Help make RBTkaFiles better for everyone! Your contributions make a real difference.
        </p>
        
        <div class="contribute-links">
            <a href="https://github.com/richiebthomas/RBTkaFiles/issues" target="_blank" class="contribute-link">
                <i class="fas fa-bug"></i>
                <span>Report Bugs</span>
            </a>
            
            <a href="https://github.com/richiebthomas/RBTkaFiles/issues/new" target="_blank" class="contribute-link">
                <i class="fas fa-lightbulb"></i>
                <span>Request Features</span>
            </a>
            
            <a href="https://github.com/richiebthomas/RBTkaFiles" target="_blank" class="contribute-link">
                <i class="fas fa-code"></i>
                <span>Write Code & Push</span>
            </a>
            
            <a href="https://github.com/richiebthomas/RBTkaFiles" target="_blank" class="contribute-link">
                <i class="fas fa-book"></i>
                <span>Write Documentation</span>
            </a>
            
            <a href="https://github.com/richiebthomas/RBTkaFiles" target="_blank" class="contribute-link">
                <i class="fas fa-star"></i>
                <span>Star the Project</span>
            </a>
        </div>
    </div>

<?php 
// We don't need any JavaScript since we removed the storage chart
?>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Weekly Visits Chart
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('weeklyVisitsChart').getContext('2d');
    
    // Get weekly data from PHP
    const weeklyData = <?= json_encode($this->getRequest()->getAttribute('weeklyVisitsData') ?? []) ?>;
    
    // Only show data if we have it, don't show fallback data
    if (!weeklyData.labels || !weeklyData.data || weeklyData.labels.length === 0) {
        // Hide the chart if no data is available
        document.getElementById('weeklyVisitsChart').style.display = 'none';
        document.querySelector('.chart-container').innerHTML = '<p class="text-muted text-center">No visit data available for this week</p>';
        return;
    }
    
    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: weeklyData.labels,
            datasets: [{
                label: 'Visits',
                data: weeklyData.data,
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#28a745',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: '#28a745',
                    borderWidth: 1,
                    cornerRadius: 8,
                    displayColors: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)',
                        drawBorder: false
                    },
                    ticks: {
                        color: '#6c757d',
                        font: {
                            weight: '600'
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#6c757d',
                        font: {
                            weight: '600'
                        }
                    }
                }
            },
            elements: {
                point: {
                    hoverBackgroundColor: '#28a745'
                }
            }
        }
    });
});
</script>
</body>
</html>