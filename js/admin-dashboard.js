/* ============================================
   ADMIN-DASHBOARD.JS - Admin Specific Functions
   ============================================ */

document.addEventListener('DOMContentLoaded', function() {
    initializeAdminDashboard();
    setupAdminEventListeners();
    loadAdminDashboard();
});

function setupAdminEventListeners() {
    // Sidebar navigation
    const sidebarLinks = document.querySelectorAll('.sidebar-menu .menu-link');
    sidebarLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const pageName = this.dataset.page;
            navigateAdminPage(pageName);
            
            // Update active link
            sidebarLinks.forEach(l => l.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // Add stock form
    const addStockForm = document.getElementById('addStockForm');
    if (addStockForm) {
        addStockForm.addEventListener('submit', handleAddStock);
    }

    // Update stock form
    const updateStockForm = document.getElementById('updateStockForm');
    if (updateStockForm) {
        updateStockForm.addEventListener('submit', handleUpdateStock);
    }

    // Record sale form
    const recordSaleForm = document.getElementById('recordSaleForm');
    if (recordSaleForm) {
        recordSaleForm.addEventListener('submit', handleRecordSale);
    }
}

function initializeAdminDashboard() {
    console.log('Admin Dashboard initialized');
    loadInventory();
    updateAdminStats();
    loadCriticalAlerts();
}

function navigateAdminPage(pageName) {
    // Hide all pages
    const pageContents = document.querySelectorAll('.page-content');
    pageContents.forEach(page => page.classList.remove('active'));

    // Show selected page
    const selectedPage = document.getElementById(pageName);
    if (selectedPage) {
        selectedPage.classList.add('active');
    }

    // Reload data based on page
    if (pageName === 'inventory-management') {
        loadInventory();
    } else if (pageName === 'stock-alerts') {
        loadStockAlerts();
    } else if (pageName === 'sales-tracking') {
        loadSalesTracking();
    } else if (pageName === 'reports') {
        generateReportSummary();
    }
}

function loadInventory() {
    const stocks = dashboardUtils.inventoryManager.getAllStocks();
    const tableBody = document.getElementById('inventoryTableBody');
    const noInventoryMessage = document.getElementById('noInventoryMessage');

    tableBody.innerHTML = '';

    if (stocks.length === 0) {
        noInventoryMessage.style.display = 'block';
    } else {
        noInventoryMessage.style.display = 'none';

        stocks.forEach(stock => {
            const row = document.createElement('tr');
            const statusClass = stock.quantity <= stock.lowStockThreshold ? 'alert-danger' : 'alert-success';
            const statusText = stock.quantity <= stock.lowStockThreshold ? '⚠️ LOW' : '✓ OK';
            const totalCost = (stock.quantity * stock.costPerUnit).toFixed(2);

            row.innerHTML = `
                <td>${stock.name}</td>
                <td>${stock.quantity}</td>
                <td>${stock.unit}</td>
                <td>${stock.lowStockThreshold}</td>
                <td><span class="alert ${statusClass}" style="display: inline-block; padding: 5px 10px; border-radius: 3px; border-left: none;">${statusText}</span></td>
                <td>${dashboardUtils.formatCurrency(totalCost)}</td>
                <td>${dashboardUtils.formatDate(stock.lastUpdated)}</td>
                <td>
                    <button class="btn btn-secondary" onclick="openUpdateStockModal(${stock.id})">Update</button>
                    <button class="btn btn-danger" onclick="deleteStock(${stock.id})">Delete</button>
                </td>
            `;
            tableBody.appendChild(row);
        });
    }

    updateAdminStats();
}

function handleAddStock(e) {
    e.preventDefault();

    const stock = {
        name: document.getElementById('ingredientName').value,
        quantity: parseFloat(document.getElementById('quantity').value),
        unit: document.getElementById('unit').value,
        lowStockThreshold: parseFloat(document.getElementById('lowStockThreshold').value),
        costPerUnit: parseFloat(document.getElementById('costPerUnit').value),
        status: 'normal',
        lastUpdated: new Date().toISOString()
    };

    dashboardUtils.inventoryManager.addStock(stock);
    dashboardUtils.showNotification('Stock item added successfully!', 'success');
    
    e.target.reset();
    loadInventory();
}

function openUpdateStockModal(stockId) {
    const stock = dashboardUtils.inventoryManager.getStock(stockId);
    if (!stock) return;

    document.getElementById('updateStockId').value = stockId;
    document.getElementById('updateQuantity').value = stock.quantity;
    document.getElementById('updateNote').value = '';

    openAdminModal('updateStockModal');
}

function handleUpdateStock(e) {
    e.preventDefault();

    const stockId = parseInt(document.getElementById('updateStockId').value);
    const newQuantity = parseFloat(document.getElementById('updateQuantity').value);

    dashboardUtils.inventoryManager.updateStock(stockId, newQuantity);
    dashboardUtils.showNotification('Stock updated successfully!', 'success');
    
    closeAdminModal('updateStockModal');
    loadInventory();
    loadStockAlerts();
}

function deleteStock(stockId) {
    if (confirm('Are you sure you want to delete this stock item?')) {
        const stocks = dashboardUtils.inventoryManager.getAllStocks();
        const filtered = stocks.filter(s => s.id !== stockId);
        localStorage.setItem('inventoryStocks', JSON.stringify(filtered));
        dashboardUtils.showNotification('Stock item deleted!', 'success');
        loadInventory();
    }
}

function loadStockAlerts() {
    const lowStocks = dashboardUtils.inventoryManager.getLowStockItems();
    const alertsList = document.getElementById('stockAlertsList');
    const stockStatusBody = document.getElementById('stockStatusBody');

    // Load low stock alerts
    if (lowStocks.length === 0) {
        alertsList.innerHTML = '<p style="text-align: center; color: #999;">All stock levels are normal!</p>';
    } else {
        let alertsHtml = '';
        lowStocks.forEach(stock => {
            const percentage = ((stock.quantity / stock.lowStockThreshold) * 100).toFixed(0);
            alertsHtml += `
                <div class="alert alert-danger" style="margin-bottom: 10px;">
                    <strong>⚠️ ${stock.name}</strong><br>
                    Current: ${stock.quantity} ${stock.unit} | Threshold: ${stock.lowStockThreshold} ${stock.unit}
                    <br>
                    <small>Stock level at ${percentage}% of threshold</small>
                </div>
            `;
        });
        alertsList.innerHTML = alertsHtml;
    }

    // Load stock status table
    const allStocks = dashboardUtils.inventoryManager.getAllStocks();
    stockStatusBody.innerHTML = '';

    allStocks.forEach(stock => {
        const row = document.createElement('tr');
        const isLow = stock.quantity <= stock.lowStockThreshold;
        const statusClass = isLow ? 'alert-danger' : 'alert-success';
        const statusText = isLow ? '⚠️ LOW STOCK' : '✓ Normal';
        const action = isLow ? '<button class="btn btn-danger" onclick="openUpdateStockModal(' + stock.id + ')">Restock Now</button>' : 'Monitor';

        row.innerHTML = `
            <td>${stock.name}</td>
            <td>${stock.quantity} ${stock.unit}</td>
            <td>${stock.lowStockThreshold}</td>
            <td><span class="alert ${statusClass}" style="display: inline-block; padding: 5px 10px; border-radius: 3px; border-left: none;">${statusText}</span></td>
            <td>${action}</td>
        `;
        stockStatusBody.appendChild(row);
    });
}

function loadSalesTracking() {
    const menuItems = dashboardUtils.menuManager.getAllItems();
    const saleMenuSelect = document.getElementById('saleMenuItem');
    
    // Populate menu items dropdown
    saleMenuSelect.innerHTML = '<option value="">Select Menu Item</option>';
    menuItems.forEach(item => {
        const option = document.createElement('option');
        option.value = item.id;
        option.textContent = `${item.name} - ${dashboardUtils.formatCurrency(item.price)}`;
        saleMenuSelect.appendChild(option);
    });

    // Load sales records
    const sales = JSON.parse(localStorage.getItem('salesRecords')) || [];
    const salesTableBody = document.getElementById('salesTableBody');
    const noSalesMessage = document.getElementById('noSalesMessage');

    salesTableBody.innerHTML = '';

    if (sales.length === 0) {
        noSalesMessage.style.display = 'block';
    } else {
        noSalesMessage.style.display = 'none';
        sales.slice().reverse().forEach(sale => {
            const row = document.createElement('tr');
            const itemName = dashboardUtils.menuManager.getItem(sale.menuItemId)?.name || 'Deleted Item';
            row.innerHTML = `
                <td>${itemName}</td>
                <td>${sale.quantity}</td>
                <td>${dashboardUtils.formatCurrency(sale.price)}</td>
                <td>${dashboardUtils.formatDate(sale.timestamp)}</td>
            `;
            salesTableBody.appendChild(row);
        });
    }
}

function handleRecordSale(e) {
    e.preventDefault();

    const menuItemId = parseInt(document.getElementById('saleMenuItem').value);
    const quantity = parseInt(document.getElementById('saleQuantity').value);
    const price = parseFloat(document.getElementById('salePrice').value);

    if (!menuItemId) {
        dashboardUtils.showNotification('Please select a menu item', 'danger');
        return;
    }

    const sale = {
        id: Date.now(),
        menuItemId,
        quantity,
        price: price * quantity,
        timestamp: new Date().toISOString()
    };

    const sales = JSON.parse(localStorage.getItem('salesRecords')) || [];
    sales.push(sale);
    localStorage.setItem('salesRecords', JSON.stringify(sales));

    dashboardUtils.showNotification('Sale recorded successfully!', 'success');
    e.target.reset();
    loadSalesTracking();
    updateAdminStats();
}

function loadCriticalAlerts() {
    const lowStocks = dashboardUtils.inventoryManager.getLowStockItems();
    const alertsList = document.getElementById('criticalAlertsList');

    if (lowStocks.length === 0) {
        alertsList.innerHTML = '<p style="text-align: center; color: #999;">No critical alerts at this time.</p>';
    } else {
        let alertsHtml = '';
        lowStocks.forEach(stock => {
            alertsHtml += `
                <div class="alert alert-warning" style="margin-bottom: 10px;">
                    <strong>${stock.name}</strong> - Current Stock: ${stock.quantity} ${stock.unit}
                </div>
            `;
        });
        alertsList.innerHTML = alertsHtml;
    }
}

function updateAdminStats() {
    const menuItems = dashboardUtils.menuManager.getAllItems();
    const stocks = dashboardUtils.inventoryManager.getAllStocks();
    const lowStocks = dashboardUtils.inventoryManager.getLowStockItems();
    const sales = JSON.parse(localStorage.getItem('salesRecords')) || [];

    const totalSales = sales.reduce((sum, sale) => sum + sale.price, 0);

    document.getElementById('adminTotalMenuItems').textContent = menuItems.length;
    document.getElementById('adminTotalStock').textContent = stocks.length;
    document.getElementById('adminLowStockAlerts').textContent = lowStocks.length;
    document.getElementById('adminTotalSales').textContent = dashboardUtils.formatCurrency(totalSales);
}

function generateReportSummary() {
    const menuItems = dashboardUtils.menuManager.getAllItems();
    const stocks = dashboardUtils.inventoryManager.getAllStocks();
    const lowStocks = dashboardUtils.inventoryManager.getLowStockItems();
    const sales = JSON.parse(localStorage.getItem('salesRecords')) || [];

    const totalSales = sales.reduce((sum, sale) => sum + sale.price, 0);
    const totalCost = stocks.reduce((sum, stock) => sum + (stock.quantity * stock.costPerUnit), 0);
    const avgItemPrice = menuItems.length > 0 ? (menuItems.reduce((sum, item) => sum + item.price, 0) / menuItems.length).toFixed(2) : 0;

    const summary = `
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            <div class="stat-card">
                <h4>Total Menu Items</h4>
                <div class="value">${menuItems.length}</div>
            </div>
            <div class="stat-card">
                <h4>Total Inventory Items</h4>
                <div class="value">${stocks.length}</div>
            </div>
            <div class="stat-card">
                <h4>Low Stock Items</h4>
                <div class="value">${lowStocks.length}</div>
            </div>
            <div class="stat-card">
                <h4>Total Sales Revenue</h4>
                <div class="value">${dashboardUtils.formatCurrency(totalSales)}</div>
            </div>
            <div class="stat-card">
                <h4>Total Inventory Cost</h4>
                <div class="value">${dashboardUtils.formatCurrency(totalCost)}</div>
            </div>
            <div class="stat-card">
                <h4>Avg Item Price</h4>
                <div class="value">${dashboardUtils.formatCurrency(avgItemPrice)}</div>
            </div>
        </div>
        <div style="margin-top: 20px;">
            <p><strong>Total Sales Records:</strong> ${sales.length}</p>
            <p><strong>Profit Margin:</strong> ${totalSales > 0 ? ((totalSales - totalCost) / totalSales * 100).toFixed(2) : 0}%</p>
        </div>
    `;

    document.getElementById('reportSummary').innerHTML = summary;
}

function generateReport(reportType) {
    const menuItems = dashboardUtils.menuManager.getAllItems();
    const stocks = dashboardUtils.inventoryManager.getAllStocks();
    const sales = JSON.parse(localStorage.getItem('salesRecords')) || [];

    let reportContent = '';

    if (reportType === 'inventory') {
        reportContent = 'INVENTORY REPORT\n\n';
        reportContent += `Generated: ${new Date().toLocaleString()}\n\n`;
        reportContent += 'STOCK ITEMS:\n';
        stocks.forEach(stock => {
            reportContent += `${stock.name}: ${stock.quantity} ${stock.unit} (Threshold: ${stock.lowStockThreshold})\n`;
        });
    } else if (reportType === 'sales') {
        reportContent = 'SALES REPORT\n\n';
        reportContent += `Generated: ${new Date().toLocaleString()}\n\n`;
        reportContent += 'SALES RECORDS:\n';
        sales.forEach(sale => {
            reportContent += `${dashboardUtils.formatCurrency(sale.price)} - ${dashboardUtils.formatDate(sale.timestamp)}\n`;
        });
        reportContent += `\nTotal Sales: ${dashboardUtils.formatCurrency(sales.reduce((sum, s) => sum + s.price, 0))}\n`;
    } else if (reportType === 'alerts') {
        reportContent = 'ALERTS REPORT\n\n';
        reportContent += `Generated: ${new Date().toLocaleString()}\n\n`;
        const lowStocks = dashboardUtils.inventoryManager.getLowStockItems();
        reportContent += `Low Stock Items (${lowStocks.length}):\n`;
        lowStocks.forEach(stock => {
            reportContent += `${stock.name}: ${stock.quantity} ${stock.unit}\n`;
        });
    }

    // Download report
    const blob = new Blob([reportContent], { type: 'text/plain' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `${reportType}-report-${new Date().getTime()}.txt`;
    document.body.appendChild(a);
    a.click();
    window.URL.revokeObjectURL(url);
    document.body.removeChild(a);

    dashboardUtils.showNotification(`${reportType} report downloaded!`, 'success');
}

function loadAdminDashboard() {
    updateAdminStats();
    loadCriticalAlerts();
}

function openAdminModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('show');
    }
}

function closeAdminModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('show');
    }
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.classList.remove('show');
    }
});