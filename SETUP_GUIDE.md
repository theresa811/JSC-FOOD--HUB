# Database Integration Setup Guide

## Overview
This guide will help you set up the database integration for JSC Food Hub.

## Prerequisites
- PHP 7.4+ with MySQLi extension
- MySQL 5.7+
- A local or remote MySQL server
- Web server (Apache, Nginx, or built-in PHP server)

## Step-by-Step Setup

### Step 1: Database Setup

1. **Open MySQL Command Line**
   ```bash
   mysql -u root -p
   ```

2. **Run the Database Schema**
   ```bash
   source backend/schema/database.sql;
   ```
   Or copy and paste the contents of `backend/schema/database.sql` into MySQL.

3. **Verify the Database**
   ```sql
   USE jsc_food_hub;
   SHOW TABLES;
   ```

### Step 2: Configure Backend

1. **Edit Database Configuration**
   Open `backend/config/database.php` and update:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', 'your_password'); // Change this
   define('DB_NAME', 'jsc_food_hub');
   define('DB_PORT', 3306);
   ```

2. **Update JWT Secret**
   Open `backend/utils/helpers.php` and change:
   ```php
   define('JWT_SECRET', 'your_super_secret_key_change_this');
   ```
   To something like:
   ```php
   define('JWT_SECRET', 'aK3jL9mP2xQ5wZ8vN1bC6yD4eF7gH0sT');
   ```

### Step 3: Frontend Updates

The frontend (`login.js`, `admin-dashboard.js`) is already configured to use the backend API:

- Login attempts to connect to `backend/api/auth.php?action=login`
- If the backend is unavailable, it falls back to demo credentials
- All data operations use API endpoints

### Step 4: Run the Application

#### Option A: Using PHP Built-in Server
```bash
cd /path/to/JSC-FOOD--HUB
php -S localhost:8000
```
Then open: `http://localhost:8000`

#### Option B: Using Apache
1. Copy the project to your Apache web root
2. Configure virtual host if needed
3. Access via your configured URL

### Step 5: Test the Connection

1. **Open the Application**
   - Navigate to `http://localhost:8000`

2. **Test Login**
   - Chef: `username: chef`, `password: chef123`, `role: chef`
   - Admin: `username: admin`, `password: admin123`, `role: admin`

3. **Test API Directly** (using curl or Postman)
   ```bash
   curl -X POST http://localhost:8000/backend/api/auth.php?action=login \
     -H "Content-Type: application/json" \
     -d '{"username":"chef","password":"chef123","role":"chef"}'
   ```

## Database Tables

### users
Stores user credentials and roles
- `id` - Primary key
- `username` - Unique username
- `password` - Hashed password (bcrypt)
- `role` - 'chef' or 'admin'
- `email`, `full_name` - User information

### menu_items
Stores menu items created by chefs
- `id` - Primary key
- `name` - Item name
- `category` - Category (Main Course, Side Dish, etc.)
- `price` - Item price
- `availability` - Available or not
- `created_by` - Reference to user

### inventory_stocks
Stores inventory items
- `id` - Primary key
- `name` - Item name
- `quantity` - Current quantity
- `unit` - Unit of measurement
- `low_stock_threshold` - Alert threshold
- `cost_per_unit` - Cost per unit
- `status` - 'Normal' or 'Low'

### sales_records
Stores sales transactions
- `id` - Primary key
- `menu_item_id` - Reference to menu item
- `quantity` - Quantity sold
- `price` - Unit price
- `total_amount` - Total sale amount
- `recorded_by` - Reference to user

### stock_alerts
Stores stock alerts
- `id` - Primary key
- `stock_id` - Reference to inventory item
- `alert_type` - Type of alert
- `is_resolved` - Alert status

## Troubleshooting

### "Database connection failed"
1. Check MySQL is running
2. Verify credentials in `database.php`
3. Ensure database `jsc_food_hub` exists
4. Check database user has proper permissions

### "No tables found"
1. Run the schema file again
2. Verify the schema executed without errors
3. Check: `SHOW TABLES;` in the database

### "Login failed but demo works"
1. Check `backend/config/database.php` settings
2. Verify API endpoints are accessible
3. Check browser console for errors (F12 → Console)
4. Test API with curl command above

### "CORS Errors"
1. Ensure API files have CORS headers
2. Check browser console for specific error messages
3. Verify API endpoints are being called (Network tab in DevTools)

## Production Considerations

1. **Security**
   - Use HTTPS only
   - Change all default credentials
   - Use a strong JWT secret
   - Implement rate limiting
   - Sanitize all inputs

2. **Performance**
   - Add database indexes
   - Implement caching
   - Use connection pooling
   - Monitor query performance

3. **Database**
   - Regular backups
   - Use managed database services
   - Enable SSL for DB connections
   - Monitor disk space

4. **Deployment**
   - Use environment variables for configuration
   - Implement logging
   - Monitor API response times
   - Set up error tracking

## Support

For issues or questions, contact: muzunzetheresa@gmail.com