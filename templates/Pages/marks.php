<?php
/**
 * Marks Page Template
 */
$this->assign('title', 'FCRIT Student Marks - RBTkaFiles');
?>

<style>
    :root {
      --primary-color: #007bff;
      --secondary-color: #28a745;
      --background-color: #f8f9fa;
      --text-color: #2c3e50;
    }
    
    * {
      box-sizing: border-box;
    }
    
    body {
      font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
      background: white !important;
      background-color: white !important;
      color: var(--text-color);
      min-height: 100vh;
      padding: 0;
      margin: 0;
      font-size: 14px;
    }
    
    .main-container {
      margin: 0 auto;
      padding: 1rem;
      max-width: 1200px;
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
      font-size: 1.8rem;
      font-weight: 700;
      color: var(--primary-color);
      margin-bottom: 1.5rem;
      position: relative;
      padding-bottom: 0.5rem;
      text-align: center;
    }
    
    .title::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 50%;
      transform: translateX(-50%);
      width: 3rem;
      height: 3px;
      background: var(--secondary-color);
    }
    
    .lead {
      text-align: center;
      margin-bottom: 2rem;
      font-size: 0.9rem;
    }
    
    .login-form {
      background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
      border-radius: 0.75rem;
      padding: 1.5rem;
      margin: 1rem auto;
      box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05);
      max-width: 400px;
    }
    
    .form-control {
      border: 1px solid #ddd;
      border-radius: 0.5rem;
      padding: 0.75rem;
      margin-bottom: 1rem;
      width: 100%;
      font-size: 16px; /* Prevents zoom on iOS */
      transition: all 0.2s ease;
    }
    
    .form-control:focus {
      border-color: var(--primary-color);
      box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
      outline: none;
    }
    
    .btn-primary {
      background: var(--primary-color);
      border: none;
      padding: 0.75rem 2rem;
      border-radius: 0.5rem;
      font-weight: 600;
      width: 100%;
      font-size: 16px;
      transition: all 0.2s ease;
    }
    
    .btn-primary:hover {
      background: #0056b3;
      transform: translateY(-2px);
    }
    
    .student-name {
      font-size: 1.2rem;
      font-weight: bold;
      margin: 1.5rem 0;
      text-align: center;
      color: var(--primary-color);
    }
    
    .marks-section {
      margin: 1.5rem 0;
    }
    
    .marks-section h3 {
      color: var(--primary-color);
      margin-bottom: 1rem;
      font-weight: 600;
      font-size: 1.1rem;
    }
    
    .marks-content {
      background: white;
      border-radius: 0.5rem;
      padding: 0.5rem;
      box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
      overflow: hidden;
    }
    
    /* Desktop table styles */
    .marks-table-container {
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
      border-radius: 0.5rem;
    }
    
    .marks-table {
      width: 100%;
      border-collapse: collapse;
      margin: 0;
      font-size: 0.85rem;
    }
    
    .marks-table th,
    .marks-table td {
      padding: 0.5rem;
      text-align: left;
      border: 1px solid #ddd;
      white-space: nowrap;
    }
    
    .marks-table th {
      background-color: #f8f9fa;
      font-weight: 600;
      position: sticky;
      top: 0;
      z-index: 1;
      font-size: 0.8rem;
    }
    
    /* Mobile card layout */
    .mobile-card-view {
      display: none;
    }
    
    .mark-card {
      background: #f8f9fa;
      border-radius: 0.5rem;
      padding: 1rem;
      margin-bottom: 1rem;
      border: 1px solid #ddd;
    }
    
    .mark-card-header {
      font-weight: bold;
      color: var(--primary-color);
      margin-bottom: 0.5rem;
      font-size: 1rem;
    }
    
    .mark-card-row {
      display: flex;
      justify-content: space-between;
      margin-bottom: 0.25rem;
      font-size: 0.9rem;
    }
    
    .mark-card-label {
      font-weight: 500;
      color: #555;
    }
    
    .mark-card-value {
      font-weight: 600;
    }
    
    .no-data {
      text-align: center;
      color: #6c757d;
      font-style: italic;
      padding: 2rem 1rem;
    }
    
    .loading {
      text-align: center;
      color: var(--primary-color);
      font-style: italic;
      padding: 1rem;
    }
    
    .error {
      color: #dc3545;
      text-align: center;
      margin: 1rem 0;
      padding: 1rem;
    }
    
    /* Mobile optimizations */
    @media (max-width: 768px) {
      body {
        font-size: 14px;
      }
      
      .main-container {
        padding: 0.5rem;
      }
      
      .title {
        font-size: 1.5rem;
        margin-bottom: 1rem;
      }
      
      .lead {
        font-size: 0.85rem;
        margin-bottom: 1.5rem;
      }
      
      .login-form {
        padding: 1rem;
        margin: 0.5rem auto;
        max-width: 100%;
      }
      
      .login-form h3 {
        font-size: 1.1rem;
      }
      
      .student-name {
        font-size: 1.1rem;
        margin: 1rem 0;
      }
      
      .marks-section h3 {
        font-size: 1rem;
      }
      
      .marks-content {
        padding: 0.25rem;
      }
      
      /* Hide table view on mobile */
      .desktop-table-view {
        display: none;
      }
      
      /* Show card view on mobile */
      .mobile-card-view {
        display: block;
      }
      
      .mark-card {
        padding: 0.75rem;
        margin-bottom: 0.75rem;
      }
      
      .mark-card-header {
        font-size: 0.95rem;
      }
      
      .mark-card-row {
        font-size: 0.85rem;
      }
    }
    
    @media (max-width: 480px) {
      .main-container {
        padding: 0.25rem;
      }
      
      .title {
        font-size: 1.3rem;
      }
      
      .login-form {
        border-radius: 0.5rem;
      }
      
      .mark-card {
        padding: 0.5rem;
      }
      
      .mark-card-header {
        font-size: 0.9rem;
      }
      
      .mark-card-row {
        font-size: 0.8rem;
      }
    }
    
    /* Utility classes */
    .text-center { text-align: center; }
    .mb-3 { margin-bottom: 1rem; }
    .mb-4 { margin-bottom: 1.5rem; }
    .mb-5 { margin-bottom: 2rem; }
    .me-2 { margin-right: 0.5rem; }
    .d-grid { display: grid; }
    .form-label { 
      font-weight: 500; 
      margin-bottom: 0.5rem; 
      display: block;
      font-size: 0.9rem;
    }
    .text-muted { color: #6c757d; }
    .small { font-size: 0.8rem; }
</style>

<div class="main-container">
  <h1 class="title">FCRIT Student Marks</h1>
  
  <div class="lead">
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

    // Function to convert table to mobile card view
    function convertTableToCards(tableHtml, containerId) {
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = tableHtml;
        const table = tempDiv.querySelector('table');
        
        if (!table) return tableHtml;
        
        const headers = Array.from(table.querySelectorAll('th')).map(th => {
            let headerText = th.textContent.trim();
            // Convert long form headers to short form
            headerText = headerText.replace(/Internal Assessment[\s-]*1[\s]*Marks?/gi, 'IA-1');
            headerText = headerText.replace(/Internal Assessment[\s-]*2[\s]*Marks?/gi, 'IA-2');
            headerText = headerText.replace(/Internal Assessment[\s-]*Marks?/gi, 'IA Marks');
            return headerText;
        });
        const rows = Array.from(table.querySelectorAll('tbody tr'));
        
        let mobileHtml = `<div class="desktop-table-view"><div class="marks-table-container">${tableHtml}</div></div>`;
        mobileHtml += `<div class="mobile-card-view">`;
        
        rows.forEach((row, index) => {
            const cells = Array.from(row.querySelectorAll('td'));
            if (cells.length === 0) return;
            
            // Find subject name (usually first column)
            const subjectName = cells[0] ? cells[0].textContent.trim() : `Subject ${index + 1}`;
            
            mobileHtml += `<div class="mark-card">`;
            mobileHtml += `<div class="mark-card-header">${subjectName}</div>`;
            
            cells.forEach((cell, cellIndex) => {
                if (headers[cellIndex] && cell.textContent.trim() && cellIndex > 0) { // Skip first column as it's used as header
                    mobileHtml += `<div class="mark-card-row">`;
                    mobileHtml += `<span class="mark-card-label">${headers[cellIndex]}:</span>`;
                    mobileHtml += `<span class="mark-card-value">${cell.textContent.trim()}</span>`;
                    mobileHtml += `</div>`;
                }
            });
            
            mobileHtml += `</div>`;
        });
        
        mobileHtml += `</div>`;
        return mobileHtml;
    }

    // Function to add responsive table classes
    function makeTableResponsive(tableHtml) {
        return tableHtml.replace(/<table([^>]*)>/gi, '<table$1 class="marks-table">');
    }

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
                    const responsiveTable = makeTableResponsive(data.internal_assessment);
                    const mobileContent = convertTableToCards(responsiveTable, 'internal-assessment');
                    internalAssessmentDiv.innerHTML = mobileContent;
                } else {
                    internalAssessmentDiv.innerHTML = '<div class="no-data">No internal assessment data found</div>';
                }
                
                // Update end semester marks
                if (data.end_semester_marks) {
                    const responsiveTable = makeTableResponsive(data.end_semester_marks);
                    const mobileContent = convertTableToCards(responsiveTable, 'end-semester');
                    endSemesterDiv.innerHTML = mobileContent;
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