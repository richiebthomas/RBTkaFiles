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
            <div class="toolbar mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="toolbar-left">
                        <button id="btn-create-folder" class="btn btn-primary btn-sm">
                            <i class="fas fa-folder-plus"></i> New Folder
                        </button>
                        <button id="btn-upload" class="btn btn-success btn-sm">
                            <i class="fas fa-upload"></i> Upload Files
                        </button>
                        <input type="file" id="file-input" multiple style="display: none;">
                    </div>
                    <div class="toolbar-right">
                        
                        <button id="btn-refresh" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    </div>
                </div>
            </div>

            <!-- Breadcrumb Navigation -->
            <nav aria-label="breadcrumb">
                <ol id="breadcrumb" class="breadcrumb">
                    <!-- Breadcrumbs will be populated by JavaScript -->
                </ol>
            </nav>

            <!-- File Listing -->
            <div id="file-listing" class="file-listing">
                <div id="loading" class="text-center py-5">
                    <div class="spinner-border" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div class="mt-2">Loading files...</div>
                </div>

                <table id="file-table" class="table table-hover" style="display: none;">
                    <thead class="table-light">
                        <tr>
                            <th class="file-name-header">
                                <i class="fas fa-file me-2"></i>Name
                            </th>
                            <th class="file-type-header">
                                <i class="fas fa-tag me-2"></i>Type
                            </th>
                            <th class="file-size-header text-end">
                                <i class="fas fa-hdd me-2"></i>Size
                            </th>
                            
                        </tr>
                    </thead>
                    <tbody id="file-table-body">
                        <!-- Files will be loaded here -->
                    </tbody>
                </table>
                
                <!-- Legacy grid container (hidden) -->
                <div id="file-grid" class="row" style="display: none;">
                    <!-- Files and folders will be populated by JavaScript -->
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
                
                <div class="preview-footer">
                    <div class="file-info">
                        <div class="info-item">
                            <small class="text-muted">Size:</small>
                            <span id="preview-size">-</span>
                        </div>
                        
                        <div class="info-item">
                            <small class="text-muted">Type:</small>
                            <span id="preview-type">-</span>
                        </div>
                    </div>
                    
                    <div class="preview-actions">
                        <button id="preview-download" class="btn btn-sm btn-primary" style="display: none;">
                            <i class="fas fa-download"></i> Download
                        </button>
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
        <li><a href="#" id="ctx-download"><i class="fas fa-download"></i> Download</a></li>
        <li><a href="#" id="ctx-print" style="display: none;"><i class="fas fa-print"></i> Print</a></li>
        <li><a href="#" id="ctx-rename"><i class="fas fa-edit"></i> Rename</a></li>
        <li class="divider"></li>
        <li><a href="#" id="ctx-delete"><i class="fas fa-trash"></i> Delete</a></li>
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
            <form id="print-form">
                <div class="modal-body">
                    <p class="text-muted mb-3">
                        
                        
                    </p>
                    
                    <div class="form-group">
                        <label for="print-roll">Roll Number <span class="text-danger">*</span></label>
                        <input type="text" id="print-roll" name="roll" class="form-control" 
                               placeholder="Enter your 7-digit roll number" value="50221" required maxlength="7">
                        <small class="form-text text-muted">Complete the 7-digit number to auto-fill your name</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="print-name">Name <span class="text-danger">*</span></label>
                        <input type="text" id="print-name" name="name" class="form-control" 
                               placeholder="Enter your full name" required>
                        <div id="user-status" class="mt-2" style="display: none;">
                            <small class="text-success">
                                <i class="fas fa-check-circle"></i> User found! Name auto-filled.
                            </small>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="print-lab">Lab Name (Optional)</label>
                        <input type="text" id="print-lab" name="lab" class="form-control" 
                               placeholder="e.g., CS Lab, Physics Lab">
                    </div>
                    
                    <div id="print-error" class="alert alert-danger" style="display: none;"></div>
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

<style>
.file-listing {
    min-height: 400px;
}

.file-item {
    cursor: pointer;
    transition: all 0.2s ease;
    margin-bottom: 15px;
}

.file-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.file-item .card {
    border: 1px solid #ddd;
    height: 120px;
}

.file-item .card-body {
    padding: 10px;
    text-align: center;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.file-item .file-icon {
    font-size: 2rem;
    margin-bottom: 5px;
}

.file-item .file-name {
    font-size: 0.85rem;
    font-weight: 500;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 100%;
}

.file-item .file-size {
    font-size: 0.75rem;
    color: #666;
}

.file-item.folder {
    color: #f39c12;
}

.file-item.file {
    color: #3498db;
}

.context-menu {
    position: fixed;
    background: white;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.2);
    z-index: 1000;
}

.context-menu ul {
    list-style: none;
    margin: 0;
    padding: 5px 0;
}

.context-menu ul li {
    margin: 0;
}

.context-menu ul li.divider {
    border-top: 1px solid #eee;
    margin: 5px 0;
}

.context-menu ul li a {
    display: block;
    padding: 8px 15px;
    color: #333;
    text-decoration: none;
}

.context-menu ul li a:hover {
    background-color: #f5f5f5;
    color: #333;
    text-decoration: none;
}

.drag-over {
    background-color: #e8f4ff !important;
    border: 2px dashed #007bff !important;
}

.toolbar {
    background: #f8f9fa;
    padding: 10px;
    border-radius: 4px;
    border: 1px solid #dee2e6;
}

.breadcrumb {
    background: #fff;
    border: 1px solid #dee2e6;
}

.breadcrumb-item a {
    color: #007bff;
    cursor: pointer;
    text-decoration: none;
}

.breadcrumb-item a:hover {
    text-decoration: underline;
}

.upload-item {
    padding: 10px;
    border-bottom: 1px solid #eee;
}

.upload-item:last-child {
    border-bottom: none;
}

.progress {
    margin-top: 5px;
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

// Add the file manager script
$this->Html->script('file-manager', ['block' => true]);
?>
