/*
   Tenant System JavaScript
   Property Rental Management System
*/

// ========================================
// DOCUMENT READY
// ========================================

document.addEventListener('DOMContentLoaded', function() {
    
    // Initialize all components
    initSidebar();
    initFormValidation();
    initFileUploadPreview();
    initPaymentForm();
    initMaintenanceForm();
    initNotificationDismiss();
    
});

// ========================================
// SIDEBAR TOGGLE
// ========================================

function initSidebar() {
    const toggleBtn = document.getElementById('toggleSidebar');
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
            
            // Save state to localStorage
            const isCollapsed = sidebar.classList.contains('collapsed');
            localStorage.setItem('sidebarCollapsed', isCollapsed);
        });
        
        // Load saved state
        const savedState = localStorage.getItem('sidebarCollapsed');
        if (savedState === 'true') {
            sidebar.classList.add('collapsed');
            mainContent.classList.add('expanded');
        }
    }
    
    // Mobile sidebar - close when clicking outside
    if (window.innerWidth <= 992) {
        document.addEventListener('click', function(e) {
            if (sidebar && !sidebar.contains(e.target) && !toggleBtn?.contains(e.target)) {
                sidebar.classList.remove('show');
            }
        });
    }
}

// ========================================
// FORM VALIDATION
// ========================================

function initFormValidation() {
    // Payment form validation
    const paymentForm = document.querySelector('form[action="submit-payment.php"]');
    if (paymentForm) {
        paymentForm.addEventListener('submit', function(e) {
            const paymentAmount = document.querySelector('input[name="payment_amount"]');
            const receiptFile = document.querySelector('input[name="receipt_file"]');
            let isValid = true;
            let errorMessage = '';
            
            if (paymentAmount && parseFloat(paymentAmount.value) <= 0) {
                errorMessage = 'Please enter a valid payment amount.';
                isValid = false;
            }
            
            if (receiptFile && receiptFile.files.length === 0) {
                errorMessage = 'Please upload your payment receipt.';
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
                showAlert(errorMessage, 'danger');
            }
        });
    }
    
    // Maintenance form validation
    const maintenanceForm = document.querySelector('form[action="maintenance-request.php"]');
    if (maintenanceForm) {
        maintenanceForm.addEventListener('submit', function(e) {
            const issueTitle = document.querySelector('input[name="issue_title"]');
            const issueDesc = document.querySelector('textarea[name="issue_description"]');
            let isValid = true;
            let errorMessage = '';
            
            if (issueTitle && issueTitle.value.trim().length < 3) {
                errorMessage = 'Please provide a clear issue title (minimum 3 characters).';
                isValid = false;
            }
            
            if (issueDesc && issueDesc.value.trim().length < 10) {
                errorMessage = 'Please provide a detailed description of the issue.';
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
                showAlert(errorMessage, 'danger');
            }
        });
    }
    
    // Profile form validation
    const profileForm = document.querySelector('form[action="profile.php"]');
    if (profileForm) {
        const phoneInput = document.querySelector('input[name="phone_number"]');
        if (phoneInput) {
            phoneInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9+]/g, '');
            });
        }
    }
    
    // Password change validation
    const passwordForm = document.querySelector('form[action="profile.php"] input[name="change_password"]')?.closest('form');
    if (passwordForm) {
        passwordForm.addEventListener('submit', function(e) {
            const newPassword = document.querySelector('input[name="new_password"]');
            const confirmPassword = document.querySelector('input[name="confirm_password"]');
            
            if (newPassword && confirmPassword) {
                if (newPassword.value.length < 8) {
                    e.preventDefault();
                    showAlert('Password must be at least 8 characters long.', 'danger');
                } else if (newPassword.value !== confirmPassword.value) {
                    e.preventDefault();
                    showAlert('New passwords do not match!', 'danger');
                }
            }
        });
    }
}

// ========================================
// FILE UPLOAD PREVIEW
// ========================================

function initFileUploadPreview() {
    // Payment receipt preview
    const receiptInput = document.querySelector('input[name="receipt_file"]');
    if (receiptInput) {
        receiptInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
                const maxSize = 5 * 1024 * 1024; // 5MB
                
                if (!allowedTypes.includes(file.type)) {
                    showAlert('Invalid file type. Please upload JPG, PNG, or PDF.', 'danger');
                    this.value = '';
                    return;
                }
                
                if (file.size > maxSize) {
                    showAlert('File size too large. Maximum size is 5MB.', 'danger');
                    this.value = '';
                    return;
                }
                
                // Show preview for images
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        let previewDiv = document.getElementById('receiptPreview');
                        if (!previewDiv) {
                            previewDiv = document.createElement('div');
                            previewDiv.id = 'receiptPreview';
                            previewDiv.className = 'mt-3';
                            receiptInput.parentNode.appendChild(previewDiv);
                        }
                        previewDiv.innerHTML = `
                            <div class="alert alert-info">
                                <i class="fas fa-check-circle"></i> File selected: ${file.name}
                                <br>
                                <img src="${e.target.result}" style="max-width: 100%; max-height: 200px; margin-top: 10px; border-radius: 8px;">
                            </div>
                        `;
                    };
                    reader.readAsDataURL(file);
                } else {
                    let previewDiv = document.getElementById('receiptPreview');
                    if (previewDiv) {
                        previewDiv.innerHTML = `
                            <div class="alert alert-info mt-3">
                                <i class="fas fa-file-pdf"></i> File selected: ${file.name}
                            </div>
                        `;
                    }
                }
            }
        });
    }
    
    // Maintenance image preview
    const imageInput = document.querySelector('input[name="issue_image"]');
    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    let previewDiv = document.getElementById('imagePreview');
                    if (!previewDiv) {
                        previewDiv = document.createElement('div');
                        previewDiv.id = 'imagePreview';
                        previewDiv.className = 'mt-3';
                        imageInput.parentNode.appendChild(previewDiv);
                    }
                    previewDiv.innerHTML = `
                        <div class="alert alert-info">
                            <img src="${e.target.result}" style="max-width: 100%; max-height: 150px; border-radius: 8px;">
                        </div>
                    `;
                };
                reader.readAsDataURL(file);
            }
        });
    }
    
    // Profile image preview
    const profileInput = document.querySelector('input[name="profile_image"]');
    if (profileInput) {
        profileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const avatarImg = document.querySelector('.avatar');
                    if (avatarImg) {
                        avatarImg.src = e.target.result;
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }
}

// ========================================
// PAYMENT FORM HELPER
// ========================================

function initPaymentForm() {
    const paymentMonthSelect = document.querySelector('select[name="payment_month"]');
    const paymentAmountInput = document.querySelector('input[name="payment_amount"]');
    
    if (paymentMonthSelect && paymentAmountInput) {
        // Auto-calculate amount based on selected month
        paymentMonthSelect.addEventListener('change', function() {
            // You can add logic here if rent changes by month
            console.log('Month selected:', this.value);
        });
    }
}

// ========================================
// MAINTENANCE FORM HELPER
// ========================================

function initMaintenanceForm() {
    const prioritySelect = document.querySelector('select[name="priority_level"]');
    
    if (prioritySelect) {
        prioritySelect.addEventListener('change', function() {
            const priority = this.value;
            const helpText = document.querySelector('.priority-help');
            
            if (!helpText) {
                const hint = document.createElement('small');
                hint.className = 'priority-help text-muted d-block mt-1';
                prioritySelect.parentNode.appendChild(hint);
            }
            
            const hintDiv = document.querySelector('.priority-help');
            if (hintDiv) {
                if (priority === 'high') {
                    hintDiv.innerHTML = '<i class="fas fa-exclamation-triangle text-danger"></i> High priority - We will respond within 24 hours.';
                    hintDiv.style.color = '#ef476f';
                } else if (priority === 'medium') {
                    hintDiv.innerHTML = '<i class="fas fa-clock text-warning"></i> Medium priority - We will respond within 48 hours.';
                    hintDiv.style.color = '#ffd166';
                } else {
                    hintDiv.innerHTML = '<i class="fas fa-info-circle text-info"></i> Low priority - We will respond within 3-5 days.';
                    hintDiv.style.color = '#118ab2';
                }
            }
        });
    }
}

// ========================================
// ALERT FUNCTIONS
// ========================================

function showAlert(message, type = 'info') {
    // Check if alert container exists
    let alertContainer = document.getElementById('alertContainer');
    if (!alertContainer) {
        alertContainer = document.createElement('div');
        alertContainer.id = 'alertContainer';
        alertContainer.style.position = 'fixed';
        alertContainer.style.top = '20px';
        alertContainer.style.right = '20px';
        alertContainer.style.zIndex = '9999';
        document.body.appendChild(alertContainer);
    }
    
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.style.marginBottom = '10px';
    alertDiv.style.minWidth = '300px';
    alertDiv.style.boxShadow = '0 4px 12px rgba(0,0,0,0.15)';
    alertDiv.style.borderRadius = '8px';
    alertDiv.innerHTML = `
        <strong>${getAlertIcon(type)}</strong> ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    alertContainer.appendChild(alertDiv);
    
    // Auto dismiss after 5 seconds
    setTimeout(() => {
        if (alertDiv && alertDiv.parentNode) {
            alertDiv.classList.remove('show');
            setTimeout(() => alertDiv.remove(), 300);
        }
    }, 5000);
}

function getAlertIcon(type) {
    switch(type) {
        case 'success': return '<i class="fas fa-check-circle"></i>';
        case 'danger': return '<i class="fas fa-exclamation-circle"></i>';
        case 'warning': return '<i class="fas fa-exclamation-triangle"></i>';
        default: return '<i class="fas fa-info-circle"></i>';
    }
}

// ========================================
// NOTIFICATION FUNCTIONS
// ========================================

function initNotificationDismiss() {
    // Mark notification as read on click
    const notificationItems = document.querySelectorAll('.notification-item');
    notificationItems.forEach(item => {
        item.addEventListener('click', function(e) {
            if (!e.target.closest('a')) {
                const readLink = this.querySelector('a[href*="read_id"]');
                if (readLink) {
                    window.location.href = readLink.href;
                }
            }
        });
    });
}

// ========================================
// DATE FORMATTING
// ========================================

function formatDate(dateString) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('en-MY', options);
}

function formatDateTime(dateString) {
    const options = { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    };
    return new Date(dateString).toLocaleDateString('en-MY', options);
}

// ========================================
// PRINT FUNCTION
// ========================================

function printSection(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        const printContents = element.innerHTML;
        const originalContents = document.body.innerHTML;
        
        document.body.innerHTML = `
            <div style="padding: 20px;">
                <h2>Property Rental Management System</h2>
                <hr>
                ${printContents}
            </div>
        `;
        
        window.print();
        document.body.innerHTML = originalContents;
        location.reload();
    }
}

// ========================================
// CONFIRMATION DIALOG
// ========================================

function confirmAction(message, callback) {
    if (confirm(message)) {
        callback();
    }
}

// ========================================
// EXPORT TABLE TO CSV
// ========================================

function exportToCSV(tableId, filename = 'export.csv') {
    const table = document.getElementById(tableId);
    if (!table) return;
    
    const rows = table.querySelectorAll('tr');
    const csvData = [];
    
    rows.forEach(row => {
        const cols = row.querySelectorAll('td, th');
        const rowData = [];
        cols.forEach(col => {
            rowData.push('"' + col.innerText.replace(/"/g, '""') + '"');
        });
        csvData.push(rowData.join(','));
    });
    
    const csvContent = csvData.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    a.click();
    URL.revokeObjectURL(url);
}

// ========================================
// SEARCH/FILTER FUNCTION
// ========================================

function filterTable(searchInput, tableId) {
    const input = document.getElementById(searchInput);
    if (!input) return;
    
    input.addEventListener('keyup', function() {
        const filter = this.value.toLowerCase();
        const table = document.getElementById(tableId);
        const rows = table.getElementsByTagName('tr');
        
        for (let i = 1; i < rows.length; i++) {
            const cells = rows[i].getElementsByTagName('td');
            let found = false;
            
            for (let j = 0; j < cells.length; j++) {
                const cellText = cells[j].innerText.toLowerCase();
                if (cellText.indexOf(filter) > -1) {
                    found = true;
                    break;
                }
            }
            
            rows[i].style.display = found ? '' : 'none';
        }
    });
}