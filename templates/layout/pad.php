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
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            background: #fafafa;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
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
        
        /* Modern Sidebar */
        .pad-sidebar {
            width: 260px;
            background: white;
            border-right: 1px solid #e5e7eb;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .pad-sidebar.collapsed {
            transform: translateX(-100%);
        }
        
        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .sidebar-header h5 {
            margin: 0 0 16px 0;
            font-size: 13px;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .pad-list {
            flex: 1;
            overflow-y: auto;
            padding: 12px;
        }
        
        .pad-list::-webkit-scrollbar {
            width: 6px;
        }
        
        .pad-list::-webkit-scrollbar-track {
            background: transparent;
        }
        
        .pad-list::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 3px;
        }
        
        .pad-list::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }
        
        .pad-item {
            padding: 10px 12px;
            margin-bottom: 4px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid transparent;
        }
        
        .pad-item:hover {
            background: #f9fafb;
            border-color: #e5e7eb;
        }
        
        .pad-item.active {
            background: #eff6ff;
            border-color: #3b82f6;
        }
        
        .pad-item-name {
            flex: 1;
            font-size: 14px;
            font-weight: 500;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            color: #1f2937;
        }
        
        .pad-item.active .pad-item-name {
            color: #1e40af;
        }
        
        .pad-item-actions {
            display: none;
            gap: 4px;
        }
        
        .pad-item:hover .pad-item-actions,
        .pad-item.active .pad-item-actions {
            display: flex;
        }
        
        .pad-item-action-btn {
            padding: 4px 8px;
            font-size: 12px;
            background: transparent;
            border: none;
            cursor: pointer;
            color: #9ca3af;
            transition: all 0.2s;
            border-radius: 4px;
        }
        
        .pad-item-action-btn:hover {
            color: #3b82f6;
            background: #eff6ff;
        }
        
        .pad-item-action-btn.delete:hover {
            color: #ef4444;
            background: #fef2f2;
        }
        
        .sidebar-toggle {
            position: fixed;
            left: 260px;
            top: 50%;
            transform: translateY(-50%);
            width: 24px;
            height: 48px;
            background: white;
            border: 1px solid #e5e7eb;
            border-left: none;
            border-radius: 0 8px 8px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 100;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.05);
        }
        
        .sidebar-toggle:hover {
            background: #f9fafb;
        }
        
        .sidebar-toggle.collapsed {
            left: 0;
        }
        
        .sidebar-toggle i {
            color: #6b7280;
            font-size: 12px;
        }
        
        /* Modern Header */
        .pad-header {
            background: white;
            border-bottom: 1px solid #e5e7eb;
            padding: 16px 24px;
        }
        
        .pad-header h4 {
            font-size: 16px;
            font-weight: 600;
            color: #1f2937;
            margin: 0;
        }
        
        .pad-header h4 i {
            color: #3b82f6;
            margin-right: 8px;
        }
        
        .pad-content {
            flex: 1;
            overflow: hidden;
            background: #fafafa;
            display: flex;
            justify-content: center;
            padding: 24px;
        }
        
        .document-wrapper {
            width: 100%;
            max-width: 8.5in;
            background: white;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1), 0 1px 2px rgba(0, 0, 0, 0.06);
            border-radius: 8px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        
        #firepad-container {
            flex: 1;
            width: 100%;
            border: none;
            overflow: hidden;
        }
        
        
        .CodeMirror {
            height: calc(100% - 50px) !important;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-size: 15px;
            line-height: 1.6;
            background: white;
        }
        
        .CodeMirror-scroll {
            height: 100% !important;
            overflow: auto !important;
        }
        
        .CodeMirror-lines {
            padding: 32px 48px !important;
        }
        
        /* Remove Firepad Watermark */
        .firepad-watermark,
        .firepad .firepad-watermark,
        [class*="watermark"],
        [id*="watermark"],
        a[href*="firepad"] {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            height: 0 !important;
            width: 0 !important;
            position: absolute !important;
            left: -9999px !important;
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
        
        
        /* Modern Badge Styles */
        .badge {
            font-size: 12px;
            padding: 4px 12px;
            border-radius: 12px;
            font-weight: 500;
            letter-spacing: 0.3px;
        }
        
        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }
        
        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }
        
        .badge-info {
            background: #dbeafe;
            color: #1e40af;
        }
        
        /* Modern Button Styles */
        .btn {
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s;
            border: 1px solid transparent;
        }
        
        .btn-sm {
            padding: 8px 16px;
            font-size: 14px;
        }
        
        .btn-primary {
            background: #3b82f6;
            color: white;
            border-color: #3b82f6;
        }
        
        .btn-primary:hover {
            background: #2563eb;
            border-color: #2563eb;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(59, 130, 246, 0.2);
        }
        
        .btn-success {
            background: #10b981;
            color: white;
            border-color: #10b981;
        }
        
        .btn-success:hover {
            background: #059669;
            border-color: #059669;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(16, 185, 129, 0.2);
        }
        
        .btn-block {
            width: 100%;
            display: block;
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
