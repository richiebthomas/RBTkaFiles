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
        
        .pad-main {
            display: flex;
            flex: 1;
            overflow: hidden;
        }
        
        .pad-sidebar {
            width: 280px;
            background: white;
            border-right: 1px solid #dee2e6;
            display: flex;
            flex-direction: column;
            transition: margin-left 0.3s ease;
        }
        
        .pad-sidebar.collapsed {
            margin-left: -280px;
        }
        
        .sidebar-header {
            padding: 15px;
            border-bottom: 1px solid #dee2e6;
            background: #f8f9fa;
        }
        
        .sidebar-header h5 {
            margin: 0 0 10px 0;
            font-size: 14px;
            font-weight: 600;
            color: #495057;
        }
        
        .pad-list {
            flex: 1;
            overflow-y: auto;
            padding: 10px;
        }
        
        .pad-item {
            padding: 10px 12px;
            margin-bottom: 5px;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.2s;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid transparent;
        }
        
        .pad-item:hover {
            background-color: #f8f9fa;
            border-color: #dee2e6;
        }
        
        .pad-item.active {
            background-color: #e3f2fd;
            border-color: #007bff;
        }
        
        .pad-item-name {
            flex: 1;
            font-size: 14px;
            font-weight: 500;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            color: #212529;
        }
        
        .pad-item-actions {
            display: none;
            gap: 5px;
        }
        
        .pad-item:hover .pad-item-actions,
        .pad-item.active .pad-item-actions {
            display: flex;
        }
        
        .pad-item-action-btn {
            padding: 2px 6px;
            font-size: 12px;
            background: transparent;
            border: none;
            cursor: pointer;
            color: #6c757d;
            transition: color 0.2s;
        }
        
        .pad-item-action-btn:hover {
            color: #007bff;
        }
        
        .pad-item-action-btn.delete:hover {
            color: #dc3545;
        }
        
        .sidebar-toggle {
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 30px;
            height: 60px;
            background: white;
            border: 1px solid #dee2e6;
            border-left: none;
            border-radius: 0 6px 6px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 100;
            transition: left 0.3s ease;
        }
        
        .sidebar-toggle.collapsed {
            left: 280px;
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
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            padding: 20px;
            position: relative;
        }
        
        .document-wrapper {
            width: 100%;
            max-width: 8.5in; /* Standard letter size width */
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 4px;
            overflow: hidden;
            position: relative;
            display: flex;
            flex-direction: column;
        }
        
        #firepad-container {
            flex: 1;
            width: 100%;
            border: none;
            border-radius: 0;
            overflow: hidden;
        }
        
        .firepad-toolbar {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            padding: 8px;
            border-radius: 0.375rem 0.375rem 0 0;
        }
        
        .CodeMirror {
            height: calc(100% - 50px) !important;
            font-family: 'Calibri', 'Segoe UI', -apple-system, BlinkMacSystemFont, sans-serif;
            font-size: 11pt; /* Standard Word font size */
            line-height: 1.15;
            background: white;
        }
        
        .CodeMirror-scroll {
            height: 100% !important;
            overflow: auto !important;
        }
        
        .CodeMirror-lines {
            padding: 0.5in 0.5in 0.5in 0.5in !important; /* 0.5 inch margins on all sides */
        }
        
        /* MS Word-like styling */
        .firepad .CodeMirror .CodeMirror-line {
            line-height: 1.15; /* Single spacing like Word */
            margin-bottom: 0;
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
            position: relative;
            z-index: 10;
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
        
         /* Print styles - simple and clean */
         @media print {
             /* Hide UI elements */
             .pad-header,
             .firepad-toolbar,
             .firepad .firepad-toolbar,
             .CodeMirror-cursors,
             .CodeMirror-cursor,
             .CodeMirror-gutters,
             .resize-handle {
                 display: none !important;
             }
             
             /* Hide Firepad watermark */
             .firepad-watermark,
             .firepad .firepad-watermark,
             [class*="watermark"],
             [id*="watermark"] {
                 display: none !important;
                 visibility: hidden !important;
                 opacity: 0 !important;
             }
             
             /* Reset everything for print */
             * {
                 -webkit-print-color-adjust: exact !important;
                 color-adjust: exact !important;
             }
             
             body {
                 margin: 0 !important;
                 padding: 0 !important;
                 background: white !important;
                 font-size: 11pt !important;
                 line-height: 1.15 !important;
                 color: black !important;
             }
             
             /* Make all containers visible and flow naturally */
             .pad-container,
             .pad-content,
             .document-wrapper,
             #firepad-container,
             .CodeMirror,
             .CodeMirror-scroll,
             .CodeMirror-sizer,
             .CodeMirror-lines {
                 height: auto !important;
                 min-height: auto !important;
                 max-height: none !important;
                 overflow: visible !important;
                 background: white !important;
                 color: black !important;
                 display: block !important;
                 position: static !important;
                 margin: 0 !important;
                 padding: 0 !important;
                 border: none !important;
                 box-shadow: none !important;
                 border-radius: 0 !important;
             }
             
             /* Set document margins */
             .CodeMirror-lines {
                 padding: 0.5in 0.5in 0.5in 0.5in !important;
             }
             
             /* Ensure text is visible */
             .CodeMirror-line {
                 color: black !important;
                 background: transparent !important;
                 height: auto !important;
                 min-height: 1.4em !important;
                 line-height: 1.15 !important;
                 font-size: 11pt !important;
                 font-family: 'Calibri', 'Segoe UI', -apple-system, BlinkMacSystemFont, sans-serif !important;
             }
             
             /* Images */
             .CodeMirror img,
             .resizable-image-container img {
                 display: block !important;
                 margin: 10px 0 !important;
                 max-width: 100% !important;
                 height: auto !important;
                 background: white !important;
             }
             
             .resizable-image-container {
                 display: block !important;
                 position: static !important;
                 margin: 10px 0 !important;
                 max-width: 100% !important;
             }
             
             .CodeMirror-linewidget {
                 display: block !important;
                 position: static !important;
                 margin: 0 !important;
                 padding: 0 !important;
             }
             
             /* Hide image placeholder text but keep the line visible for the widget */
             .CodeMirror-line[data-image-line="true"] {
                 color: transparent !important;
                 font-size: 0 !important;
                 line-height: 0 !important;
                 height: 0 !important;
                 overflow: hidden !important;
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
        
        /* Hide image markers from view - use JavaScript approach instead */
        .CodeMirror-line {
            position: relative;
        }
        
        /* Hide lines that contain image markers */
        .CodeMirror-line[data-image-line="true"] {
            color: transparent !important;
            font-size: 0 !important;
            line-height: 0 !important;
            height: 0 !important;
            overflow: hidden !important;
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
        <div class="pad-main">
            <?= $this->fetch('content') ?>
        </div>
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
