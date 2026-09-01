/* ============================================
   COMMON BUTTON HANDLERS & UTILITIES
   Shared functions for all buttons across dashboards
   ============================================ */

// ===== NOTIFICATION SYSTEM =====
function showNotification(message, type = 'success', duration = 3000) {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <span>${message}</span>
            <button class="notification-close" onclick="this.parentElement.parentElement.remove()">×</button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.add('show');
    }, 10);
    
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, duration);
}

// ===== MODAL MANAGEMENT =====
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('show');
        modal.style.display = 'block';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('show');
        modal.style.display = 'none';
    }
}

function closeAllModals() {
    const modals = document.querySelectorAll('.modal.show');
    modals.forEach(modal => modal.classList.remove('show'));
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.classList.remove('show');
    }
});

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeAllModals();
    }
});

// ===== FORM VALIDATION =====
function validateFormFields(fields) {\n    for (let field of fields) {\n        if (!field.value || field.value.trim() === '') {\n            showNotification(`${field.name || 'Field'} is required`, 'danger');\n            return false;\n        }\n    }\n    return true;\n}\n\nfunction validateEmail(email) {\n    const emailRegex = /^[^\\s@]+@[^\\s@]+\\.[^\\s@]+$/;\n    return emailRegex.test(email);\n}\n\nfunction validatePrice(price) {\n    const priceRegex = /^\\d+(\\.\\d{1,2})?$/;\n    return priceRegex.test(price) && parseFloat(price) > 0;\n}\n\n// ===== BUTTON CONFIRMATION DIALOGS =====\nfunction confirmAction(message, callback) {\n    if (confirm(message)) {\n        callback();\n    }\n}\n\n// ===== DATA FORMATTING =====\nfunction formatCurrency(value) {\n    return new Intl.NumberFormat('en-US', {\n        style: 'currency',\n        currency: 'USD'\n    }).format(value);\n}\n\nfunction formatDate(dateString) {\n    return new Date(dateString).toLocaleDateString('en-US', {\n        year: 'numeric',\n        month: 'short',\n        day: 'numeric'\n    });\n}\n\nfunction formatDateTime(dateString) {\n    return new Date(dateString).toLocaleString('en-US', {\n        year: 'numeric',\n        month: 'short',\n        day: 'numeric',\n        hour: '2-digit',\n        minute: '2-digit'\n    });\n}\n\n// ===== API HELPERS =====\nasync function fetchAPI(endpoint, method = 'GET', data = null) {\n    const options = {\n        method,\n        headers: {\n            'Content-Type': 'application/json',\n            'Authorization': `Bearer ${localStorage.getItem('authToken')}`\n        }\n    };\n    \n    if (data) {\n        options.body = JSON.stringify(data);\n    }\n    \n    try {\n        const response = await fetch(endpoint, options);\n        const result = await response.json();\n        return result;\n    } catch (error) {\n        showNotification(`Error: ${error.message}`, 'danger');\n        return { success: false, message: error.message };\n    }\n}\n\n// ===== LOGOUT HANDLER =====\nfunction logout() {\n    confirmAction('Are you sure you want to logout?', function() {\n        localStorage.removeItem('authToken');\n        localStorage.removeItem('userRole');\n        localStorage.removeItem('username');\n        localStorage.removeItem('userId');\n        localStorage.removeItem('loginTime');\n        window.location.href = '../index.html';\n    });\n}\n\n// ===== LOADING STATE =====\nfunction setButtonLoading(button, isLoading = true) {\n    if (isLoading) {\n        button.disabled = true;\n        button.dataset.originalText = button.textContent;\n        button.textContent = 'Loading...';\n    } else {\n        button.disabled = false;\n        button.textContent = button.dataset.originalText || button.textContent;\n    }\n}\n\n// ===== TABLE REFRESH HELPERS =====\nfunction clearTableBody(tableId) {\n    const tbody = document.getElementById(tableId);\n    if (tbody) {\n        tbody.innerHTML = '';\n    }\n}\n\nfunction showEmptyMessage(containerId, message = 'No data available') {\n    const container = document.getElementById(containerId);\n    if (container) {\n        container.innerHTML = `<p style=\"text-align: center; color: #999; padding: 20px;\">${message}</p>`;\n    }\n}\n\n// ===== EXPORT TO CSV =====\nfunction exportTableToCSV(tableId, filename = 'export.csv') {\n    const table = document.getElementById(tableId);\n    let csv = [];\n    \n    // Get headers\n    const headers = table.querySelectorAll('thead th');\n    let headerRow = [];\n    headers.forEach(header => headerRow.push(header.textContent));\n    csv.push(headerRow.join(','));\n    \n    // Get rows\n    const rows = table.querySelectorAll('tbody tr');\n    rows.forEach(row => {\n        let rowData = [];\n        row.querySelectorAll('td').forEach(cell => {\n            rowData.push('\"' + cell.textContent.replace(/\"/g, '\"\"') + '\"');\n        });\n        csv.push(rowData.join(','));\n    });\n    \n    // Download\n    downloadCSV(csv.join('\\n'), filename);\n}\n\nfunction downloadCSV(csvContent, filename) {\n    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });\n    const link = document.createElement('a');\n    const url = URL.createObjectURL(blob);\n    link.setAttribute('href', url);\n    link.setAttribute('download', filename);\n    link.style.visibility = 'hidden';\n    document.body.appendChild(link);\n    link.click();\n    document.body.removeChild(link);\n}\n\n// ===== SESSION MANAGEMENT =====\nfunction checkAuthentication() {\n    const authToken = localStorage.getItem('authToken');\n    const userRole = localStorage.getItem('userRole');\n    \n    if (!authToken || !userRole) {\n        window.location.href = '../index.html';\n        return false;\n    }\n    return true;\n}\n\nfunction getUserInfo() {\n    return {\n        id: localStorage.getItem('userId'),\n        username: localStorage.getItem('username'),\n        role: localStorage.getItem('userRole'),\n        token: localStorage.getItem('authToken')\n    };\n}\n\n// ===== KEYBOARD SHORTCUTS =====\ndocument.addEventListener('keydown', function(e) {\n    // Ctrl+S or Cmd+S to submit form\n    if ((e.ctrlKey || e.metaKey) && e.key === 's') {\n        e.preventDefault();\n        const form = document.querySelector('form');\n        if (form) form.submit();\n    }\n    \n    // Ctrl+E or Cmd+E to export\n    if ((e.ctrlKey || e.metaKey) && e.key === 'e') {\n        e.preventDefault();\n        const exportBtn = document.querySelector('[onclick*=\"export\"]');\n        if (exportBtn) exportBtn.click();\n    }\n});\n\n// ===== DEBOUNCE FOR SEARCH =====\nfunction debounce(func, wait) {\n    let timeout;\n    return function executedFunction(...args) {\n        const later = () => {\n            clearTimeout(timeout);\n            func(...args);\n        };\n        clearTimeout(timeout);\n        timeout = setTimeout(later, wait);\n    };\n}\n\n// ===== SEARCH FUNCTIONALITY =====\nfunction setupTableSearch(inputId, tableId) {\n    const input = document.getElementById(inputId);\n    if (!input) return;\n    \n    input.addEventListener('keyup', debounce(function() {\n        const filter = this.value.toUpperCase();\n        const table = document.getElementById(tableId);\n        const rows = table.querySelectorAll('tbody tr');\n        \n        rows.forEach(row => {\n            const text = row.textContent.toUpperCase();\n            row.style.display = text.includes(filter) ? '' : 'none';\n        });\n    }, 300));\n}\n\n// ===== PRINT PAGE =====\nfunction printPage(elementId = null) {\n    if (elementId) {\n        const element = document.getElementById(elementId);\n        const printWindow = window.open('', '', 'height=400,width=800');\n        printWindow.document.write(element.outerHTML);\n        printWindow.document.close();\n        printWindow.print();\n    } else {\n        window.print();\n    }\n}\n\n// ===== TOOLTIP INITIALIZATION =====\nfunction initializeTooltips() {\n    const tooltips = document.querySelectorAll('[data-tooltip]');\n    tooltips.forEach(element => {\n        element.addEventListener('mouseover', function() {\n            const tooltip = document.createElement('div');\n            tooltip.className = 'tooltip';\n            tooltip.textContent = this.dataset.tooltip;\n            document.body.appendChild(tooltip);\n            \n            const rect = this.getBoundingClientRect();\n            tooltip.style.top = (rect.top - tooltip.offsetHeight - 5) + 'px';\n            tooltip.style.left = (rect.left + rect.width/2 - tooltip.offsetWidth/2) + 'px';\n        });\n        \n        element.addEventListener('mouseout', function() {\n            const tooltips = document.querySelectorAll('.tooltip');\n            tooltips.forEach(t => t.remove());\n        });\n    });\n}\n\n// Initialize on page load\ndocument.addEventListener('DOMContentLoaded', function() {\n    checkAuthentication();\n    initializeTooltips();\n});\n