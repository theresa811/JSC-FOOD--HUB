# JSC Food Hub - Backend Setup Guide

## Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache web server with mod_rewrite enabled

## Installation Steps

### 1. Database Setup

**Option A: Automatic Setup (Recommended)**
1. Open your browser and navigate to: `http://localhost/jsc-food-hub/backend/config/init_database.php`
2. The script will create the database and all necessary tables
3. Demo users will be automatically created:
   - **Admin:** username: `admin`, password: `admin123`
   - **Chef:** username: `chef`, password: `chef123`

**Option B: Manual Setup**
1. Open phpMyAdmin or MySQL command line
2. Run the SQL commands from the database schema in the config folder
3. Insert the demo users with hashed passwords

### 2. Configuration

Edit `backend/config/Database.php` if needed:
```php
private $host = 'localhost';
private $db_name = 'jsc_food_hub';
private $user = 'root';
private $password = ''; // Add your password if needed
```

### 3. File Structure

```
backend/
├── config/
│   ├── Database.php          # Database connection class
│   └── init_database.php     # Database initialization script
├── api/
│   ├── auth.php              # Authentication API
│   ├── menu_items.php        # Menu items CRUD API
│   ├── inventory.php         # Inventory management API
│   ├── sales.php             # Sales tracking API
│   └── reports.php           # Reports generation API
└── .htaccess                 # Apache configuration for CORS
```

## API Endpoints

### Authentication
- **POST** `/api/auth.php?action=login` - User login
  ```json
  {
    "username": "admin",
    "password": "admin123",
    "role": "admin"
  }
  ```

### Menu Items
- **GET** `/api/menu_items.php` - Get all menu items
- **POST** `/api/menu_items.php` - Create menu item
- **PUT** `/api/menu_items.php?id=X` - Update menu item
- **DELETE** `/api/menu_items.php?id=X` - Delete menu item

### Inventory
- **GET** `/api/inventory.php` - Get all inventory
- **GET** `/api/inventory.php?low=1` - Get low stock items
- **POST** `/api/inventory.php` - Add stock item
- **PUT** `/api/inventory.php?id=X` - Update stock quantity
- **DELETE** `/api/inventory.php?id=X` - Delete stock item

### Sales
- **GET** `/api/sales.php` - Get all sales records
- **GET** `/api/sales.php?stats=1` - Get sales statistics
- **POST** `/api/sales.php` - Record new sale

### Reports
- **GET** `/api/reports.php?type=inventory` - Inventory report
- **GET** `/api/reports.php?type=sales` - Sales report
- **GET** `/api/reports.php?type=alerts` - Alerts report
- **GET** `/api/reports.php?type=dashboard` - Dashboard overview

## Frontend Integration

The frontend uses the `ApiClient` JavaScript module to communicate with the backend.

### Usage Example:

```javascript
// Login
await ApiClient.auth.login('admin', 'admin123', 'admin');

// Get all inventory
const inventory = await ApiClient.inventory.getAll();

// Add new stock item
await ApiClient.inventory.add({
    name: 'Flour',
    quantity: 50,
    unit: 'kg',
    low_stock_threshold: 10,
    cost_per_unit: 5.50
});

// Record a sale
await ApiClient.sales.record({
    menu_item_id: 1,
    quantity: 2,
    unit_price: 15.99
});

// Get reports
const inventoryReport = await ApiClient.reports.getInventoryReport();
```

## Security Considerations

1. **HTTPS:** Use HTTPS in production for secure data transmission
2. **Authentication:** Implement JWT tokens for better security
3. **Input Validation:** All inputs are validated on the server
4. **Password Hashing:** Passwords are hashed using PHP's password_hash()
5. **SQL Injection Prevention:** Using prepared statements with PDO
6. **CORS:** Configure CORS properly for production

## Troubleshooting

### Database Connection Error
- Verify MySQL is running
- Check credentials in `Database.php`
- Ensure database exists or run `init_database.php`

### API Not Responding
- Check Apache is running
- Verify mod_rewrite is enabled
- Check `.htaccess` file permissions
- View error logs in Apache error log

### CORS Errors
- Ensure `.htaccess` is properly configured
- Check browser console for specific error messages
- Verify API URLs match frontend configuration

## Converting Frontend to Use Backend

### Step 1: Replace localStorage calls
```javascript
// Old (Frontend only)
const items = JSON.parse(localStorage.getItem('menuItems'));

// New (With Backend)
const response = await ApiClient.menuItems.getAll();
const items = response.data;
```

### Step 2: Update event handlers
```javascript
// Old
function handleAddMenu(e) {
    // Store in localStorage
    localStorage.setItem('menuItems', JSON.stringify(items));
}

// New
async function handleAddMenu(e) {
    const result = await ApiClient.menuItems.create(newMenuItem);
    if (result.success) {
        loadMenuItems();
    }
}
```

### Step 3: Add API client to HTML
```html
<script src="js/api-client.js"></script>
```

## Production Deployment

1. Update `Database.php` with production credentials
2. Enable HTTPS
3. Set up proper authentication with JWT tokens
4. Configure firewall and access controls
5. Set up automated backups
6. Monitor API performance and errors
7. Implement rate limiting on APIs
8. Add comprehensive logging

## Support

For issues or questions, contact: muzunzetheresa@gmail.com
