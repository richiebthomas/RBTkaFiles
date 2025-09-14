<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= $this->fetch('title') ?: 'RBTkaWordPad' ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- CodeMirror CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.css" />
    
    <!-- Firepad CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/firepad@1.5.9/dist/firepad.css" />
    
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        
        .pad-container {
            height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .pad-header {
            background: white;
            border-bottom: 1px solid #dee2e6;
            padding: 15px 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .pad-content {
            flex: 1;
            overflow: hidden;
        }
        
        #firepad-container {
            height: 100%;
            width: 100%;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
        }
        
        .firepad-toolbar {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            padding: 8px;
            border-radius: 0.375rem 0.375rem 0 0;
        }
        
        .CodeMirror {
            height: calc(100% - 50px) !important;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-size: 14px;
        }
        
        /* Firepad toolbar styling */
        .firepad .firepad-toolbar {
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            padding: 8px;
            display: flex;
            align-items: center;
            gap: 5px;
            flex-wrap: wrap;
        }
        
        .firepad .firepad-toolbar button {
            background: white;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 5px 8px;
            cursor: pointer;
            font-size: 12px;
            margin: 2px;
        }
        
        .firepad .firepad-toolbar button:hover {
            background: #e9ecef;
        }
        
        .firepad .firepad-toolbar button.active {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }
        
         /* Print styles - hide toolbar and buttons when printing */
         @media print {
             body {
                 margin: 0 !important;
                 padding: 0 !important;
                 background: white !important;
             }
             
             .pad-header {
                 display: none !important;
             }
             
             .firepad-toolbar {
                 display: none !important;
             }
             
             .firepad .firepad-toolbar {
                 display: none !important;
             }
             
             .pad-container {
                 height: auto !important;
                 display: block !important;
             }
             
             .pad-content {
                 flex: none !important;
                 overflow: visible !important;
                 height: auto !important;
             }
             
             #firepad-container {
                 height: auto !important;
                 border: none !important;
                 box-shadow: none !important;
                 border-radius: 0 !important;
             }
             
             .CodeMirror {
                 height: auto !important;
                 min-height: auto !important;
                 border: none !important;
                 box-shadow: none !important;
                 overflow: visible !important;
                 background: white !important;
                 font-size: 12pt !important;
                 line-height: 1.4 !important;
                 color: #000 !important;
             }
             
             .CodeMirror-scroll {
                 height: auto !important;
                 min-height: auto !important;
                 overflow: visible !important;
                 background: white !important;
             }
             
             .CodeMirror-sizer {
                 height: auto !important;
                 min-height: auto !important;
             }
             
             .CodeMirror-lines {
                 padding: 20px !important;
                 height: auto !important;
                 min-height: auto !important;
             }
             
             .CodeMirror-line {
                 padding: 0 !important;
                 height: auto !important;
                 min-height: 1.4em !important;
                 page-break-inside: avoid;
             }
             
             /* Ensure images print properly */
             .CodeMirror img {
                 max-width: 100% !important;
                 height: auto !important;
                 page-break-inside: avoid !important;
                 margin: 10px 0 !important;
                 display: block !important;
             }
             
             /* Line widgets for images */
             .CodeMirror-linewidget {
                 page-break-inside: avoid !important;
             }
             
             /* Hide any other UI elements */
             .CodeMirror-cursors,
             .CodeMirror-cursor,
             .CodeMirror-gutters {
                 display: none !important;
             }
             
             /* Ensure proper page breaks */
             .CodeMirror-line {
                 page-break-inside: avoid;
             }
             
             /* Allow content to flow across pages */
             .CodeMirror {
                 page-break-inside: auto;
             }
             
             /* Force all content to be visible */
             * {
                 overflow: visible !important;
             }
         }
        
        .CodeMirror-focused .CodeMirror-cursor {
            border-left: 1px solid #000;
        }
        
        /* Ensure images are displayed properly in Firepad */
        .CodeMirror img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 5px;
            background: #f9f9f9;
        }
        
        /* Hide image markers from view */
        .CodeMirror-line {
            position: relative;
        }
        
        /* Hide text that contains image markers */
        .CodeMirror .cm-line:has-text("[IMAGE:") {
            color: transparent !important;
            font-size: 0 !important;
            line-height: 0 !important;
            height: 0 !important;
        }
        
        /* Alternative approach for hiding image markers */
        .CodeMirror-line span:contains("[IMAGE:") {
            color: transparent;
            font-size: 0;
        }
        
        /* Firepad rich text styling */
        .firepad .CodeMirror {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        
        .firepad .CodeMirror .CodeMirror-line {
            line-height: 1.6;
        }
        
        /* Status badge styles */
        .badge {
            font-size: 0.75rem;
            padding: 0.375rem 0.75rem;
        }
        
        .badge-success {
            background-color: #28a745;
        }
        
        .badge-warning {
            background-color: #ffc107;
            color: #212529;
        }
        
        .badge-info {
            background-color: #17a2b8;
        }
        
        .btn {
            border-radius: 6px;
            font-weight: 500;
        }
        
        .btn-sm {
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
        }
    </style>
    
    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
</head>
<body>
    <div class="pad-container">
        <?= $this->fetch('content') ?>
    </div>

    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>
    
    <!-- CodeMirror JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.js"></script>
    
    <!-- Firepad JS -->
    <script src="https://cdn.jsdelivr.net/npm/firepad@1.5.9/dist/firepad.min.js"></script>
    
    <?= $this->fetch('script') ?>
</body>
</html>
