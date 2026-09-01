# JSC Food Hub - Button Functionality Guide

## Overview
This guide explains all button functionalities in the JSC Canteen Food Hub application and how they integrate with the backend.

---

## 🔐 Login Page Buttons

### Button: "Login"
**Location:** `index.html` - Main login form submit button

**Functionality:**
```javascript
// Sends authentication request to backend
POST /api/auth.php?action=login
{
    "username": "string",
    "password": "string",
    "role": "chef|admin"
}
```

**Response on Success:**
- Stores authentication token in localStorage
- Redirects to appropriate dashboard
- Sets user session data

**Response on Failure:**
- Displays error message
- Allows retry

**HTML:**
```html
<button type="submit" class="btn-login">Login</button>
```

---

## 👨‍🍳 Chef Dashboard Buttons

### 1. Sidebar Navigation Buttons
**Location:** `pages/chef-dashboard.html` - Left sidebar

**Buttons:**
- 📊 Overview
- 🍽️ Menu Items
- 📦 Stock Levels
- 🔔 Notifications
- 🚪 Logout

**Functionality:**
```javascript
// Click handler
sidebarLinks.forEach(link => {
    link.addEventListener('click', function(e) {
        const pageName = this.dataset.page;
        navigateChefPage(pageName);
    });
});
```

### 2. "Add New Menu Item" Button
**Location:** Chef Dashboard - Menu Items section

**Functionality:**
```javascript
// Opens modal form
<button class="btn btn-success" onclick="openChefModal('addMenuModal')">
    + Add New Menu Item
</button>

// Submits to backend
POST /api/menu_items.php
{
    "name": "Jollof Rice",
    "category": "Main Course",
    "price": 25.99,
    "description": "Delicious African rice",
    "is_available": true,
    "created_by": 2
}
```

**Success Response:**
- Menu item created with ID
- Notification: "Menu item added successfully!"
- Page refreshes to show new item

### 3. "Edit Menu Item" Button
**Location:** Chef Dashboard - Menu Items table (for each item)

**Functionality:**
```javascript
// Opens edit modal with pre-filled data
<button class="btn btn-secondary" onclick="openEditMenuModal(menuItem.id)">
    Edit
</button>

// Updates backend
PUT /api/menu_items.php?id=X
{
    "name": "Updated name",
    "category": "Category",
    "price": 29.99,
    "description": "Updated description",
    "is_available": true
}
```

### 4. "Delete Menu Item" Button
**Location:** Chef Dashboard - Menu Items table (for each item)

**Functionality:**
```javascript
// Confirms deletion
<button class="btn btn-danger" onclick="deleteMenuItem(menuItem.id)">
    Delete
</button>

// Sends delete request
DELETE /api/menu_items.php?id=X

// On success:
// - Item removed from database
// - Table refreshes
// - Notification: "Menu item deleted!"
```

### 5. Stock Alert Action Buttons
**Location:** Chef Dashboard - Stock Levels section

**"Restock Alert" Button:**
```javascript
// Notifies admin of low stock
<button class="btn btn-warning" onclick="sendRestockAlert(stockId)">
    Notify Admin
</button>
```

---

## ⚙️ Admin Dashboard Buttons

### 1. Sidebar Navigation Buttons
**Location:** `pages/admin-dashboard.html` - Left sidebar

**Buttons:**
- 📊 Overview
- 📦 Inventory Management
- ⚠️ Stock Alerts
- 💰 Sales Tracking
- 📄 Reports
- 🚪 Logout

**Functionality:** Same as Chef sidebar - navigates between pages

### 2. "Add Stock Item" Button
**Location:** Admin Dashboard - Inventory Management

**Functionality:**
```javascript
// Opens modal form
<button class="btn btn-success" onclick="openAdminModal('addStockModal')">
    + Add Stock Item
</button>

// Submits to backend
POST /api/inventory.php
{
    "name": "Flour",
    "quantity": 50,
    "unit": "kg",
    "low_stock_threshold": 10,
    "cost_per_unit": 5.50
}
```

**API Response:**
```json
{
    "success": true,
    "message": "Stock item added successfully",
    "id": 123
}
```

### 3. "Update Stock" Button
**Location:** Inventory table (for each item)

**Functionality:**
```javascript
// Opens update modal
<button class="btn btn-secondary" onclick="openUpdateStockModal(stockId)">
    Update
</button>

// Updates backend
PUT /api/inventory.php?id=X
{
    "quantity": 45,
    "low_stock_threshold": 10
}

// Auto-updates status to "low" or "normal"
// Triggers alerts if threshold exceeded
```

### 4. "Delete Stock" Button
**Location:** Inventory table (for each item)

**Functionality:**
```javascript
// Confirms deletion
if (confirm('Are you sure?')) {
    DELETE /api/inventory.php?id=X
}

// On success:
// - Stock removed from database
// - Inventory list updates
// - Notification: "Stock item deleted!"
```

### 5. "Restock Now" Button
**Location:** Stock Alerts section

**Functionality:**
```javascript
// Quick update for low stock items
<button class="btn btn-danger" onclick="openUpdateStockModal(stockId)">
    Restock Now
</button>

// Opens update modal pre-filled with current item
```

### 6. "Record Sale" Button
**Location:** Sales Tracking section

**Functionality:**
```javascript
// Opens sale recording form
<button class="btn btn-success" onclick="openRecordSaleModal()">
    + Record Sale
</button>

// Submits sale to backend
POST /api/sales.php
{
    "menu_item_id": 5,
    "quantity": 2,
    "unit_price": 15.99
}

// Calculates total: quantity × unit_price
// Records in database
// Updates revenue statistics
```

### 7. Report Generation Buttons
**Location:** Reports section

**Buttons:**
```javascript
// Inventory Report
<button class="btn btn-info" onclick="generateReport('inventory')">
    📊 Generate Inventory Report
</button>

// Sales Report
<button class="btn btn-info" onclick="generateReport('sales')">
    💰 Generate Sales Report
</button>

// Alerts Report
<button class="btn btn-info" onclick="generateReport('alerts')">
    ⚠️ Generate Alerts Report
</button>
```

**API Call:**
```javascript
GET /api/reports.php?type=inventory
GET /api/reports.php?type=sales
GET /api/reports.php?type=alerts
```

**Response Contains:**
```json
{
    "success": true,
    "report_type": "Inventory Report",
    "generated_at": "2026-09-01 10:30:00",
    "summary": {
        "total_items": 15,
        "total_quantity": 200,
        "total_inventory_value": 1050.00,
        "low_stock_count": 3
    },
    "details": [...]
}
```

**Download Action:**
- Report downloads as text file
- Filename format: `{type}-report-{timestamp}.txt`
- Notification: "Report downloaded!"

### 8. "Logout" Button
**Location:** Top-right corner / Sidebar footer

**Functionality:**
```javascript
function logout() {
    // Clear session storage
    localStorage.removeItem('authToken');
    localStorage.removeItem('userRole');
    localStorage.removeItem('username');
    localStorage.removeItem('userId');
    
    // Redirect to login
    window.location.href = '../index.html';
}
```

---

## 🔄 Modal Button Actions

### Modal Close Buttons
**Close (X) Button:**
```javascript
// Closes modal without saving
<button class="close" onclick="closeAdminModal('modalId')">×</button>

// Keyboard shortcut
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeAllModals();
    }
});
```

### Modal Submit Buttons
**Submit/Save Button:**
```javascript
<button type="submit" class="btn btn-success">Save Changes</button>

// Triggers form validation
// Sends data to API
// Updates UI on success
// Shows error message on failure
```

---

## 📊 Quick Actions & Button Combinations

### Add → Submit Flow
```
1. Click "Add..." button
2. Modal opens with form
3. Fill in required fields
4. Click "Save" button
5. Form validates
6. API sends POST request
7. Success notification shown
8. List/table refreshes
9. Modal closes
```

### Edit → Update Flow
```
1. Click "Edit" button
2. Modal opens with pre-filled data
3. Modify fields
4. Click "Update" button
5. API sends PUT request
6. Success notification
7. Table/list refreshes
8. Modal closes
```

### Delete Flow
```
1. Click "Delete" button
2. Confirmation dialog appears
3. User confirms
4. API sends DELETE request
5. Item removed
6. List refreshes
7. Success notification
```

---

## 🎨 Button Styling Classes

```html
<!-- Primary Action (Green) -->
<button class="btn btn-success">Save</button>

<!-- Secondary Action (Gray) -->
<button class="btn btn-secondary">Edit</button>

<!-- Danger Action (Red) -->
<button class="btn btn-danger">Delete</button>

<!-- Info Action (Blue) -->
<button class="btn btn-info">Report</button>

<!-- Warning Action (Orange) -->
<button class="btn btn-warning">Alert</button>

<!-- Login Form -->
<button class="btn-login">Login</button>
```

---

## 🔗 Integration Checklist

- [x] Login button sends credentials to `/api/auth.php`
- [x] Menu buttons use `/api/menu_items.php`
- [x] Inventory buttons use `/api/inventory.php`
- [x] Sales buttons use `/api/sales.php`
- [x] Report buttons use `/api/reports.php`
- [x] All buttons validate input before sending
- [x] Error handling with user notifications
- [x] Success notifications after actions
- [x] Modal management (open/close)
- [x] Logout clears session storage
- [x] Keyboard shortcuts (Escape to close modals)

---

## 📝 Example: Full Button Implementation

### HTML
```html
<button class="btn btn-success" onclick="openAddMenuModal()">
    + Add Menu Item
</button>
```

### JavaScript
```javascript
async function openAddMenuModal() {
    const modal = document.getElementById('addMenuModal');
    modal.classList.add('show');
}

async function handleAddMenu(e) {
    e.preventDefault();
    
    const menuItem = {
        name: document.getElementById('menuName').value,
        category: document.getElementById('menuCategory').value,
        price: parseFloat(document.getElementById('menuPrice').value),
        description: document.getElementById('menuDesc').value,
        is_available: true,
        created_by: localStorage.getItem('userId')
    };
    
    try {
        const response = await fetch('backend/api/menu_items.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${localStorage.getItem('authToken')}`
            },
            body: JSON.stringify(menuItem)
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification('Menu item added successfully!', 'success');
            closeAdminModal('addMenuModal');
            loadMenuItems(); // Refresh list
        } else {
            showNotification(data.message, 'danger');
        }
    } catch (error) {
        showNotification('Error: ' + error.message, 'danger');
    }
}
```

---

## 🚀 Testing Buttons

1. **Login Button**
   - Test with valid credentials: admin/admin123 or chef/chef123
   - Test with invalid credentials
   - Test with missing fields

2. **CRUD Buttons**
   - Add → Verify item appears in list
   - Edit → Verify changes saved
   - Delete → Verify item removed
   - List refresh → Verify updates immediate

3. **Report Buttons**
   - Click report button
   - Verify modal/download appears
   - Check file contains correct data

4. **Logout Button**
   - Click logout
   - Verify redirected to login page
   - Verify session cleared

---

## 📞 Support

For button functionality issues, check:
1. Browser console for errors
2. Network tab in DevTools for API responses
3. Backend logs at `backend/logs/`
4. Database connections in `backend/config/Database.php`

Contact: muzunzetheresa@gmail.com
