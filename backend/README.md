# Backend API Documentation

## Database Setup

### 1. Create Database
```bash
mysql -u root -p < schema/database.sql
```

### 2. Configure Database Connection
Edit `backend/config/database.php` and update:
- `DB_HOST` - Database host (default: localhost)
- `DB_USER` - Database user (default: root)
- `DB_PASS` - Database password
- `DB_NAME` - Database name (default: jsc_food_hub)

### 3. Update JWT Secret
Edit `backend/utils/helpers.php` and change `JWT_SECRET` to a strong secret key.

## API Endpoints

### Authentication

#### Login
- **POST** `/api/auth.php?action=login`
- **Request:**
  ```json
  {
    "username": "chef",
    "password": "chef123",
    "role": "chef"
  }
  ```
- **Response:**
  ```json
  {
    "success": true,
    "token": "eyJ...",
    "user": {
      "id": 1,
      "username": "chef",
      "role": "chef",
      "email": "chef@jscfoodhub.com"
    }
  }
  ```

#### Verify Token
- **GET** `/api/auth.php?action=verify`
- **Headers:** `Authorization: Bearer <token>`
- **Response:**
  ```json
  {
    "success": true,
    "user": {
      "userId": 1,
      "username": "chef",
      "role": "chef"
    }
  }
  ```

### Menu Items (Chef Only)

#### Get All Menu Items
- **GET** `/api/menu.php`
- **Headers:** `Authorization: Bearer <token>`

#### Add Menu Item
- **POST** `/api/menu.php`
- **Headers:** `Authorization: Bearer <token>`, `Content-Type: application/json`
- **Request:**
  ```json
  {
    "name": "Grilled Chicken",
    "category": "Main Course",
    "price": 15.99,
    "description": "Delicious grilled chicken",
    "availability": true
  }
  ```

#### Update Menu Item
- **PUT** `/api/menu.php`
- **Headers:** `Authorization: Bearer <token>`, `Content-Type: application/json`
- **Request:**
  ```json
  {
    "id": 1,
    "price": 16.99,
    "availability": true
  }
  ```

#### Delete Menu Item
- **DELETE** `/api/menu.php?id=1`
- **Headers:** `Authorization: Bearer <token>`

### Inventory (Admin Only)

#### Get Inventory
- **GET** `/api/inventory.php`
- **Headers:** `Authorization: Bearer <token>`

#### Add Stock
- **POST** `/api/inventory.php`
- **Headers:** `Authorization: Bearer <token>`, `Content-Type: application/json`
- **Request:**
  ```json
  {
    "name": "Chicken Breast",
    "quantity": 50,
    "unit": "kg",
    "low_stock_threshold": 10,
    "cost_per_unit": 8.50
  }
  ```

#### Update Stock
- **PUT** `/api/inventory.php`
- **Headers:** `Authorization: Bearer <token>`, `Content-Type: application/json`
- **Request:**
  ```json
  {
    "id": 1,
    "quantity": 45
  }
  ```

#### Delete Stock
- **DELETE** `/api/inventory.php?id=1`
- **Headers:** `Authorization: Bearer <token>`

### Sales (Admin Only)

#### Get Sales Records
- **GET** `/api/sales.php`
- **Headers:** `Authorization: Bearer <token>`

#### Record Sale
- **POST** `/api/sales.php`
- **Headers:** `Authorization: Bearer <token>`, `Content-Type: application/json`
- **Request:**
  ```json
  {
    "menu_item_id": 1,
    "quantity": 5,
    "price": 15.99
  }
  ```

## Default Credentials

### Chef
- **Username:** chef
- **Password:** chef123

### Admin
- **Username:** admin
- **Password:** admin123

## Important Notes

1. **Change Default Passwords:** Update the default user passwords in production.
2. **Update JWT Secret:** Change the secret key in `helpers.php`.
3. **Use HTTPS:** Always use HTTPS in production.
4. **Input Validation:** All inputs are validated and sanitized.
5. **Token Expiration:** Tokens expire after 24 hours.

## Error Handling

All endpoints return standardized JSON responses:

### Success Response (200)
```json
{
  "success": true,
  "message": "Operation successful",
  "data": { ... }
}
```

### Error Response (4xx/5xx)
```json
{
  "success": false,
  "message": "Error description"
}
```

## CORS

All API endpoints have CORS enabled for `*`. For production, restrict this to your frontend domain.