<?php
/**
 * File Manager Index Template
 */
$this->assign('title', 'RBTkaFiles');
?>

<div id="file-manager" class="container-fluid">
    <div class="row">
        <div class="col-md-12" id="main-panel">
            <!-- Toolbar -->
            <div class="actions-toolbar">
                <button class="action-button" id="btn-create-folder">
                    <i class="fas fa-folder-plus"></i>
                    Create Folder
                </button>
                
                <label class="action-button" for="file-input">
                    <i class="fas fa-cloud-upload"></i>
                    Upload Files
                    <input type="file" id="file-input" multiple hidden>
                </label>
            </div>

            <!-- Upload Progress (moved to top) -->
            <div class="upload-progress" id="upload-progress" style="display: none;">
                <div class="d-flex align-items-center">
                    <div class="mr-2">Uploading...</div>
                    <div class="progress flex-grow-1">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                    </div>
                    <div class="ml-2 upload-percentage">0%</div>
                </div>
            </div>

            <!-- Breadcrumb Navigation -->
            <nav aria-label="breadcrumb">
                <ol id="breadcrumb" class="breadcrumb">
                    <!-- Breadcrumbs will be populated by JavaScript -->
                </ol>
            </nav>

            <!-- File Listing -->
            <div id="file-listing" class="list-group">
                <!-- Add table structure -->
                <div class="table-responsive" id="file-table-wrapper">
                <table id="file-table" class="table table-hover">
                    <thead>
                        <tr>
                            <th class="file-name-header">Name</th>
                            <th class="file-size-header text-end">Size</th>
                        </tr>
                    </thead>
                    <tbody id="file-table-body">
                        <!-- File items will be inserted here by JavaScript -->
                    </tbody>
                </table>
                </div>
                
                <!-- Loading spinner positioned below table headers -->
                <div id="loading" class="text-center py-3">
                    <div class="spinner-border" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div class="mt-2">Loading files...</div>
                </div>
                
                <div id="empty-folder" class="text-center py-5" style="display: none;">
                    <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                    <p class="text-muted">This folder is empty</p>
                </div>
            </div>
        </div>
        
        <!-- Preview Panel -->
        <div class="col-md-4" id="preview-panel" style="display: none;">
            <div class="preview-container">
                <div class="preview-header">
                    <div class="preview-title">
                        <i class="fas fa-file preview-icon"></i>
                        <span class="preview-filename">No file selected</span>
                    </div>
                    <button type="button" class="close-preview">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="preview-content">
                    <div id="preview-loading" class="text-center py-4" style="display: none;">
                        <div class="spinner-border" role="status"></div>
                        <div class="mt-2">Loading preview...</div>
                    </div>
                    
                    <div id="preview-body">
                        <div class="preview-placeholder text-center py-5">
                            <i class="fas fa-eye fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Click on a file to preview it here</p>
                        </div>
                    </div>
                </div>
                

            </div>
        </div>
    </div>
</div>

<!-- Create Folder Modal -->
<div class="modal fade" id="createFolderModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Folder</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="folder-name">Folder Name:</label>
                    <input type="text" id="folder-name" class="form-control" placeholder="Enter folder name">
                    <div id="folder-name-error" class="text-danger mt-2" style="display: none;"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" id="btn-create-folder-confirm" class="btn btn-primary">Create</button>
            </div>
        </div>
    </div>
</div>

<!-- Rename Modal -->
<div class="modal fade" id="renameModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Rename Item</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="rename-input">New Name:</label>
                    <input type="text" id="rename-input" class="form-control">
                    <div id="rename-extension-info" class="mt-2" style="display: none;"></div>
                    <div id="rename-error" class="text-danger mt-2" style="display: none;"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" id="btn-rename-confirm" class="btn btn-primary">Rename</button>
            </div>
        </div>
    </div>
</div>

<!-- Context Menu -->
<div id="context-menu" class="context-menu" style="display: none;">
    <ul>
        <li><a href="javascript:void(0)" id="ctx-download"><i class="fas fa-download"></i> Download</a></li>
        <li><a href="javascript:void(0)" id="ctx-print" style="display: none;"><i class="fas fa-print"></i> Print</a></li>
        <li><a href="javascript:void(0)" id="ctx-rename"><i class="fas fa-edit"></i> Rename</a></li>
        <li class="divider"></li>
        <li><a href="javascript:void(0)" id="ctx-delete"><i class="fas fa-trash"></i> Delete</a></li>
    </ul>
</div>

<!-- Upload Progress Modal -->
<div class="modal fade" id="uploadProgressModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Uploading Files</h5>
            </div>
            <div class="modal-body">
                <div id="upload-progress">
                    <!-- Progress bars will be added here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="btn-upload-close" class="btn btn-secondary" data-dismiss="modal" style="display: none;">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Print Modal -->
<div class="modal fade" id="printModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-print"></i> Print PDF
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="print-form" action="javascript:void(0)">
                <div class="modal-body">
                    <p class="text-muted mb-3">
                        
                        
                    </p>
                    
                    <div class="form-group">
                        <label for="print-roll">Roll Number <span class="text-danger">*</span></label>
                        <input type="text" id="print-roll" name="roll" class="form-control" 
                               placeholder="Enter your 7-digit roll number" value="50221" required maxlength="7">
                        
                    </div>
                    
                    <div class="form-group">
                        <label for="print-name">Name <span class="text-danger">*</span></label>
                        <input type="text" id="print-name" name="name" class="form-control" 
                               placeholder="Enter your full name" required>
                        <div id="user-status" class="mt-2" style="display: none;">
                            
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="print-lab">Lab Name (Optional)</label>
                        <input type="text" id="print-lab" name="lab" class="form-control" 
                               placeholder="No need to type 'Lab'">
                    </div>
                    
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" id="btn-print-confirm" class="btn btn-primary">
                        <i class="fas fa-print"></i> Print PDF
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="file-manager">
    <div id="notes-section" class="notes-section" style="display: none;">
        <div class="notes-header d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Notes</h5>
            <button id="add-note" class="btn btn-sm btn-primary">
                <i class="fas fa-plus"></i> Add Note
            </button>
        </div>
        <div id="notes-list">
            <!-- Notes will be added here dynamically -->
        </div>
    </div>
</div>

<style>
/* Base styles */
body {
  background-color: #f5f7fa;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
}

.container-fluid#file-manager {
  width: 90%;
  margin-left: auto;
  margin-right: auto;
}

@media (max-width: 992px) {
  .container-fluid#file-manager { width: 100%; }
}

.container {
  max-width: 1200px;
  margin: 2rem auto;
}

/* File and Folder Listing Styles */
.list-group-item {
  transition: all 0.2s ease;
  border-left: 4px solid transparent;
  display: flex;
  align-items: center;
  padding: 1rem 1.5rem;
  border: none;
  border-bottom: 1px solid #edf2f7;
}

.list-group-item:hover {
  transform: translateX(5px);
  border-left-color: #4ecdc4;
  background-color: #f8f9fa;
}

.bi-folder-fill { color: #ff9f43; }
.bi-file-earmark-pdf { color: #ff6b6b; }
.bi-file-earmark-word { color: #2e86de; }
.bi-file-earmark-text { color: #718096; }

.file-icon {
  font-size: 1.25rem;
  margin-right: 1rem;
}

.file-name {
  flex: 1;
  font-weight: 500;
}

.file-details {
  color: #718096;
  font-size: 0.875rem;
  display: flex;
  align-items: center;
  gap: 2rem;
}

.drop-target.dragover { 
  background-color: #e9ecef !important; 
  border-radius: 0.25rem; 
}

/* Breadcrumb styles */
.breadcrumb {
  background-color: #fff;
  padding: 0.75rem 1rem;
  border-radius: 0.5rem;
  box-shadow: 0 2px 4px rgba(0,0,0,0.05);
  margin-bottom: 1.5rem;
}

/* Toolbar styles */
.actions-toolbar {
    display: flex;
    gap: 1rem;
    margin-bottom: 2rem;
}

.action-button {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.625rem 1rem;
    height: 42px;
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 9999px;
    color: #1f2937;
    font-weight: 600;
    letter-spacing: .2px;
    transition: background-color .15s ease, border-color .15s ease, box-shadow .15s ease;
    cursor: pointer;
    line-height: 1;
    box-shadow: 0 1px 2px rgba(0,0,0,.05);
}

.action-button:hover {
    background: #f9fafb;
    border-color: #d1d5db;
}

.action-button:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(14,165,233,.25);
    border-color: #93c5fd;
}

.action-button:active {
    background: #f3f4f6;
}

/* Progress bar for uploads */
.upload-progress {
    background: white;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.progress {
    height: 6px;
    border-radius: 3px;
    background: #edf2f7;
    margin: 0 1rem;
    flex: 1;
}

.progress-bar { 
    background: #4ecdc4;
    border-radius: 3px;
    transition: width 0.2s ease;
}

.upload-percentage {
    min-width: 48px;
    text-align: right;
    font-weight: 500;
    color: #4a5568;
}

/* Table styles */
#file-table {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    margin-bottom: 0;
}

#file-table thead th {
    background: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
    color: #495057;
    padding: 12px 16px;
    border-top: none;
}

.file-name-header {
    width: 80%;
}

.file-size-header {
    width: 20%;
}

/* File Row Items */
.file-item {
    cursor: pointer;
    transition: all 0.2s ease;
}

.file-item:hover {
    background-color: rgba(0, 255, 255, 0.05) !important;
}

.file-item.selected {
    background-color: rgba(0, 255, 255, 0.1) !important;
    border-left: 4px solid #0ff;
}

.file-item td {
    padding: 12px 16px;
    vertical-align: middle;
    border-bottom: 1px solid #edf2f7;
}

.file-name-cell {
    font-weight: 500;
}

.file-name-cell .d-flex {
    align-items: center;
    gap: 12px;
}

.file-name-cell .file-icon {
    font-size: 1.25rem;
    width: 20px;
    text-align: center;
    flex-shrink: 0;
}

.file-name {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    flex: 1;
    min-width: 0;
}

.file-type-cell {
    color: #6c757d;
    font-size: 0.9rem;
}

.file-size-cell {
    font-family: 'Courier New', monospace;
    font-size: 0.85rem;
    color: #6c757d;
}

/* Media queries for responsive table */
@media (max-width: 768px) {
    .file-name-header {
        width: 80%;
    }
    
    .file-size-header {
        width: 20%;
    }
    
    .file-name { max-width: none; }
}

@media (max-width: 576px) {
    #file-table td {
        padding: 8px;
    }
    
    .file-name { max-width: none; font-size: 0.9rem; }
    
    .file-size-cell {
        font-size: 0.8rem;
    }
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .actions-toolbar {
    flex-direction: column;
  }
  
  .action-button {
    width: 100%;
  }
  
  .file-details {
    display: none;
  }
}

/* Mobile horizontal scroll for file table */
#file-table-wrapper { width: 100%; }
@media (max-width: 768px) {
    #file-table-wrapper { overflow-x: auto; -webkit-overflow-scrolling: touch; }
    #file-table { min-width: 600px; }
}

/* Notes Section Styling */
.notes-section {
    margin: 2rem auto;
    padding: 1.5rem;
    background: white;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    max-width: 50%;
}

.note-item {
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 1rem;
    margin-bottom: 1rem;
    background: #f8fafc;
}

.note-item:last-child {
    margin-bottom: 0;
}

.note-header {
    margin-bottom: 0.5rem;
}

.note-timestamp {
    font-size: 0.85rem;
    color: #718096;
}

.note-actions {
    opacity: 0;
    transition: opacity 0.2s ease;
}

.note-item:hover .note-actions {
    opacity: 1;
}

.note-content {
    font-family: 'Fira Mono', 'Consolas', 'Monaco', 'Courier New', monospace;
    font-size: 0.95rem;
    line-height: 1.6;
    color: #2c3e50;
    white-space: pre-wrap;
    word-break: break-word;
    background: none;
    border: none;
    padding: 1.5rem;
    margin: 0;
    transition: max-height 0.4s ease-out;
}

/* Collapsed state with gradient fade */
.note-content.collapsed {
    max-height: 8.5em;
    overflow: hidden;
    position: relative;
}

.note-content.collapsed::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 40px;
    background: linear-gradient(transparent, #ffffff 90%);
    pointer-events: none;
}

/* Custom scrollbar for longer notes */
.note-content:not(.collapsed) {
    max-height: 500px;
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: #4ecdc4 #f1f1f1;
}

.note-content:not(.collapsed)::-webkit-scrollbar {
    width: 6px;
}

.note-content:not(.collapsed)::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.note-content:not(.collapsed)::-webkit-scrollbar-thumb {
    background: #4ecdc4;
    border-radius: 3px;
}

.note-content:not(.collapsed)::-webkit-scrollbar-thumb:hover {
    background: #3aa89e;
}

.note-editor {
    width: 100%;
    min-height: 100px;
    padding: 0.75rem;
    margin: 0.5rem 0;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    font-family: inherit;
    font-size: 0.95rem;
    line-height: 1.6;
    resize: vertical;
}

/* Read More Button Styling */
.read-more-btn {
    background: none;
    border: none;
    color: #4ecdc4;
    font-size: 0.9rem;
    font-weight: 500;
    padding: 0.5rem 0;
    margin-top: 0.5rem;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-block;
    position: relative;
}

.read-more-btn:hover {
    color: #3aa89e;
    text-decoration: underline;
}

.read-more-btn:focus {
    outline: none;
    box-shadow: 0 0 0 2px rgba(78, 205, 196, 0.3);
}

.read-more-btn::after {
    content: ' ↴';
    font-size: 1rem;
    transition: transform 0.3s ease;
}

.read-more-btn:hover::after {
    transform: translateY(2px);
}

/* When expanded (Show Less state) */
.read-more-btn[data-state="expanded"]::after {
    content: ' ↰';
}

.read-more-btn[data-state="expanded"]:hover::after {
    transform: translateY(-2px);
}

/* Text formatting within notes */
.note-content strong {
    font-weight: 600;
    color: #2c3e50;
}

.note-content em {
    font-style: italic;
    color: #6c757d;
}

.note-content u {
    text-decoration: underline;
    text-decoration-color: #4ecdc4;
}

.note-content code {
    background: #f8f9fa;
    padding: 0.2rem 0.4rem;
    border-radius: 4px;
    font-size: 0.85em;
    color: #dc3545;
}

/* Line breaks and paragraphs */
.note-content br {
    display: block;
    content: "";
    margin-bottom: 0.5rem;
}

/* Responsive text sizing */
@media (max-width: 768px) {
    .note-content {
        font-size: 0.9rem;
        padding: 1rem;
        line-height: 1.5;
    }
    
    .read-more-btn {
        font-size: 0.85rem;
    }
    
    .note-content.collapsed {
        max-height: 7em;
    }
}

@media (max-width: 992px) {
    .notes-section {
        max-width: 100%;
    }
}

/* Notes Actions */
.notes-actions {
    margin-top: 1rem;
    display: flex;
    gap: 0.5rem;
    justify-content: flex-end;
}

/* Edit Notes Button */
#edit-notes {
    margin-top: 1rem;
    width: 100%;
}

/* Notes Text Styling */
.note-content {
    font-family: 'Fira Mono', 'Consolas', 'Monaco', 'Courier New', monospace;
    font-size: 0.95rem;
    line-height: 1.6;
    color: #2c3e50;
    white-space: pre-wrap;
    word-break: break-word;
    background: none;
    border: none;
    padding: 1.5rem;
    margin: 0;
}

/* Collapsed state with gradient fade */
.note-content.collapsed {
    max-height: 8.5em;
    overflow: hidden;
    position: relative;
}

.note-content.collapsed::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 40px;
    background: linear-gradient(transparent, #ffffff 90%);
    pointer-events: none;
}

/* Read More Button Styling */
.read-more-btn {
    background: none;
    border: none;
    color: #4ecdc4;
    font-size: 0.9rem;
    font-weight: 500;
    padding: 0.5rem 0;
    margin-top: 0.5rem;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-block;
    position: relative;
}

.read-more-btn:hover {
    color: #3aa89e;
    text-decoration: underline;
}

.read-more-btn:focus {
    outline: none;
    box-shadow: 0 0 0 2px rgba(78, 205, 196, 0.3);
}

.read-more-btn::after {
    content: ' ↴';
    font-size: 1rem;
    transition: transform 0.3s ease;
}

.read-more-btn:hover::after {
    transform: translateY(2px);
}

/* When expanded (Show Less state) */
.read-more-btn[data-state="expanded"]::after {
    content: ' ↰';
}

.read-more-btn[data-state="expanded"]:hover::after {
    transform: translateY(-2px);
}

/* Text formatting within notes */
.note-content strong {
    font-weight: 600;
    color: #2c3e50;
}

.note-content em {
    font-style: italic;
    color: #6c757d;
}

.note-content u {
    text-decoration: underline;
    text-decoration-color: #4ecdc4;
}

.note-content code {
    background: #f8f9fa;
    padding: 0.2rem 0.4rem;
    border-radius: 4px;
    font-size: 0.85em;
    color: #dc3545;
}

/* Line breaks and paragraphs */
.note-content br {
    display: block;
    content: "";
    margin-bottom: 0.5rem;
}

/* Responsive text sizing */
@media (max-width: 768px) {
    .note-content {
        font-size: 0.9rem;
        padding: 1rem;
        line-height: 1.5;
    }
    
    .read-more-btn {
        font-size: 0.85rem;
    }
    
    .note-content.collapsed {
        max-height: 7em;
    }
}

/* Smooth transitions for text expansion */
.note-content {
    transition: max-height 0.4s ease-out;
}

/* Custom scrollbar for longer notes */
.note-content:not(.collapsed) {
    max-height: 500px;
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: #4ecdc4 #f1f1f1;
}

.note-content:not(.collapsed)::-webkit-scrollbar {
    width: 6px;
}

.note-content:not(.collapsed)::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.note-content:not(.collapsed)::-webkit-scrollbar-thumb {
    background: #4ecdc4;
    border-radius: 3px;
}

.note-content:not(.collapsed)::-webkit-scrollbar-thumb:hover {
    background: #3aa89e;
}

/* Preview Panel Button Styles */
.preview-filename .btn {
    margin: 2px;
    font-size: 0.8rem;
    padding: 0.375rem 0.75rem;
    border-radius: 0.375rem;
    transition: all 0.2s ease;
}

.preview-filename .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.preview-filename .btn-outline-primary:hover {
    background-color: #007bff;
    border-color: #007bff;
    color: white;
}

.preview-filename .btn-outline-success:hover {
    background-color: #28a745;
    border-color: #28a745;
    color: white;
}

/* Ensure buttons don't wrap awkwardly */
.preview-filename {
    flex-wrap: nowrap;
    align-items: center;
    justify-content: center;
}

/* Office Preview Styles */
.office-preview {
    text-align: center;
}

.office-preview .preview-iframe {
    width: 100%;
    height: 500px;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

/* Responsive adjustments for office preview */
@media (max-width: 768px) {
    .office-preview .preview-iframe {
        height: 300px;
    }
}

/* Responsive text sizing */
@media (max-width: 768px) {
    .note-content {
        font-size: 0.9rem;
        padding: 1rem;
        line-height: 1.5;
    }
    
    .read-more-btn {
        font-size: 0.85rem;
    }
    
    .note-content.collapsed {
        max-height: 7em;
    }
}

/* Smooth transitions for text expansion */
.note-content {
    transition: max-height 0.4s ease-out;
}

/* Custom scrollbar for longer notes */
.note-content:not(.collapsed) {
    max-height: 500px;
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: #4ecdc4 #f1f1f1;
}

.note-content:not(.collapsed)::-webkit-scrollbar {
    width: 6px;
}

.note-content:not(.collapsed)::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.note-content:not(.collapsed)::-webkit-scrollbar-thumb {
    background: #4ecdc4;
    border-radius: 3px;
}

.note-content:not(.collapsed)::-webkit-scrollbar-thumb:hover {
    background: #3aa89e;
}
</style>

<?php
// Add scripts to the script block that will be loaded after jQuery
$this->Html->scriptStart(['block' => true]);
?>
// Initialize current path
window.currentPath = '<?= h($path) ?>';
<?php 
$this->Html->scriptEnd();

// Add the notes CSS inline to avoid loading issues

// Add the file manager script
$this->Html->script('file-manager', ['block' => true]);
?>
