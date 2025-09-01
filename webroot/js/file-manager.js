class FileManager{constructor(){this.currentPath=window.currentPath||'';this.selectedItem=null;this.notesVisible=!1;this.currentNotes='';this.hoverTimeout=null;this.hoverHideTimeout=null;this.isProcessingMove=!1;this.init()}
encodePath(path){if(!path)return'';return path.split('/').map(segment=>encodeURIComponent(segment)).join('/')}
init(){this.bindEvents();this.loadDirectory(this.currentPath)}
bindEvents(){$('#btn-create-folder').on('click',()=>this.showCreateFolderModal());$('#btn-upload').on('click',()=>$('#file-input').click());$('#file-input').on('change',(e)=>this.handleFileUpload(e));$('#btn-create-folder-confirm').on('click',()=>this.createFolder());$('#btn-rename-confirm').on('click',()=>this.renameItem());$(document).on('contextmenu','.file-item',(e)=>this.showContextMenu(e));$(document).on('click',()=>this.hideContextMenu());$('#ctx-download').on('click',()=>this.downloadItem());$('#ctx-print').on('click',()=>this.showPrintModal());$('#ctx-rename').on('click',()=>this.showRenameModal());$('#ctx-delete').on('click',()=>this.deleteItem());$('#print-form').on('submit',(e)=>this.handlePrintSubmit(e));$(document).on('input','#print-roll',(e)=>this.handleRollNumberInput(e));$(document).on('mouseenter','.file-item',(e)=>this.handleItemHover(e));$(document).on('mouseleave','.file-item',(e)=>this.handleItemHoverLeave(e));$(document).on('click','.file-item',(e)=>this.handleItemClick(e));$(document).on('dblclick','.file-item',(e)=>this.handleItemDoubleClick(e));$(document).on('click','.close-preview',()=>this.closePreview());$(document).on('mouseenter','#preview-panel',()=>this.handlePreviewPanelEnter());$(document).on('mouseleave','#preview-panel',()=>this.handlePreviewPanelLeave());$(document).on('dragstart','.file-item',(e)=>this.handleDragStart(e));$(document).on('dragenter','.drop-target',(e)=>this.handleDragEnter(e));$(document).on('dragover','.drop-target',(e)=>this.handleDragOver(e));$(document).on('dragleave','.drop-target',(e)=>this.handleDragLeave(e));$(document).on('drop','.drop-target',(e)=>this.handleDrop(e));$(document).on('dragend','.file-item',(e)=>this.handleDragEnd(e));$(document).on('click','.breadcrumb-item a',(e)=>this.navigateToPath(e));this.setupFileDragAndDrop();$(document).on('keydown',(e)=>this.handleKeyboard(e));$('#folder-name').on('keypress',(e)=>{if(e.which===13)$('#btn-create-folder-confirm').click();});$('#rename-input').on('keypress',(e)=>{if(e.which===13)$('#btn-rename-confirm').click();});$(document).on('click','.read-more-btn',(e)=>this.toggleReadMore(e));$(document).on('click','#add-note',()=>{const notesList=$('#notes-list');const newNoteElement=this.createNoteElement({id:'new',content:'',created:new Date().toISOString()});notesList.prepend(newNoteElement);this.enterNoteEditMode(newNoteElement)});$(document).on('click','.edit-note',(e)=>{const noteElement=$(e.currentTarget).closest('.note-item');this.enterNoteEditMode(noteElement)});$(document).on('click','.delete-note',(e)=>{const noteElement=$(e.currentTarget).closest('.note-item');const noteId=noteElement.data('note-id');this.deleteNote(noteId)});$(document).on('click','.save-note',(e)=>{const noteElement=$(e.currentTarget).closest('.note-item');this.exitNoteEditMode(noteElement,!0)});$(document).on('click','.cancel-edit',(e)=>{const noteElement=$(e.currentTarget).closest('.note-item');this.exitNoteEditMode(noteElement,!1)});$(document).on('keydown','.note-editor',(e)=>{if(e.key==='Enter'&&!e.shiftKey){e.preventDefault();const noteElement=$(e.currentTarget).closest('.note-item');this.exitNoteEditMode(noteElement,!0)}})}
setupFileDragAndDrop(){$('#file-listing').on('dragenter dragover',(e)=>{if(e.originalEvent.dataTransfer.types.includes('Files')){e.preventDefault();$(e.currentTarget).addClass('drag-over')}});$('#file-listing').on('dragleave',(e)=>{if(!$(e.currentTarget).is(e.relatedTarget)&&!$(e.currentTarget).has(e.relatedTarget).length){$(e.currentTarget).removeClass('drag-over')}});$('#file-listing').on('drop',(e)=>{$(e.currentTarget).removeClass('drag-over');const files=e.originalEvent.dataTransfer.files;if(files&&files.length>0){e.preventDefault();this.uploadFiles(files)}})}
handleDragStart(e){const $item=$(e.currentTarget);this.draggedItem={path:$item.data('path'),type:$item.data('type'),name:$item.data('name')};e.originalEvent.dataTransfer.setData('text/plain',JSON.stringify(this.draggedItem));e.originalEvent.dataTransfer.effectAllowed='move';$item.addClass('dragging');this.closePreview()}
handleDragEnter(e){e.preventDefault();const $target=$(e.currentTarget);if(this.isValidDropTarget($target)){$target.addClass('drag-over-target')}}
handleDragOver(e){e.preventDefault();e.originalEvent.dataTransfer.dropEffect='move'}
handleDragLeave(e){const $target=$(e.currentTarget);if(!$target.is(e.relatedTarget)&&!$target.has(e.relatedTarget).length){$target.removeClass('drag-over-target')}}
handleDrop(e){e.preventDefault();const $target=$(e.currentTarget);$target.removeClass('drag-over-target');if(!this.draggedItem)return;if(this.isProcessingMove)return;let targetPath='';if($target.hasClass('breadcrumb-item')){targetPath=$target.find('a').data('path')||''}else if($target.is('a')&&$target.parent().hasClass('breadcrumb-item')){targetPath=$target.data('path')||''}else if($target.hasClass('file-item')){targetPath=$target.data('path')}
if(targetPath===this.draggedItem.path||this.dirname(this.draggedItem.path)===targetPath){return}
this.moveItem(this.draggedItem.path,targetPath)}
handleDragEnd(e){$(e.currentTarget).removeClass('dragging');$('.drag-over-target').removeClass('drag-over-target');this.draggedItem=null;this.isProcessingMove=!1}
isValidDropTarget($target){if(!this.draggedItem)return!1;if($target.hasClass('breadcrumb-item')||($target.is('a')&&$target.parent().hasClass('breadcrumb-item'))){return!0}
if($target.hasClass('file-item')&&$target.data('type')==='folder'){const targetPath=$target.data('path');if(this.draggedItem.type==='folder'&&(targetPath===this.draggedItem.path||targetPath.startsWith(this.draggedItem.path+'/'))){return!1}
return!0}
return!1}
moveItem(sourcePath,targetPath){if(this.isProcessingMove){return}
this.isProcessingMove=!0;$.ajax({url:'/api/move',method:'POST',data:{source_path:sourcePath,target_path:targetPath},dataType:'json',success:(response)=>{this.isProcessingMove=!1;if(response.success){this.showSuccess('Item moved successfully');this.loadDirectory(this.currentPath)}else{this.showError(response.message||'Failed to move item')}},error:(xhr)=>{this.isProcessingMove=!1;this.showError('Failed to move item: '+xhr.statusText)}})}
dirname(path){if(!path)return'';const parts=path.split('/');parts.pop();return parts.join('/')}
loadDirectory(path){this.showLoading();this.currentPath=path;const url=path?`/${this.encodePath(path)}`:'/';window.history.pushState({path:path},'',url);const ajaxUrl='/api/browse'+(path?'/'+this.encodePath(path):'');$.ajax({url:ajaxUrl,method:'GET',dataType:'json',success:(response)=>{if(response.success){this.renderDirectory(response)}else{this.showError('Failed to load directory: '+(response.message||'Unknown error'))}},error:(xhr,status,error)=>{this.showError('Failed to load directory: '+xhr.statusText);this.hideLoading()}})}
renderDirectory(response){this.hideLoading();this.updateBreadcrumbs(response.breadcrumbs);const $grid=$('#file-grid');const $tableBody=$('#file-table-body');$grid.hide();$tableBody.empty();if(response.items.length===0){$('#empty-folder').show()}else{$('#empty-folder').hide();response.items.forEach(item=>{$tableBody.append(this.createFileItemElement(item))})}
this.currentNotes=response.notes||[];if(response.notes!==undefined){this.showNotes(this.currentNotes)}else{this.hideNotes()}
this.setupFileDragAndDrop()}
createFileItemElement(item){const isFolder=item.type==='folder';const icon=isFolder?'fas fa-folder':this.getFileIcon(item.name,item.mime_type);const sizeText=isFolder?'-':this.formatSize(item.size||0);const typeText=isFolder?'Folder':this.getFileType(item.name);const element=$(`
            <tr class="file-item ${item.type}" 
                data-path="${item.path}" 
                data-type="${item.type}"
                data-name="${item.name}"
                draggable="true">
                <td class="file-name-cell">
                    <div class="d-flex align-items-center">
                        <i class="file-icon ${icon}"></i>
                        <span class="file-name" title="${item.name}">${item.name}</span>
                    </div>
                </td>
                
                <td class="file-size-cell text-end">${sizeText}</td>
            </tr>
        `);if(isFolder){element.addClass('drop-target')}
return element}
getFileIcon(filename,mimeType){const ext=filename.split('.').pop().toLowerCase();if(['jpg','jpeg','png','gif','bmp','svg','webp'].includes(ext)){return'fas fa-file-image'}
if(['mp4','avi','mkv','mov','wmv','flv','webm'].includes(ext)){return'fas fa-file-video'}
if(['mp3','wav','flac','aac','ogg','wma'].includes(ext)){return'fas fa-file-audio'}
if(['pdf'].includes(ext)){return'fas fa-file-pdf'}
if(['doc','docx'].includes(ext)){return'fas fa-file-word'}
if(['xls','xlsx'].includes(ext)){return'fas fa-file-excel'}
if(['ppt','pptx'].includes(ext)){return'fas fa-file-powerpoint'}
if(['zip','rar','7z','tar','gz'].includes(ext)){return'fas fa-file-archive'}
if(['js','html','css','php','py','java','cpp','c','cs','rb','go'].includes(ext)){return'fas fa-file-code'}
if(['txt','md','json','xml','yml','yaml'].includes(ext)){return'fas fa-file-alt'}
return'fas fa-file'}
formatSize(bytes){if(!bytes)return'0 B';const units=['B','KB','MB','GB','TB'];const power=Math.floor(Math.log(bytes)/Math.log(1024));return Math.round(bytes/Math.pow(1024,power)*100)/100+' '+units[power]}
updateBreadcrumbs(breadcrumbs){const $breadcrumb=$('#breadcrumb');$breadcrumb.empty();breadcrumbs.forEach((item,index)=>{const isLast=index===breadcrumbs.length-1;const $item=$(`
                <li class="breadcrumb-item drop-target ${isLast ? 'active' : ''}" data-path="${item.path}">
                    ${isLast ? item.name : `<a data-path="${item.path}">${item.name}</a>`}
                </li>
            `);$breadcrumb.append($item)})}
handleItemHover(e){const $item=$(e.currentTarget);const path=$item.data('path');const type=$item.data('type');if(this.draggedItem){return}
if(type==='file'){if(this.hoverTimeout){clearTimeout(this.hoverTimeout)}
this.hoverTimeout=setTimeout(()=>{this.showPreview(path)},2000)}}
handleItemHoverLeave(e){if(this.hoverTimeout){clearTimeout(this.hoverTimeout);this.hoverTimeout=null}
if(this.draggedItem){return}
const $relatedTarget=$(e.relatedTarget);const isMovingToPreview=$relatedTarget.closest('#preview-panel').length>0;if(!isMovingToPreview){if(this.hoverHideTimeout){clearTimeout(this.hoverHideTimeout)}
this.hoverHideTimeout=setTimeout(()=>{this.hidePreview()},300)}}
handlePreviewPanelEnter(){if(this.hoverHideTimeout){clearTimeout(this.hoverHideTimeout);this.hoverHideTimeout=null}}
handlePreviewPanelLeave(){if(this.draggedItem){return}
if(this.hoverHideTimeout){clearTimeout(this.hoverHideTimeout)}
this.hoverHideTimeout=setTimeout(()=>{this.hidePreview()},300)}
handleItemClick(e){
    e.preventDefault();
    const $item=$(e.currentTarget);
    $('.file-item').removeClass('selected');
    $item.addClass('selected');
    this.selectedItem={
        path:$item.data('path'),
        type:$item.data('type'),
        name:$item.find('.file-name').text()
    };
    
    if(this.selectedItem.type==='file'){
        // Determine the preview URL based on file type
        let previewUrl;
        if(this.isOfficeFile(this.selectedItem)){
            // Use Google Docs Viewer for Office files
            const baseUrl=window.location.origin;
            const apiUrl=baseUrl+'/api/preview/'+this.encodePath(this.selectedItem.path);
            previewUrl='https://docs.google.com/gview?url='+encodeURIComponent(apiUrl)+'&embedded=true';
        }else{
            // Use direct API preview for other files
            previewUrl='/api/preview/'+this.encodePath(this.selectedItem.path);
        }
        window.open(previewUrl,'_blank');
    }else if(this.selectedItem.type==='folder'){
        this.loadDirectory(this.selectedItem.path);
        this.closePreview();
    }else{
        this.closePreview();
    }
}
handleItemDoubleClick(e){
    e.preventDefault();
    const $item=$(e.currentTarget);
    const type=$item.data('type');
    const path=$item.data('path');
    
    if(type==='folder'){
        this.loadDirectory(path);
    }else{
        // Determine the preview URL based on file type
        let previewUrl;
        const fileItem={
            path:path,
            type:type,
            name:$item.find('.file-name').text()
        };
        
        if(this.isOfficeFile(fileItem)){
            // Use Google Docs Viewer for Office files
            const baseUrl=window.location.origin;
            const apiUrl=baseUrl+'/api/preview/'+this.encodePath(path);
            previewUrl='https://docs.google.com/gview?url='+encodeURIComponent(apiUrl)+'&embedded=true';
        }else{
            // Use direct API preview for other files
            previewUrl='/api/preview/'+this.encodePath(path);
        }
        window.open(previewUrl,'_blank');
    }
}
navigateToPath(e){e.preventDefault();const path=$(e.currentTarget).data('path');this.loadDirectory(path)}
showCreateFolderModal(){$('#folder-name').val('');$('#folder-name-error').hide();$('#createFolderModal').modal('show');setTimeout(()=>$('#folder-name').focus(),500)}
createFolder(){const name=$('#folder-name').val().trim();if(!name){this.showModalError('#folder-name-error','Folder name is required');return}
$.ajax({url:'/api/create-folder',method:'POST',data:{parent_path:this.currentPath,name:name},dataType:'json',success:(response)=>{if(response.success){$('#createFolderModal').modal('hide');this.showSuccess('Folder created successfully');this.loadDirectory(this.currentPath)}else{this.showModalError('#folder-name-error',response.message)}},error:(xhr)=>{this.showModalError('#folder-name-error','Failed to create folder: '+xhr.statusText)}})}
handleFileUpload(e){const files=e.target.files;if(files.length>0){this.uploadFiles(files)}
$(e.target).val('')}
showUploadProgress(){$('#upload-progress').show();$('.progress-bar').css('width','0%');$('.upload-percentage').text('0%')}
updateUploadProgress(percentage){const roundedPercentage=Math.round(percentage);$('.progress-bar').css('width',roundedPercentage+'%');$('.upload-percentage').text(roundedPercentage+'%')}
uploadFiles(files){let formData=new FormData();formData.append('parent_path',this.currentPath);for(let i=0;i<files.length;i++){formData.append('files[]',files[i])}
this.showUploadProgress();$.ajax({url:'/api/upload',method:'POST',data:formData,processData:!1,contentType:!1,dataType:'json',xhr:()=>{const xhr=new window.XMLHttpRequest();xhr.upload.addEventListener("progress",(e)=>{if(e.lengthComputable){const percentComplete=(e.loaded/e.total)*100;this.updateUploadProgress(percentComplete)}},!1);return xhr},success:(response)=>{if(response.success){this.showSuccess('Files uploaded successfully');this.loadDirectory(this.currentPath)}else{this.showError(response.message||'Upload failed')}
$('#upload-progress').fadeOut()},error:(xhr)=>{this.showError('Upload failed: '+xhr.statusText);$('#upload-progress').fadeOut()}})}
showContextMenu(e){e.preventDefault();const $item=$(e.currentTarget);this.selectedItem={path:$item.data('path'),type:$item.data('type'),name:$item.find('.file-name').text()};$('#ctx-download').toggle(this.selectedItem.type==='file');const isPdf=this.selectedItem.type==='file'&&this.selectedItem.name.toLowerCase().endsWith('.pdf');$('#ctx-print').toggle(isPdf);const $menu=$('#context-menu');$menu.css({display:'block',left:e.pageX,top:e.pageY});$('.file-item').removeClass('selected');$item.addClass('selected')}
hideContextMenu(){$('#context-menu').hide()}
downloadItem(){if(this.selectedItem&&this.selectedItem.type==='file'){this.downloadFile(this.selectedItem.path)}
this.hideContextMenu()}
downloadFile(path){
    // Always download using direct API URL for context menu download button
    window.location.href = `/api/download/${this.encodePath(path)}`;
}
showRenameModal(){if(!this.selectedItem)return;const currentName=this.selectedItem.name;let baseName=currentName;let extension='';if(this.selectedItem.type==='file'){const lastDot=currentName.lastIndexOf('.');if(lastDot!==-1&&lastDot!==0){baseName=currentName.substring(0,lastDot);extension=currentName.substring(lastDot)}}
$('#rename-input').val(baseName);$('#rename-error').hide();if(this.selectedItem.type==='file'&&extension){$('#rename-extension-info').html(`
                <small class="text-muted">
                    <i class="fas fa-info-circle"></i> 
                    File extension <code>${extension}</code> will be preserved for security.
                </small>
            `).show()}else{$('#rename-extension-info').hide()}
$('#renameModal').modal('show');setTimeout(()=>$('#rename-input').select(),500);this.hideContextMenu()}
renameItem(){if(!this.selectedItem)return;let newName=$('#rename-input').val().trim();if(!newName){this.showModalError('#rename-error','Name is required');return}
if(this.selectedItem.type==='file'){const currentName=this.selectedItem.name;const lastDot=currentName.lastIndexOf('.');if(lastDot!==-1&&lastDot!==0){const extension=currentName.substring(lastDot);newName=newName.replace(/\.[^.]*$/,'');newName=newName+extension}}
if(newName===this.selectedItem.name){$('#renameModal').modal('hide');return}
if(!this.isValidFileName(newName)){this.showModalError('#rename-error','Invalid filename. Avoid special characters.');return}
$.ajax({url:'/api/rename',method:'POST',data:{old_path:this.selectedItem.path,new_name:newName},dataType:'json',success:(response)=>{if(response.success){$('#renameModal').modal('hide');this.showSuccess('Item renamed successfully');this.loadDirectory(this.currentPath)}else{this.showModalError('#rename-error',response.message)}},error:(xhr)=>{this.showModalError('#rename-error','Failed to rename: '+xhr.statusText)}})}
deleteItem(){if(!this.selectedItem)return;let message=`Are you sure you want to delete "${this.selectedItem.name}"?`;if(this.selectedItem.type==='folder'){message+='\n\nThis will delete all contents of the folder.'}
if(!confirm(message)){this.hideContextMenu();return}
$.ajax({url:'/api/delete',method:'POST',data:{path:this.selectedItem.path},dataType:'json',success:(response)=>{if(response.success){this.showSuccess('Item deleted successfully');this.loadDirectory(this.currentPath)}else{this.showError(response.message)}},error:(xhr)=>{this.showError('Failed to delete: '+xhr.statusText)}});this.hideContextMenu()}
handleKeyboard(e){if(e.keyCode===46&&this.selectedItem){this.deleteItem()}
if(e.keyCode===113&&this.selectedItem){this.showRenameModal()}
if(e.keyCode===116){e.preventDefault();this.loadDirectory(this.currentPath)}
if(e.keyCode===27){this.hideContextMenu();$('.file-item').removeClass('selected');this.selectedItem=null}}
showLoading(){$('#loading').show();$('#file-grid, #empty-folder').hide();$('#file-table-body').empty()}
hideLoading(){$('#loading').hide()}
showSuccess(message){this.showNotification(message,'success')}
showError(message){this.showNotification(message,'danger')}
showWarning(message){this.showNotification(message,'warning')}
showNotification(message,type){const $alert=$(`
            <div class="alert alert-${type} alert-dismissible fade show notification" role="alert">
                ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        `);$('body').prepend($alert);setTimeout(()=>{$alert.alert('close')},5000)}
showModalError(selector,message){$(selector).text(message).show()}
showPrintModal(){
    if(!this.selectedItem||this.selectedItem.type!=='file')return;
    $('#print-form')[0].reset();
    $('#print-error').hide();
    $('#user-status').hide();
    $('#print-roll').val('50221');
    $('.modal-title').html(`
        <i class="fas fa-print"></i> Print: ${this.selectedItem.name}
    `);
    $('#printModal').modal('show');
    setTimeout(()=>$('#print-roll').focus(),500);
    this.hideContextMenu()
}

showPrintModalFromPreview(filePath, fileName){
    // Set the selected item for printing
    this.selectedItem = {
        path: filePath,
        name: fileName,
        type: 'file'
    };
    
    // Show the print modal
    this.showPrintModal();
}
handlePrintSubmit(e){e.preventDefault();const formData=new FormData();formData.append('file',this.selectedItem.path);formData.append('name',$('#print-name').val().trim());formData.append('roll',$('#print-roll').val().trim());formData.append('lab',$('#print-lab').val().trim());const name=$('#print-name').val().trim();const roll=$('#print-roll').val().trim();if(!name||!roll){this.showPrintError('Name and Roll Number are required.');return}
$('#btn-print-confirm').prop('disabled',!0).html('<i class="fas fa-spinner fa-spin"></i> Processing...');this.showPrintError('');const tryBlobApproach=()=>{$.ajax({url:'/api/print',method:'POST',data:formData,processData:!1,contentType:!1,xhrFields:{responseType:'blob'},success:(data,status,xhr)=>{console.log('Print response received:',{status:status,contentType:xhr.getResponseHeader('Content-Type'),contentLength:xhr.getResponseHeader('Content-Length'),dataSize:data.size,dataType:data.type});if(!data||data.size===0){this.showPrintError('Received empty response from server');return}
try{const blob=new Blob([data],{type:'application/pdf'});const url=window.URL.createObjectURL(blob);const newWindow=window.open(url,'_blank');if(newWindow){$('#printModal').modal('hide');this.showSuccess('PDF prepared for printing!');setTimeout(()=>{window.URL.revokeObjectURL(url)},5000)}else{this.showPrintError('Popup blocked! Please allow popups for this site and try again.');window.URL.revokeObjectURL(url)}}catch(error){console.error('Error creating blob:',error);this.showPrintError('Failed to create PDF: '+error.message)}},error:(xhr,status,error)=>{console.error('Print error:',{xhr,status,error});console.log('Blob approach failed, trying fallback...');tryFallbackApproach()},complete:()=>{$('#btn-print-confirm').prop('disabled',!1).html('<i class="fas fa-print"></i> Print PDF')}})};const tryFallbackApproach=()=>{console.log('Trying fallback approach...');const tempForm=document.createElement('form');tempForm.method='POST';tempForm.action='/api/print';tempForm.target='_blank';tempForm.style.display='none';const fileInput=document.createElement('input');fileInput.type='hidden';fileInput.name='file';fileInput.value=this.selectedItem.path;tempForm.appendChild(fileInput);const nameInput=document.createElement('input');nameInput.type='hidden';nameInput.name='name';nameInput.value=$('#print-name').val().trim();tempForm.appendChild(nameInput);const rollInput=document.createElement('input');rollInput.type='hidden';rollInput.name='roll';rollInput.value=$('#print-roll').val().trim();tempForm.appendChild(rollInput);const labInput=document.createElement('input');labInput.type='hidden';labInput.name='lab';labInput.value=$('#print-lab').val().trim();tempForm.appendChild(labInput);document.body.appendChild(tempForm);tempForm.submit();document.body.removeChild(tempForm);$('#printModal').modal('hide');this.showSuccess('PDF prepared for printing!')};if(window.Blob&&window.URL&&window.URL.createObjectURL){console.log('Blob support detected, trying blob approach...');tryBlobApproach()}else{console.log('Blob not supported, using fallback approach...');tryFallbackApproach()}}
showPrintError(message){$('#print-error').text(message).show()}
handleRollNumberInput(e){const rollNumber=$(e.target).val().trim();$('#user-status').hide();$('#print-error').hide();if(rollNumber.length===7&&/^\d{7}$/.test(rollNumber)){this.lookupUser(rollNumber)}else if(rollNumber.length>7){$(e.target).val(rollNumber.substring(0,7))}}
lookupUser(rollNumber){$.ajax({url:'/api/lookup-user',method:'POST',data:{roll_number:rollNumber},dataType:'json',success:(response)=>{if(response.success&&response.user_found){$('#print-name').val(response.user.name);$('#user-status').show().find('small').html('<i class="fas fa-check-circle"></i> User found! Name auto-filled.').removeClass('text-warning text-danger').addClass('text-success')}else if(response.success&&!response.user_found){$('#print-name').val('').focus();$('#user-status').show().find('small').html('<i class="fas fa-info-circle"></i> New user - please enter your name.').removeClass('text-success text-danger').addClass('text-warning')}},error:(xhr)=>{$('#user-status').show().find('small').html('<i class="fas fa-exclamation-triangle"></i> Error looking up user.').removeClass('text-success text-warning').addClass('text-danger');$('#print-name').focus()}})}
isValidFileName(filename){const invalidChars=/[<>:"/\\|?*\x00-\x1f]/;return!invalidChars.test(filename)&&filename.length>0&&filename.length<=255}
isDangerousFile(filename){const dangerousExtensions=['exe','bat','cmd','com','pif','scr','vbs','vbe','js','jse','jar','msi','msp','hta','cpl','msc','wsf','wsh','ps1','ps2','psc1','psc2','php','asp','aspx','jsp','pl','py','rb','sh','cgi','htaccess','htpasswd','ini','cfg','conf','7z','tar','gz','bz2','xz','sql','db','sqlite','mdb'];const extension=filename.toLowerCase().split('.').pop();return dangerousExtensions.includes(extension)}
getSafeFileTypes(){return['pdf','doc','docx','xls','xlsx','ppt','pptx','odt','ods','odp','jpg','jpeg','png','gif','bmp','tiff','svg','webp','txt','rtf','csv','xml','json','mp3','wav','ogg','flac','aac','m4a','mp4','avi','mkv','mov','wmv','flv','webm','log','md','readme']}
getFileType(filename){const extension=filename.toLowerCase().split('.').pop();const typeMap={'pdf':'PDF Document','doc':'Word Document','docx':'Word Document','xls':'Excel Spreadsheet','xlsx':'Excel Spreadsheet','ppt':'PowerPoint','pptx':'PowerPoint','odt':'OpenDocument Text','ods':'OpenDocument Spreadsheet','odp':'OpenDocument Presentation','jpg':'JPEG Image','jpeg':'JPEG Image','png':'PNG Image','gif':'GIF Image','bmp':'Bitmap Image','svg':'SVG Image','webp':'WebP Image','tiff':'TIFF Image','txt':'Text File','rtf':'Rich Text','csv':'CSV File','xml':'XML File','json':'JSON File','log':'Log File','md':'Markdown','mp3':'MP3 Audio','wav':'WAV Audio','ogg':'OGG Audio','flac':'FLAC Audio','aac':'AAC Audio','m4a':'M4A Audio','mp4':'MP4 Video','avi':'AVI Video','mkv':'MKV Video','mov':'QuickTime Video','wmv':'WMV Video','flv':'Flash Video','webm':'WebM Video'};return typeMap[extension]||(extension?extension.toUpperCase()+' File':'File')}
showPreview(filePath){if(this.draggedItem){return}
$('#main-panel').removeClass('col-md-12').addClass('col-md-8');$('#preview-panel').show().removeClass('hidden');this.showPreviewLoading();$.ajax({url:'/api/file-info',method:'GET',data:{path:filePath},dataType:'json',success:(response)=>{if(response.success){this.renderPreview(response.file)}else{this.showPreviewError('Failed to load file information: '+(response.message||'Unknown error'))}},error:(xhr,status,error)=>{let errorMessage='Failed to load file: '+xhr.statusText;try{const errorResponse=JSON.parse(xhr.responseText);if(errorResponse.message){errorMessage=errorResponse.message}}catch(e){}
this.showPreviewError(errorMessage)}})}
closePreview(){$('#preview-panel').addClass('hidden');setTimeout(()=>{$('#preview-panel').hide();$('#main-panel').removeClass('col-md-8').addClass('col-md-12')},300);this.currentPreviewFile=null}
hidePreview(){$('#preview-panel').addClass('hidden');setTimeout(()=>{$('#preview-panel').hide();$('#main-panel').removeClass('col-md-8').addClass('col-md-12')},300);this.currentPreviewFile=null}
showPreviewLoading(){$('#preview-loading').show();$('#preview-body').hide();$('.preview-filename').text('Loading...')}
hidePreviewLoading(){$('#preview-loading').hide();$('#preview-body').show()}
showPreviewError(message){this.hidePreviewLoading();$('#preview-body').html(`
            <div class="preview-error text-center py-5">
                <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                <p class="text-muted">${message}</p>
            </div>
        `)}
renderPreview(file){
    this.currentPreviewFile=file;
    const icon=this.getFileIcon(file.name,file.mime_type);
    
    // Determine the preview URL based on file type
    let previewUrl;
    if(this.isOfficeFile(file)){
        // Use Google Docs Viewer for Office files
        const baseUrl=window.location.origin;
        const apiUrl=baseUrl+'/api/preview/'+this.encodePath(file.path);
        previewUrl='https://docs.google.com/gview?url='+encodeURIComponent(apiUrl)+'&embedded=true';
    }else{
        // Use direct API preview for other files
        previewUrl='/api/preview/'+this.encodePath(file.path);
    }
    
    $('.preview-icon').attr('class',icon+' preview-icon');
    $('.preview-filename').html(`
        <button class="btn btn-sm btn-outline-primary me-2" onclick="window.open('${previewUrl}', '_blank')">
            <i class="fas fa-external-link-alt"></i> Open in new tab
        </button>
        <button class="btn btn-sm btn-outline-success" onclick="fileManager.showPrintModalFromPreview('${file.path}', '${file.name}')">
            <i class="fas fa-print"></i> Print
        </button>
    `);
    
    this.hidePreviewLoading();
    console.log('renderPreview - file.is_previewable:', file.is_previewable);
    console.log('renderPreview - this.isOfficeFile(file):', this.isOfficeFile(file));
    
    // Check if file is previewable OR if it's an Office file (which we can preview with Google Docs Viewer)
    if(file.is_previewable || this.isOfficeFile(file)){
        console.log('Calling loadPreviewContent for:', file.name);
        this.loadPreviewContent(file)
    }else{
        console.log('Calling showNonPreviewableFile for:', file.name);
        this.showNonPreviewableFile(file)
    }
}
loadPreviewContent(file){
    console.log('loadPreviewContent called with file:', file);
    console.log('isOfficeFile result:', this.isOfficeFile(file));
    
    const previewUrl='/api/preview/'+this.encodePath(file.path);
    
    if(this.isOfficeFile(file)){
        console.log('Showing Office preview for:', file.name);
        this.showOfficePreview(file);
    }else if(file.mime_type&&file.mime_type.startsWith('image/')){
        this.showImagePreview(previewUrl,file);
    }else if(file.mime_type==='application/pdf'){
        this.showPdfPreview(previewUrl,file);
    }else if(this.isTextFile(file)){
        this.showTextPreview(previewUrl,file);
    }else{
        console.log('Showing non-previewable file for:', file.name);
        this.showNonPreviewableFile(file);
    }
}

showOfficePreview(file){
    const baseUrl=window.location.origin;
    const apiUrl=baseUrl+'/api/preview/'+this.encodePath(file.path);
    const googleDocsUrl='https://docs.google.com/gview?url='+encodeURIComponent(apiUrl)+'&embedded=true';
    
    $('#preview-body').html(`
        <div class="office-preview">
            <iframe src="${googleDocsUrl}" class="preview-iframe" frameborder="0"></iframe>
        </div>
    `);
}
showImagePreview(previewUrl,file){$('#preview-body').html(`
            <div class="image-preview">
                <img src="${previewUrl}" alt="${file.name}" class="preview-image" />
            </div>
        `)}
showPdfPreview(previewUrl,file){$('#preview-body').html(`
            <div class="pdf-preview">
                <iframe src="${previewUrl}" class="preview-iframe" frameborder="0"></iframe>
            </div>
        `)}
showTextPreview(previewUrl,file){$.ajax({url:previewUrl,method:'GET',dataType:'text',success:(content)=>{let displayContent=content;if(content.length>10000){displayContent=content.substring(0,10000)+'\n\n... (content truncated, download to see full file)'}
$('#preview-body').html(`
                    <div class="text-preview">
                        <pre class="preview-code"><code>${this.escapeHtml(displayContent)}</code></pre>
                    </div>
                `)},error:()=>{this.showPreviewError('Failed to load text content')}})}
showNonPreviewableFile(file){$('#preview-body').html(`
            <div class="non-previewable text-center py-5">
                <i class="${this.getFileIcon(file.name, file.mime_type)} fa-4x text-muted mb-3"></i>
                <h5>${file.name}</h5>
                <p class="text-muted">This file type cannot be previewed</p>
            </div>
        `)}
isTextFile(file){if(!file.mime_type)return!1;return file.mime_type.startsWith('text/')||['application/json','application/xml','application/javascript'].includes(file.mime_type)||['txt','md','json','xml','html','htm','css','js','php','py','java','c','cpp','sql'].includes(file.extension)}

isOfficeFile(file){
    console.log('isOfficeFile called with:', file);
    console.log('file.mime_type:', file.mime_type);
    console.log('file.name:', file.name);
    console.log('file.extension:', file.extension);
    
    // Extract extension from filename if not provided
    const extension = file.extension || (file.name ? file.name.toLowerCase().split('.').pop() : '');
    console.log('Extracted extension:', extension);
    
    // Check by extension first (this works even without MIME type)
    const officeExtensions = ['docx','xlsx','pptx','doc','xls','ppt','odt','ods','odp','csv','rtf'];
    const extensionMatch = officeExtensions.includes(extension);
    
    // Check by MIME type if available
    let mimeTypeMatch = false;
    if(file.mime_type){
        const officeMimeTypes=[
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // .docx
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // .xlsx
            'application/vnd.openxmlformats-officedocument.presentationml.presentation', // .pptx
            'application/msword', // .doc
            'application/vnd.ms-excel', // .xls
            'application/vnd.ms-powerpoint', // .ppt
            'application/vnd.oasis.opendocument.text', // .odt
            'application/vnd.oasis.opendocument.spreadsheet', // .ods
            'application/vnd.oasis.opendocument.presentation', // .odp
            'text/csv', // .csv
            'application/rtf' // .rtf
        ];
        mimeTypeMatch = officeMimeTypes.includes(file.mime_type);
    }
    
    console.log('MIME type match:', mimeTypeMatch);
    console.log('Extension match:', extensionMatch);
    
    return mimeTypeMatch || extensionMatch;
}
escapeHtml(text){const div=document.createElement('div');div.textContent=text;return div.innerHTML}
showNotes(notes){const notesSection=$('#notes-section');const notesList=notesSection.find('#notes-list');notesSection.show();notesList.empty();if(Array.isArray(notes)&&notes.length>0){notes.forEach(note=>{notesList.append(this.createNoteElement(note))})}else{notesList.html('<p class="text-muted text-center">No notes yet. Click "Add Note" to create one.</p>')}}
createNoteElement(note){const date=new Date(note.modified||note.created);const formattedDate=date.toLocaleString();const contentLength=note.content.length;const needsReadMore=contentLength>100;return $(`
            <div class="note-item" data-note-id="${note.id}">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="note-header">
                        <div class="note-timestamp">
                            <i class="fas fa-clock"></i> ${formattedDate}
                        </div>
                    </div>
                    <div class="note-actions">
                        <button class="btn btn-sm btn-outline-primary edit-note">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger delete-note">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <div class="note-content ${needsReadMore ? 'collapsed' : ''}">${note.content}</div>
                ${needsReadMore ? '<button class="read-more-btn" data-state="collapsed">Read More</button>' : ''}
                <textarea class="note-editor" style="display: none;">${note.content}</textarea>
                <div class="edit-actions" style="display: none;">
                    <button class="btn btn-sm btn-primary save-note">Save</button>
                    <button class="btn btn-sm btn-secondary cancel-edit">Cancel</button>
                </div>
            </div>
        `)}
hideNotes(){$('#notes-section').hide()}
enterNoteEditMode(noteElement){noteElement.find('.note-content, .note-actions').hide();noteElement.find('.note-editor, .edit-actions').show();noteElement.find('.note-editor').focus()}
exitNoteEditMode(noteElement,save=!1){const noteId=noteElement.data('note-id');const editor=noteElement.find('.note-editor');const content=editor.val().trim();if(save){const action=(noteId==='new')?'add':'edit';this.saveNote({noteId:noteId,content:content,action:action})}else{noteElement.find('.note-editor, .edit-actions').hide();noteElement.find('.note-content, .note-actions').show()}}
saveNote(data){if(!this.currentPath&&this.currentPath!==''){this.showError('Current path is not set. Please refresh the page.');return}
$.ajax({url:'/api/save-notes',method:'POST',data:{path:this.currentPath,noteId:data.noteId,content:data.content,action:data.action},success:(response)=>{if(response.success){this.showNotes(response.notes);this.showSuccess('Note saved successfully')}else{this.showError('Failed to save note: '+response.message)}},error:(xhr,status,error)=>{this.showError('Error saving note')}})}
deleteNote(noteId){if(!confirm('Are you sure you want to delete this note?')){return}
$.ajax({url:'/api/save-notes',method:'POST',data:{path:this.currentPath,noteId:noteId,action:'delete'},success:(response)=>{if(response.success){this.showNotes(response.notes);this.showSuccess('Note deleted successfully')}else{this.showError('Failed to delete note: '+response.message)}},error:()=>{this.showError('Error deleting note')}})}
toggleReadMore(event){const button=$(event.currentTarget);const noteContent=button.siblings('.note-content');const currentState=button.attr('data-state');if(currentState==='collapsed'){noteContent.removeClass('collapsed');button.attr('data-state','expanded');button.text('Show Less')}else{noteContent.addClass('collapsed');button.attr('data-state','collapsed');button.text('Read More')}}}
$(document).ready(function(){try{window.fileManager=new FileManager();window.addEventListener('popstate',function(e){const path=e.state?.path||'';window.fileManager.currentPath=path;window.fileManager.loadDirectory(path)})}catch(error){}})