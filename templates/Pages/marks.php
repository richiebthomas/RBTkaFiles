<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>FCRIT Student Marks - RBTkaFiles</title>
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
      max-width: 1000px;
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
    .login-form {
      background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
      border-radius: 0.75rem;
      padding: 2rem;
      margin: 2rem auto;
      box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05);
      max-width: 500px;
    }
    .form-control {
      border: 1px solid #ddd;
      border-radius: 0.5rem;
      padding: 0.75rem;
      margin-bottom: 1rem;
      transition: all 0.2s ease;
    }
    .form-control:focus {
      border-color: var(--primary-color);
      box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
    .btn-primary {
      background: var(--primary-color);
      border: none;
      padding: 0.75rem 2rem;
      border-radius: 0.5rem;
      font-weight: 600;
      transition: all 0.2s ease;
    }
    .btn-primary:hover {
      background: #0056b3;
      transform: translateY(-2px);
    }
    .student-name {
      font-size: 1.5rem;
      font-weight: bold;
      margin: 2rem 0;
      text-align: center;
      color: var(--primary-color);
    }
    .marks-section {
      margin: 2rem 0;
    }
    .marks-section h3 {
      color: var(--primary-color);
      margin-bottom: 1rem;
      font-weight: 600;
    }
    .marks-content {
      background: white;
      border-radius: 0.5rem;
      padding: 1.5rem;
      box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
    }
    .marks-content table {
      width: 100%;
      border-collapse: collapse;
      margin: 1rem 0;
    }
    .marks-content table, 
    .marks-content th, 
    .marks-content td {
      border: 1px solid #ddd;
    }
    .marks-content th, 
    .marks-content td {
      padding: 0.75rem;
      text-align: left;
    }
    .marks-content th {
      background-color: #f8f9fa;
      font-weight: 600;
    }
    .no-data {
      text-align: center;
      color: #6c757d;
      font-style: italic;
      padding: 2rem;
    }
    .loading {
      text-align: center;
      color: var(--primary-color);
      font-style: italic;
    }
    .error {
      color: #dc3545;
      text-align: center;
      margin: 1rem 0;
    }
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
      .login-form {
        padding: 1.5rem;
        margin: 1rem auto;
      }
    }
  </style>
</head>
<body>
 
<?php
/**
 * Marks Page Template
 */
$this->assign('title', 'FCRIT Student Marks - RBTkaFiles');
?>

<div class="main-container">
  <h1 class="title text-center">FCRIT Student Marks</h1>
  
  <div class="lead text-center mb-5">
    <p class="mb-4">
      View your internal assessment and end semester marks from the FCRIT student portal.
    </p>
    <p class="text-muted small">
      Use the same credentials as your student portal login
    </p>
  </div>

  <div class="login-form">
    <h3 class="text-center mb-4">Login to View Your Marks</h3>
    <form id="marksForm" method="post">
      <div class="mb-3">
        <label for="rollnumber" class="form-label">Roll Number:</label>
        <input type="number" class="form-control" id="rollnumber" name="rollnumber" required>
      </div>
      
      <div class="mb-3">
        <label for="password" class="form-label">Password:</label>
        <input type="password" class="form-control" id="password" name="password" 
               placeholder="Leave blank if same as roll number">
      </div>
      
      <div class="d-grid">
        <button type="submit" class="btn btn-primary">
          <i class="fas fa-sign-in-alt me-2"></i>Login
        </button>
      </div>
    </form>
  </div>

  <div id="student-name" class="student-name" style="display: none;"></div>

  <div id="marks-container" style="display: none;">
    <div class="marks-section">
      <h3><i class="fas fa-chart-line me-2"></i>Internal Assessment Marks</h3>
      <div class="marks-content" id="internal-assessment">
        <div class="loading">Loading internal assessment marks...</div>
      </div>
    </div>

    <div class="marks-section">
      <h3><i class="fas fa-graduation-cap me-2"></i>End Semester Marks</h3>
      <div class="marks-content" id="end-semester">
        <div class="loading">Loading end semester marks...</div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('marksForm');
    const studentNameDiv = document.getElementById('student-name');
    const marksContainer = document.getElementById('marks-container');
    const internalAssessmentDiv = document.getElementById('internal-assessment');
    const endSemesterDiv = document.getElementById('end-semester');

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        const rollnumber = formData.get('rollnumber');
        const password = formData.get('password');
        
        // Show loading state
        marksContainer.style.display = 'block';
        studentNameDiv.style.display = 'block';
        studentNameDiv.textContent = 'Loading...';
        
        // First, fetch the marks data
        fetch('<?= $this->Url->build('/marks') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                'rollnumber': rollnumber,
                'password': password
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update internal assessment
                if (data.internal_assessment) {
                    internalAssessmentDiv.innerHTML = data.internal_assessment;
                } else {
                    internalAssessmentDiv.innerHTML = '<div class="no-data">No internal assessment data found</div>';
                }
                
                // Update end semester marks
                if (data.end_semester_marks) {
                    endSemesterDiv.innerHTML = data.end_semester_marks;
                } else {
                    endSemesterDiv.innerHTML = '<div class="no-data">No end semester marks found</div>';
                }
            } else {
                internalAssessmentDiv.innerHTML = '<div class="error">Failed to load marks data</div>';
                endSemesterDiv.innerHTML = '<div class="error">Failed to load marks data</div>';
            }
        })
        .catch(error => {
            console.error('Error loading marks:', error);
            internalAssessmentDiv.innerHTML = '<div class="error">An error occurred while loading marks</div>';
            endSemesterDiv.innerHTML = '<div class="error">An error occurred while loading marks</div>';
        });

        // Then, fetch the student name separately (lazy loading)
        const nameFormData = new FormData();
        nameFormData.append('lazy_name', '1');
        nameFormData.append('rollnumber', rollnumber);
        nameFormData.append('password', password);

        fetch('<?= $this->Url->build('/marks') ?>', {
            method: 'POST',
            body: nameFormData
        })
        .then(res => res.json())
        .then(data => {
            studentNameDiv.textContent = data.name || 'Name not found';
            studentNameDiv.style.color = '#28a745';
        })
        .catch(err => {
            console.error("Failed to load name:", err);
            studentNameDiv.textContent = 'Name fetch failed';
            studentNameDiv.style.color = '#dc3545';
        });
    });
});
</script>

</body>
</html>
