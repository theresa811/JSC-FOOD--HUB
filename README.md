# JSC Canteen Food Hub

A responsive web application for canteen management with role-based access for Chef and Admin users.

## Features

### 🍳 Chef Dashboard
- **Add, edit, and update daily menu items**
- View current stock levels for ingredients
- Menu automatically updates when changes are made
- Monitor real-time inventory
- Receive notifications for low stock alerts

### ⚙️ Admin Dashboard
- **Monitor stock levels in real-time**
- Receive automatic notifications when stock is low
- Stock quantities automatically deducted when menu items are sold
- Ability to restock and update inventory
- Sales tracking and revenue monitoring
- Generate comprehensive reports (Inventory, Sales, Alerts)
- Analytics and profit margin calculations

### 🔒 General Features
- User authentication with role-based access (Chef vs Admin)
- Clean, modern UI with green and gold color scheme
- Dynamic updates and notifications with JavaScript
- Mobile-friendly responsive design
- Local storage for data persistence
- In-app notification system for low stock alerts

## Tech Stack

- **Frontend:** HTML5, CSS3, JavaScript (ES6+)
- **Backend:** PHP/Node.js (ready for integration)
- **Database:** MySQL (ready for integration)
- **Styling:** Responsive CSS with Green (#2D6A4F), Gold (#D4AF37), and White (#FFFFFF)

## Project Structure

```
JSC-Canteen-Food-Hub/
├── index.html                    # Login page
├── css/
│   └── style.css                 # Main stylesheet
├── js/
│   ├── login.js                  # Login functionality
│   ├── dashboard.js              # Common dashboard utilities
│   ├── chef-dashboard.js         # Chef-specific functions
│   └── admin-dashboard.js        # Admin-specific functions
├── pages/
│   ├── chef-dashboard.html       # Chef dashboard
│   └── admin-dashboard.html      # Admin dashboard
└── README.md                      # This file
```

## Demo Credentials

### Chef Login
- **Username:** chef
- **Password:** chef123

### Admin Login
- **Username:** admin
- **Password:** admin123

## Getting Started

### Installation

1. Clone the repository:
```bash
git clone https://github.com/theresa811/JSC-FOOD--HUB.git
cd JSC-Canteen-Food-Hub
```

2. Open in a web browser:
   - Simply open `index.html` in your web browser
   - Or use a local server:
     ```bash
     python -m http.server 8000
     ```

3. Access the application:
   - Open `http://localhost:8000` (if using server)
   - Or directly open `index.html`

## Usage

### For Chefs
1. Log in with Chef credentials
2. **Overview:** View dashboard statistics and recent menu items
3. **Menu Items:** 
   - Add new menu items with details (name, category, price, description)
   - Edit existing menu items
   - Delete menu items
   - View all current menu items
4. **Stock Levels:** Monitor ingredient inventory and low stock alerts
5. **Notifications:** Check system notifications and alerts

### For Admins
1. Log in with Admin credentials
2. **Overview:** View all system statistics and critical alerts
3. **Inventory Management:**
   - Add ingredients with quantity and cost
   - Update stock quantities
   - Track total inventory value
   - Delete outdated items
4. **Stock Alerts:**
   - View all low stock items
   - Monitor stock status
   - Quick restock actions
5. **Sales Tracking:**
   - Record menu item sales
   - Track revenue
   - View sales history
6. **Reports:**
   - Generate Inventory reports
   - Generate Sales reports
   - Generate Alerts reports
   - Download reports as text files

## Features in Detail

### Menu Management
- Create menu items with categories (Main Course, Side Dish, Dessert, Beverage, Snack)
- Set prices and availability status
- Add item descriptions
- Edit and delete items easily

### Inventory System
- Track ingredient quantities with different units (kg, lb, liters, pieces, dozen, box)
- Set low stock thresholds
- Track cost per unit and total inventory value
- Automatic status updates (Normal/Low)

### Stock Alerts
- Real-time low stock notifications
- Visual alerts for critical items
- Quick restock action buttons
- Color-coded status indicators

### Sales Tracking
- Record menu item sales
- Track revenue by item
- View sales history
- Calculate profit margins

### Reports
- Inventory status reports
- Sales performance reports
- Alert history reports
- Downloadable reports in text format

## Local Storage Implementation

The application uses browser localStorage for data persistence:
- `userRole` - Current user's role (chef/admin)
- `username` - Logged-in username
- `loginTime` - Login timestamp
- `menuItems` - Array of menu items
- `inventoryStocks` - Array of inventory items
- `salesRecords` - Array of sales transactions

**Note:** This is suitable for testing and demo purposes. For production, integrate with a backend database.

## API Integration (Future)

The application is structured to easily integrate with a backend:
- Replace localStorage calls with API endpoints
- Add server-side validation
- Implement database storage
- Add authentication tokens
- Enable multi-user sessions

## Responsive Design

- Mobile-friendly layout
- Tablet optimized views
- Desktop full-featured interface
- Responsive navigation sidebar
- Flexible grid layouts for tables and stats

## Color Scheme

- **Primary Green:** #2D6A4F
- **Light Green:** #40916C
- **Gold Accent:** #D4AF37
- **White:** #FFFFFF
- **Dark Background:** #1B4332

## Future Enhancements

- [ ] Backend integration with PHP/Node.js
- [ ] MySQL database implementation
- [ ] User account management
- [ ] Password reset functionality
- [ ] Advanced analytics and charts
- [ ] Export to Excel/PDF
- [ ] Email notifications
- [ ] Multi-location support
- [ ] API documentation
- [ ] Unit testing

## Security Considerations

- Currently uses demo credentials for testing
- Implement proper authentication for production
- Add password hashing and salting
- Use HTTPS for data transmission
- Add CSRF protection
- Implement role-based access control on backend
- Validate all user inputs on server

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

For support, email muzunzetheresa@gmail.com or open an issue on GitHub.

## Author

**Theresa Muzunze**
- GitHub: [@theresa811](https://github.com/theresa811)
- Email: muzunzetheresa@gmail.com

---

**Last Updated:** September 1, 2026