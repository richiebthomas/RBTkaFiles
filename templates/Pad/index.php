<?php
/**
 * Pad Index Template - Simple Firepad Editor
 */
$this->assign('title', 'RBTkaWordPad - Collaborative Editor');
?>

<div class="pad-header">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="mb-0">
            <i class="fas fa-edit text-primary"></i> RBTkaWordPad
        </h4>
        <div>
            <span id="firepad-status" class="badge badge-info">Connecting...</span>
            <button id="btn-print" class="btn btn-success btn-sm ml-2">
                <i class="fas fa-print"></i> Print
            </button>
        </div>
    </div>
</div>

<div class="pad-content">
    <div class="document-wrapper">
        <div id="firepad-container"></div>
    </div>
</div>

<!-- Firebase SDK v8 (compatible with Firepad) -->
<script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-database.js"></script>

<script>
    // Your web app's Firebase configuration
    <?php 
    $firebaseConfig = $this->get('firebaseConfig');
    if (!$firebaseConfig) {
        // Fallback: Load directly from config
        $firebaseConfig = \Cake\Core\Configure::read('Firebase');
    }
    ?>
    const firebaseConfig = {
        apiKey: "<?= h($firebaseConfig['apiKey'] ?? '') ?>",
        authDomain: "<?= h($firebaseConfig['authDomain'] ?? '') ?>",
        databaseURL: "<?= h($firebaseConfig['databaseURL'] ?? '') ?>",
        projectId: "<?= h($firebaseConfig['projectId'] ?? '') ?>",
        storageBucket: "<?= h($firebaseConfig['storageBucket'] ?? '') ?>",
        messagingSenderId: "<?= h($firebaseConfig['messagingSenderId'] ?? '') ?>",
        appId: "<?= h($firebaseConfig['appId'] ?? '') ?>"
    };
    
    
    // Initialize Firebase
    firebase.initializeApp(firebaseConfig);
    const database = firebase.database();
    
    // Initialize Firepad
    let firepad;
    let codeMirror;
    let insertedImages = []; // Store information about inserted images
    
    // Wait for Firepad to load
    function initFirepad() {
        if (typeof Firepad === 'undefined') {
            setTimeout(initFirepad, 100);
            return;
        }
        
        // Create CodeMirror instance
        codeMirror = CodeMirror(document.getElementById('firepad-container'), {
            lineWrapping: true,
            mode: 'text/html',
            theme: 'default'
        });
        
        // Create Firepad instance
        firepad = Firepad.fromCodeMirror(database.ref('pad'), codeMirror, {
            richTextShortcuts: true,
            richTextToolbar: true,
            defaultText: 'Welcome to RBTkaWordPad!\n\nStart typing your document here...\n\nThis is a collaborative editor powered by Firepad and Firebase.',
            userColors: ['#ff6b6b', '#4ecdc4', '#45b7d1', '#96ceb4', '#feca57', '#ff9ff3', '#54a0ff', '#5f27cd']
        });
        
        // Update status
        firepad.on('ready', function() {
            document.getElementById('firepad-status').textContent = 'Connected';
            document.getElementById('firepad-status').className = 'badge badge-success';
            
            // Setup image rendering
            setupImageRendering();
            
            // Setup global keyboard handling for images
            setupGlobalImageKeyboardHandling();
            
            // Force initial image rendering after a short delay
            setTimeout(() => {
                renderImages();
            }, 500);
        });
        
        firepad.on('synced', function(isSynced) {
            if (isSynced) {
                document.getElementById('firepad-status').textContent = 'Synced';
                document.getElementById('firepad-status').className = 'badge badge-success';
            } else {
                document.getElementById('firepad-status').textContent = 'Syncing...';
                document.getElementById('firepad-status').className = 'badge badge-warning';
            }
        });
    }
    
    // Initialize when page loads
    document.addEventListener('DOMContentLoaded', function() {
        initFirepad();
        
        // Print button
        document.getElementById('btn-print').addEventListener('click', function() {
            printDocument();
        });
        
        
        // Add image paste functionality
        setupImagePaste();
    });
    
    // Setup image paste functionality
    function setupImagePaste() {
        document.addEventListener('paste', function(e) {
            const items = e.clipboardData.items;
            
            for (let i = 0; i < items.length; i++) {
                const item = items[i];
                
                // Check if the pasted item is an image
                if (item.type.indexOf('image') !== -1) {
                    e.preventDefault();
                    
                    const file = item.getAsFile();
                    if (file) {
                        uploadImage(file);
                    }
                }
            }
        });
    }
    
    // Upload image function
    function uploadImage(file) {
        // Show uploading status
        const statusElement = document.getElementById('firepad-status');
        const originalText = statusElement.textContent;
        statusElement.textContent = 'Uploading image...';
        statusElement.className = 'badge badge-warning';
        
        const formData = new FormData();
        formData.append('image', file);
        
        fetch('/pad/upload-image', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Insert image into Firepad
                insertImageIntoFirepad(data.url);
                
                // Restore status
                statusElement.textContent = originalText;
                statusElement.className = 'badge badge-success';
            } else {
                alert('Failed to upload image: ' + data.message);
                
                // Restore status
                statusElement.textContent = originalText;
                statusElement.className = 'badge badge-success';
            }
        })
        .catch(error => {
            console.error('Error uploading image:', error);
            alert('Failed to upload image: ' + error.message);
            
            // Restore status
            statusElement.textContent = originalText;
            statusElement.className = 'badge badge-success';
        });
    }
    
    // Insert image into Firepad as text content (persists in Firebase)
    function insertImageIntoFirepad(imageUrl) {
        if (firepad) {
            // Insert a unique image marker that will be rendered as an image
            // Add default dimensions (will be updated when image loads)
            const imageMarker = `[IMAGE:${imageUrl}:400:300]`;
            
            // Get current cursor position in Firepad
            const cursor = codeMirror.getCursor();
            
            // Insert the image marker as text (this will sync with Firebase)
            codeMirror.replaceRange('\n' + imageMarker + '\n', cursor);
            
            // Move cursor after the image
            const newCursor = { 
                line: cursor.line + 2, 
                ch: 0 
            };
            codeMirror.setCursor(newCursor);
            
            // Refresh image rendering after a short delay
            setTimeout(() => {
                renderImages();
            }, 100);
        }
    }
    
    // Setup image rendering system
    function setupImageRendering() {
        if (!codeMirror) return;
        
        // Initial render
        renderImages();
        
        // Re-render images on content change
        codeMirror.on('change', function(cm, changeObj) {
            // If text was removed, immediately clean up and re-render
            if (changeObj.removed && changeObj.removed.some(line => line.includes('[IMAGE:'))) {
                setTimeout(renderImages, 50);
            } else {
                setTimeout(renderImages, 100);
            }
        });
        
        // Re-render on scroll to handle dynamic content
        codeMirror.on('scroll', function() {
            renderImages();
        });
        
        // Add periodic cleanup to ensure orphaned widgets are removed
        setInterval(function() {
            cleanupOrphanedImages();
        }, 2000);
    }
    
    // Setup global keyboard handling for images in CodeMirror
    function setupGlobalImageKeyboardHandling() {
        if (!codeMirror) return;
        
        codeMirror.on('keydown', function(cm, event) {
            const cursor = cm.getCursor();
            const line = cm.getLine(cursor.line);
            
            // Check if cursor is on a line with an image marker
            if (line && line.includes('[IMAGE:')) {
                handleImageLineKeyboard(event, cursor.line, cursor.ch, line);
            }
            
            // Handle navigation into/out of image lines
            if (event.key === 'ArrowUp' || event.key === 'ArrowDown') {
                handleVerticalNavigation(event, cursor);
            }
        });
        
        // Handle cursor position changes
        codeMirror.on('cursorActivity', function(cm) {
            const cursor = cm.getCursor();
            const line = cm.getLine(cursor.line);
            
            // Auto-select image if cursor lands on image line
            if (line && line.includes('[IMAGE:')) {
                highlightImageAtLine(cursor.line);
            } else {
                // Deselect all images if cursor is not on image line
                deselectAllImages();
            }
        });
    }
    
    // Handle keyboard events when cursor is on image line
    function handleImageLineKeyboard(event, lineNumber, charPosition, lineContent) {
        switch(event.key) {
            case 'Delete':
            case 'Backspace':
                // If at start of line or anywhere on image line, delete the image
                event.preventDefault();
                deleteImageAtLine(lineNumber);
                return;
                
            case 'ArrowLeft':
                // If at start of image line, move to previous line end
                if (charPosition === 0) {
                    event.preventDefault();
                    moveToLineBefore(lineNumber);
                }
                return;
                
            case 'ArrowRight':
                // If at end of image line, move to next line start
                if (charPosition >= lineContent.length) {
                    event.preventDefault();
                    moveToLineAfter(lineNumber);
                }
                return;
                
            case 'Enter':
                // Insert new line after image
                event.preventDefault();
                insertLineAfterImage(lineNumber);
                return;
                
            default:
                // For any other key, move cursor past the image
                if (event.key.length === 1) { // Printable character
                    event.preventDefault();
                    moveToLineAfter(lineNumber);
                    // Insert the character on the new line
                    setTimeout(() => {
                        codeMirror.replaceSelection(event.key);
                    }, 10);
                }
                return;
        }
    }
    
    // Handle vertical navigation (up/down arrows)
    function handleVerticalNavigation(event, cursor) {
        const targetLine = event.key === 'ArrowUp' ? cursor.line - 1 : cursor.line + 1;
        const doc = codeMirror.getDoc();
        
        if (targetLine >= 0 && targetLine < doc.lineCount()) {
            const targetLineContent = doc.getLine(targetLine);
            
            // If navigating to an image line, position cursor appropriately
            if (targetLineContent && targetLineContent.includes('[IMAGE:')) {
                setTimeout(() => {
                    highlightImageAtLine(targetLine);
                }, 10);
            }
        }
    }
    
    // Highlight image at specific line (visual selection)
    function highlightImageAtLine(lineNumber) {
        // Find the image container for this line
        const containers = document.querySelectorAll('.resizable-image-container');
        containers.forEach(container => {
            if (parseInt(container.getAttribute('data-line-number')) === lineNumber) {
                // Visual selection without showing resize handles
                container.classList.add('cursor-selected');
                const img = container.querySelector('img');
                img.style.border = '2px solid #007bff';
                img.style.backgroundColor = '#e3f2fd';
            } else {
                container.classList.remove('cursor-selected');
                const img = container.querySelector('img');
                img.style.border = '1px solid #ddd';
                img.style.backgroundColor = '#f9f9f9';
            }
        });
    }
    
    // Clean up orphaned image widgets
    function cleanupOrphanedImages() {
        if (!codeMirror) return;
        
        const doc = codeMirror.getDoc();
        const lineCount = doc.lineCount();
        
        for (let i = 0; i < lineCount; i++) {
            const line = doc.getLine(i);
            const lineHandle = doc.getLineHandle(i);
            
            if (lineHandle.widgets) {
                lineHandle.widgets.forEach(widget => {
                    if (widget.node && widget.node.classList.contains('resizable-image-container')) {
                        // If the line doesn't contain an image marker, remove the widget
                        if (!line || !line.includes('[IMAGE:')) {
                            widget.clear();
                        }
                    }
                });
            }
        }
    }
    
    // Render image markers as actual images
    function renderImages() {
        if (!codeMirror) return;
        
        const doc = codeMirror.getDoc();
        const lineCount = doc.lineCount();
        
        // First, clean up widgets for lines that no longer have image markers
        for (let i = 0; i < lineCount; i++) {
            const line = doc.getLine(i);
            const lineHandle = doc.getLineHandle(i);
            
            // Remove data attribute from lines that no longer have image markers
            if (lineHandle && lineHandle.lineElement) {
                if (!line || !line.includes('[IMAGE:')) {
                    lineHandle.lineElement.removeAttribute('data-image-line');
                    lineHandle.lineElement.style.color = '';
                    lineHandle.lineElement.style.fontSize = '';
                    lineHandle.lineElement.style.lineHeight = '';
                    lineHandle.lineElement.style.height = '';
                    lineHandle.lineElement.style.overflow = '';
                }
            }
            
            if (lineHandle.widgets) {
                // If line doesn't have an image marker, remove the widget
                if (!line || !line.includes('[IMAGE:')) {
                    lineHandle.widgets.forEach(widget => {
                        if (widget.node && widget.node.classList.contains('resizable-image-container')) {
                            widget.clear();
                        }
                    });
                }
            }
        }
        
        // Then, add images for lines that have markers but no widgets
        for (let i = 0; i < lineCount; i++) {
            const line = doc.getLine(i);
            if (line && line.includes('[IMAGE:')) {
                const match = line.match(/\[IMAGE:(.*?)(?::(.*?):(.*?))?\]/);
                if (match) {
                    const imageUrl = match[1];
                    const width = match[2] || null;
                    const height = match[3] || null;
                    const lineHandle = doc.getLineHandle(i);
                    
                    // Mark this line as an image line and hide the text
                    if (lineHandle && lineHandle.lineElement) {
                        lineHandle.lineElement.setAttribute('data-image-line', 'true');
                        lineHandle.lineElement.style.color = 'transparent';
                        lineHandle.lineElement.style.fontSize = '0';
                        lineHandle.lineElement.style.lineHeight = '0';
                        lineHandle.lineElement.style.height = '0';
                        lineHandle.lineElement.style.overflow = 'hidden';
                    }
                    
                    // Check if this line already has an image widget
                    const hasImageWidget = lineHandle.widgets && 
                        lineHandle.widgets.some(widget => 
                            widget.node && widget.node.classList.contains('resizable-image-container')
                        );
                    
                    if (!hasImageWidget) {
                        // Create image container with resize handles
                        const imgContainer = createResizableImage(imageUrl, i, width, height);
                        
                        // Add as line widget (doesn't interfere with text)
                        codeMirror.addLineWidget(i, imgContainer, {
                            coverGutter: false,
                            noHScroll: true
                        });
                    }
                }
            }
        }
    }
    
    // Create resizable image with MS Word-like handles
    function createResizableImage(imageUrl, lineNumber, width = null, height = null) {
        // Create container
        const container = document.createElement('div');
        container.className = 'resizable-image-container';
        container.style.position = 'relative';
        container.style.display = 'inline-block';
        container.style.margin = '10px 0';
        container.style.maxWidth = '100%';
        
        // Create image element
        const img = document.createElement('img');
        img.src = imageUrl;
        img.alt = 'Uploaded image';
        img.style.display = 'block';
        img.style.border = '1px solid #ddd';
        img.style.borderRadius = '4px';
        img.style.padding = '5px';
        img.style.backgroundColor = '#f9f9f9';
        img.style.cursor = 'pointer';
        img.style.userSelect = 'none';
        
        // Apply custom size if provided
        if (width && height) {
            img.style.width = width;
            img.style.height = height;
            img.style.maxWidth = 'none';
        } else {
            img.style.maxWidth = '100%';
            img.style.height = 'auto';
        }
        
        // Add click event to show resize handles
        img.addEventListener('click', function(e) {
            e.stopPropagation();
            selectImage(container, lineNumber);
        });
        
        // Make the container focusable for keyboard events
        container.tabIndex = -1;
        container.setAttribute('data-line-number', lineNumber);
        container.setAttribute('data-image-url', imageUrl);
        
        // Create resize handles (hidden by default)
        const handles = createResizeHandles(container, img, lineNumber, imageUrl);
        
        container.appendChild(img);
        handles.forEach(handle => container.appendChild(handle));
        
        return container;
    }
    
    // Create resize handles for image
    function createResizeHandles(container, img, lineNumber, imageUrl) {
        const handles = [];
        const handlePositions = [
            { pos: 'nw', cursor: 'nw-resize', x: 0, y: 0 },
            { pos: 'ne', cursor: 'ne-resize', x: 1, y: 0 },
            { pos: 'sw', cursor: 'sw-resize', x: 0, y: 1 },
            { pos: 'se', cursor: 'se-resize', x: 1, y: 1 },
            { pos: 'n', cursor: 'n-resize', x: 0.5, y: 0 },
            { pos: 's', cursor: 's-resize', x: 0.5, y: 1 },
            { pos: 'w', cursor: 'w-resize', x: 0, y: 0.5 },
            { pos: 'e', cursor: 'e-resize', x: 1, y: 0.5 }
        ];
        
        handlePositions.forEach(handleInfo => {
            const handle = document.createElement('div');
            handle.className = `resize-handle resize-handle-${handleInfo.pos}`;
            handle.style.position = 'absolute';
            handle.style.width = '8px';
            handle.style.height = '8px';
            handle.style.backgroundColor = '#007bff';
            handle.style.border = '1px solid #fff';
            handle.style.borderRadius = '2px';
            handle.style.cursor = handleInfo.cursor;
            handle.style.display = 'none';
            handle.style.zIndex = '10';
            handle.style.boxShadow = '0 1px 3px rgba(0,0,0,0.3)';
            
            // Position the handle
            if (handleInfo.x === 0) handle.style.left = '-4px';
            else if (handleInfo.x === 1) handle.style.right = '-4px';
            else handle.style.left = 'calc(50% - 4px)';
            
            if (handleInfo.y === 0) handle.style.top = '-4px';
            else if (handleInfo.y === 1) handle.style.bottom = '-4px';
            else handle.style.top = 'calc(50% - 4px)';
            
            // Add resize functionality
            handle.addEventListener('mousedown', function(e) {
                e.preventDefault();
                e.stopPropagation();
                startResize(e, container, img, handleInfo, lineNumber, imageUrl);
            });
            
            handles.push(handle);
        });
        
        return handles;
    }
    
    // Select image and show resize handles
    function selectImage(container, lineNumber) {
        // Deselect all other images
        document.querySelectorAll('.resizable-image-container').forEach(c => {
            if (c !== container) {
                c.classList.remove('selected');
                c.querySelectorAll('.resize-handle').forEach(h => h.style.display = 'none');
                const img = c.querySelector('img');
                img.style.border = '1px solid #ddd';
            }
        });
        
        // Select this image
        container.classList.add('selected');
        container.querySelectorAll('.resize-handle').forEach(h => h.style.display = 'block');
        
        // Add selection border
        const img = container.querySelector('img');
        img.style.border = '2px solid #007bff';
        
        // Focus the container for keyboard events
        container.focus();
        
        // Position cursor before the image in CodeMirror
        if (codeMirror) {
            codeMirror.setCursor({ line: lineNumber, ch: 0 });
            codeMirror.focus();
        }
        
        // Set up keyboard event listeners
        setupImageKeyboardHandlers(container, lineNumber);
    }
    
    // Start resize operation
    function startResize(e, container, img, handleInfo, lineNumber, imageUrl) {
        const startX = e.clientX;
        const startY = e.clientY;
        const startWidth = img.offsetWidth;
        const startHeight = img.offsetHeight;
        const aspectRatio = startWidth / startHeight;
        
        function onMouseMove(e) {
            const deltaX = e.clientX - startX;
            const deltaY = e.clientY - startY;
            
            let newWidth = startWidth;
            let newHeight = startHeight;
            
            // Calculate new dimensions based on handle position
            if (handleInfo.pos.includes('e')) {
                newWidth = startWidth + deltaX;
            } else if (handleInfo.pos.includes('w')) {
                newWidth = startWidth - deltaX;
            }
            
            if (handleInfo.pos.includes('s')) {
                newHeight = startHeight + deltaY;
            } else if (handleInfo.pos.includes('n')) {
                newHeight = startHeight - deltaY;
            }
            
            // Maintain aspect ratio for corner handles
            if (handleInfo.pos.length === 2) {
                if (Math.abs(deltaX) > Math.abs(deltaY)) {
                    newHeight = newWidth / aspectRatio;
                } else {
                    newWidth = newHeight * aspectRatio;
                }
            }
            
            // Set minimum size
            newWidth = Math.max(50, newWidth);
            newHeight = Math.max(50, newHeight);
            
            // Set maximum size (container width)
            const maxWidth = container.parentElement.offsetWidth - 20;
            if (newWidth > maxWidth) {
                newWidth = maxWidth;
                newHeight = newWidth / aspectRatio;
            }
            
            // Apply new dimensions
            img.style.width = newWidth + 'px';
            img.style.height = newHeight + 'px';
            img.style.maxWidth = 'none';
        }
        
        function onMouseUp() {
            document.removeEventListener('mousemove', onMouseMove);
            document.removeEventListener('mouseup', onMouseUp);
            
            // Update the image marker in the document with new size
            updateImageSize(lineNumber, imageUrl, img.style.width, img.style.height);
        }
        
        document.addEventListener('mousemove', onMouseMove);
        document.addEventListener('mouseup', onMouseUp);
    }
    
    // Update image size in the document
    function updateImageSize(lineNumber, imageUrl, width, height) {
        if (!codeMirror) return;
        
        const doc = codeMirror.getDoc();
        const line = doc.getLine(lineNumber);
        
        if (line && line.includes('[IMAGE:')) {
            // Create new image marker with size information
            const newMarker = `[IMAGE:${imageUrl}:${width}:${height}]`;
            const newLine = line.replace(/\[IMAGE:.*?\]/, newMarker);
            
            // Replace the line
            doc.replaceRange(newLine, 
                { line: lineNumber, ch: 0 }, 
                { line: lineNumber, ch: line.length }
            );
        }
    }
    
    // Setup keyboard handlers for selected image
    function setupImageKeyboardHandlers(container, lineNumber) {
        // Remove any existing handlers
        container.removeEventListener('keydown', container._imageKeyHandler);
        
        // Create new handler
        container._imageKeyHandler = function(e) {
            handleImageKeyboard(e, container, lineNumber);
        };
        
        // Add keyboard event listener
        container.addEventListener('keydown', container._imageKeyHandler);
    }
    
    // Handle keyboard events for images (MS Word style)
    function handleImageKeyboard(e, container, lineNumber) {
        const imageUrl = container.getAttribute('data-image-url');
        
        switch(e.key) {
            case 'Delete':
            case 'Backspace':
                e.preventDefault();
                deleteImageAtLine(lineNumber);
                break;
                
            case 'ArrowLeft':
                e.preventDefault();
                moveToImageStart(lineNumber);
                break;
                
            case 'ArrowRight':
                e.preventDefault();
                moveToImageEnd(lineNumber);
                break;
                
            case 'ArrowUp':
                e.preventDefault();
                moveToLineBefore(lineNumber);
                break;
                
            case 'ArrowDown':
                e.preventDefault();
                moveToLineAfter(lineNumber);
                break;
                
            case 'Enter':
                e.preventDefault();
                insertLineAfterImage(lineNumber);
                break;
                
            case 'Escape':
                e.preventDefault();
                deselectAllImages();
                break;
        }
    }
    
    // Delete image at specific line
    function deleteImageAtLine(lineNumber) {
        if (!codeMirror) return;
        
        const doc = codeMirror.getDoc();
        const line = doc.getLine(lineNumber);
        
        if (line && line.includes('[IMAGE:')) {
            // Remove the entire line
            doc.replaceRange('', 
                { line: lineNumber, ch: 0 }, 
                { line: lineNumber + 1, ch: 0 }
            );
            
            // Focus back to editor
            codeMirror.focus();
            
            // Position cursor where the image was
            codeMirror.setCursor({ line: lineNumber, ch: 0 });
        }
    }
    
    // Move cursor to start of image line
    function moveToImageStart(lineNumber) {
        if (!codeMirror) return;
        
        codeMirror.setCursor({ line: lineNumber, ch: 0 });
        codeMirror.focus();
        deselectAllImages();
    }
    
    // Move cursor to end of image line
    function moveToImageEnd(lineNumber) {
        if (!codeMirror) return;
        
        const doc = codeMirror.getDoc();
        const line = doc.getLine(lineNumber);
        const lineLength = line ? line.length : 0;
        
        codeMirror.setCursor({ line: lineNumber, ch: lineLength });
        codeMirror.focus();
        deselectAllImages();
    }
    
    // Move cursor to line before image
    function moveToLineBefore(lineNumber) {
        if (!codeMirror) return;
        
        const targetLine = Math.max(0, lineNumber - 1);
        const doc = codeMirror.getDoc();
        const line = doc.getLine(targetLine);
        const lineLength = line ? line.length : 0;
        
        codeMirror.setCursor({ line: targetLine, ch: lineLength });
        codeMirror.focus();
        deselectAllImages();
    }
    
    // Move cursor to line after image
    function moveToLineAfter(lineNumber) {
        if (!codeMirror) return;
        
        const doc = codeMirror.getDoc();
        const targetLine = Math.min(doc.lineCount() - 1, lineNumber + 1);
        
        codeMirror.setCursor({ line: targetLine, ch: 0 });
        codeMirror.focus();
        deselectAllImages();
    }
    
    // Insert new line after image
    function insertLineAfterImage(lineNumber) {
        if (!codeMirror) return;
        
        const doc = codeMirror.getDoc();
        
        // Insert a new line after the image
        doc.replaceRange('\n', { line: lineNumber + 1, ch: 0 });
        
        // Move cursor to the new line
        codeMirror.setCursor({ line: lineNumber + 1, ch: 0 });
        codeMirror.focus();
        deselectAllImages();
    }
    
    // Deselect all images
    function deselectAllImages() {
        document.querySelectorAll('.resizable-image-container').forEach(container => {
            container.classList.remove('selected', 'cursor-selected');
            container.querySelectorAll('.resize-handle').forEach(h => h.style.display = 'none');
            const img = container.querySelector('img');
            img.style.border = '1px solid #ddd';
            img.style.backgroundColor = '#f9f9f9';
            
            // Remove keyboard handler
            if (container._imageKeyHandler) {
                container.removeEventListener('keydown', container._imageKeyHandler);
                container._imageKeyHandler = null;
            }
        });
    }
    
    // Deselect images when clicking elsewhere
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.resizable-image-container')) {
            deselectAllImages();
        }
    });
    
    // Print document function - extract HTML and print in new window
    function printDocument() {
        if (!firepad) {
            alert('Editor not ready');
            return;
        }
        
        // Force image rendering before getting HTML
        renderImages();
        
        // Small delay to ensure images are rendered
        setTimeout(() => {
            // Get the full HTML from Firepad
            let htmlContent = firepad.getHtml();
            
            // Process image markers in the HTML content
            htmlContent = htmlContent.replace(/\[IMAGE:([^\]]+)\]/g, function(match, imageData) {
                const parts = imageData.split(':');
                if (parts.length >= 3) {
                    const url = parts[0];
                    const width = parts[1];
                    const height = parts[2];
                    
                    // Clean up width/height values (remove any existing 'px')
                    const cleanWidth = width.replace(/px$/, '');
                    const cleanHeight = height.replace(/px$/, '');
                    
                    return `<img src="${url}" style="max-width: 100%; width: ${cleanWidth}px; height: ${cleanHeight}px; display: block; margin: 10px 0;" />`;
                } else if (parts.length === 1) {
                    // Incomplete image marker - just URL, add default dimensions
                    const url = parts[0];
                    return `<img src="${url}" style="max-width: 100%; width: 400px; height: 300px; display: block; margin: 10px 0;" />`;
                }
                return match; // Return original if can't parse
            });
            
            // Create a new window for printing
            const printWindow = window.open('', '_blank', 'width=800,height=600');
            
            // Write the HTML content to the new window
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="UTF-8">
                    <title>Document Print</title>
                    <style>
                        body {
                            font-family: 'Calibri', 'Segoe UI', -apple-system, BlinkMacSystemFont, sans-serif;
                            font-size: 11pt;
                            line-height: 1.15;
                            margin: 0;
                            padding: 0.5in;
                            color: #000;
                            background: white;
                        }
                        img {
                            max-width: 100%;
                            height: auto;
                            display: block;
                            margin: 10px 0;
                        }
                        p {
                            margin: 0 0 1em 0;
                        }
                        @media print {
                            body {
                                margin: 0;
                                padding: 0.5in;
                            }
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
                    </style>
                </head>
                <body>
                    ${htmlContent}
                </body>
                </html>
            `);
            
            printWindow.document.close();
            
            // Wait for content to load, then print
            printWindow.onload = function() {
                setTimeout(() => {
                    printWindow.print();
                    // Close the window after printing
                    setTimeout(() => {
                        printWindow.close();
                    }, 1000);
                }, 500);
            };
        }, 100);
    }
    
    // Get editor content for printing with proper image handling
    function getEditorContentForPrint() {
        if (!codeMirror) return '';
        
        // Get the text content
        let content = codeMirror.getValue();
        
        // Convert image markers to proper HTML for printing
        content = content.replace(/\[IMAGE:(.*?)(?::(.*?):(.*?))?\]/g, function(match, imageUrl, width, height) {
            let style = "display: block; margin: 20px auto; border: 1px solid #ddd; border-radius: 4px; padding: 10px; background: #f9f9f9;";
            
            if (width && height) {
                style += ` width: ${width}; height: ${height};`;
            } else {
                style += " max-width: 100%; height: auto;";
            }
            
            return `<img src="${imageUrl}" alt="Uploaded image" style="${style}" />`;
        });
        
        return content;
    }
    
    // Format content for printing (convert HTML to proper format)
    function formatContentForPrint(content, baseUrl) {
        // Convert line breaks to proper HTML
        let formatted = content.replace(/\n/g, '<br>');
        
        // Convert basic formatting
        formatted = formatted.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        formatted = formatted.replace(/\*(.*?)\*/g, '<em>$1</em>');
        
        // Handle images - convert relative URLs to absolute URLs
        formatted = formatted.replace(/<img([^>]*?)src="([^"]*?)"([^>]*?)>/g, function(match, before, src, after) {
            // Convert relative URLs to absolute URLs
            let absoluteSrc = src;
            if (src.startsWith('/')) {
                absoluteSrc = baseUrl + src;
            } else if (!src.startsWith('http')) {
                absoluteSrc = baseUrl + '/' + src;
            }
            
            return `<img${before}src="${absoluteSrc}"${after} style="max-width: 100%; height: auto; display: block; margin: 20px auto; border: 1px solid #ddd; border-radius: 4px; padding: 10px; background: #f9f9f9;">`;
        });
        
        return formatted;
    }
</script>

