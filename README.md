# Pastimes - Clothing Store

A modern PHP-based e-commerce application designed for buying and selling clothing items with comprehensive admin controls and user management features.

**Project Status:** Active Development  
**Last Updated:** 2026  
**Repository:** [Matshidisonthebe/Pastimes](https://github.com/Matshidisonthebe/Pastimes)

---

## 📋 Table of Contents

- [Features](#-features)
- [Project Structure](#-project-structure)
- [Technology Stack](#-technology-stack)
- [Installation & Setup](#-installation--setup)
- [User Guide](#-user-guide)
- [Admin Guide](#-admin-guide)
- [Database Schema](#-database-schema)
- [Security Features](#-security-features)
- [Configuration](#-configuration)
- [Troubleshooting](#-troubleshooting)
- [Contributing](#-contributing)
- [License](#-license)

---

## ✨ Features

### User Management
- **User Registration & Authentication**
  - User registration with email and password validation
  - Admin approval system for new user accounts
  - Secure password hashing using bcrypt
  - Session-based authentication
  - User status tracking (pending, approved, rejected)

- **User Roles**
  - Regular Users: Can browse products and shop
  - Admin Users: Full access to dashboard and user management

### Shopping Functionality
- **Product Browsing**
  - Browse products by category
  - View detailed product information (name, price, description)
  - Product filtering and search capabilities
  
- **Shopping Cart**
  - Add/remove items from shopping cart
  - Real-time cart total calculation
  - Persistent cart data per user session
  - Quantity management for cart items

- **Checkout & Orders**
  - Complete checkout process
  - Order history tracking
  - Order items detail storage
  - Order total calculation

### Admin Dashboard
- **User Management**
  - View all registered users with status indicators
  - Approve pending user accounts
  - Delete/reject user accounts
  - Manage user account statuses
  - User role assignment

- **Product Management** (Database Integration Ready)
  - Sample clothing products pre-loaded
  - 5 integrated database tables
  - Easy data management through phpMyAdmin

---

## 📁 Project Structure

```
Pastimes/
├── index.php                    # Homepage landing page
├── shop.php                     # Product listing and browsing
├── cart.php                     # Shopping cart with total display
├── login.php                    # User authentication
├── register.php                 # New user registration
├── logout.php                   # Session termination
├── add_to_cart.php             # Cart addition functionality
├── remove_from_cart.php        # Cart removal functionality
├── createTable.php             # Database table initialization
├── loadClothing.php            # Load sample product data
├── admin/
│   ├── dashboard.php           # Admin control panel
│   └── [admin-specific files]  # Additional admin utilities
├── css/
│   └── style.css               # Responsive styling and layout
├── images/                     # Product images (if applicable)
├── README.md                   # Project documentation
└── .gitignore                  # Git ignore configuration
```

---

## 💻 Technology Stack

### Backend
- **Language:** PHP 7.4+
- **Pattern:** MVC-inspired structure
- **Session Management:** PHP Native Sessions
- **Password Security:** bcrypt hashing

### Database
- **DBMS:** MySQL 5.7+
- **Tables:** 5 (users, products, cart, orders, order_items)
- **Management Tool:** phpMyAdmin

### Frontend
- **Markup:** HTML5
- **Styling:** CSS3 with responsive design
- **Interactivity:** Form handling and basic JavaScript

### Server Requirements
- **Web Server:** Apache/Nginx
- **PHP Extensions:** MySQLi or PDO (for database operations)

---

## 🚀 Installation & Setup

### Prerequisites
Before you begin, ensure you have:
- PHP 7.4 or higher installed
- MySQL 5.7 or higher running
- phpMyAdmin installed (for database management)
- Apache or Nginx web server running
- A text editor or IDE (VS Code, PhpStorm, etc.)

### Step 1: Clone the Repository
```bash
git clone https://github.com/Matshidisonthebe/Pastimes.git
cd Pastimes
```

### Step 2: Create Database Tables
Run the database initialization script:
```bash
php createTable.php
```

This creates the following tables:
- `users` - User account information
- `products` - Product catalog
- `cart` - Shopping cart items
- `orders` - Order records
- `order_items` - Individual order line items

### Step 3: Load Sample Products
Load sample clothing products into the database:
```bash
php loadClothing.php
```

This populates the `products` table with sample clothing items and categories.

### Step 4: Configure Database Connection
Update database credentials in your PHP files if necessary:
```php
$host = 'localhost';
$db_user = 'root';
$db_password = '';
$database = 'clothingStore';
```

### Step 5: Export Database (Backup)
To backup your database:
1. Open phpMyAdmin in your browser
2. Navigate to the `clothingStore` database
3. Click **Export**
4. Choose format: **SQL**
5. Save as `myClothingStore.sql`

### Step 6: Access the Application
Open your web browser and navigate to:

| Page | URL |
|------|-----|
| Homepage | `http://localhost/Pastimes/index.php` |
| Shop | `http://localhost/Pastimes/shop.php` |
| Login | `http://localhost/Pastimes/login.php` |
| Register | `http://localhost/Pastimes/register.php` |
| Admin Dashboard | `http://localhost/Pastimes/admin/dashboard.php` |

---

## 👥 User Guide

### New User Registration

1. **Navigate to Register Page**
   - Go to `http://localhost/Pastimes/register.php`
   - Click "Sign Up" or "Register" link from homepage

2. **Fill Registration Form**
   - Enter your email address
   - Create a password (recommended: 8+ characters with mixed case)
   - Confirm your password
   - Click "Register"

3. **Wait for Admin Approval**
   - Your account status will be "pending"
   - Admin will review and approve your account
   - You'll receive notification once approved

4. **Login to Your Account**
   - Go to `http://localhost/Pastimes/login.php`
   - Enter email and password
   - Click "Login"

### Shopping Workflow

**Step 1: Browse Products**
- Visit the Shop page (`shop.php`)
- View all available clothing items
- Filter by category if available
- Click on products for more details

**Step 2: Add Items to Cart**
- Select desired product
- Choose quantity
- Click "Add to Cart"
- Item appears in your shopping cart

**Step 3: View Shopping Cart**
- Navigate to Cart page (`cart.php`)
- View all items in your cart
- See itemized prices and quantities
- **View total amount** at the bottom
- Update quantities if needed

**Step 4: Remove Items**
- In cart, click "Remove" next to unwanted items
- Confirm removal
- Cart updates with new total

**Step 5: Checkout**
- Review cart items and total
- Confirm order details
- Complete checkout process
- Order is saved to your history

### Account Management

- **View Account Status:** Check your profile page
- **Change Password:** (if feature available)
- **View Order History:** Check your previous orders
- **Update Profile:** Edit personal information (if available)

---

## 🔐 Admin Guide

### Admin Access

1. **Login as Admin**
   - Use an admin account email and password
   - Navigate to Admin Dashboard after login

2. **Access Dashboard**
   - URL: `http://localhost/Pastimes/admin/dashboard.php`
   - View all admin controls and user management options

### User Management

#### Viewing Users
- Dashboard displays all registered users
- See user email addresses and status
- Filter users by status (pending, approved, rejected)

#### Approving Users
1. Locate pending user in the list
2. Click "Approve" button
3. User status changes to "approved"
4. User receives notification
5. User can now login and shop

#### Deleting Users
1. Locate user in the list
2. Click "Delete" button
3. Confirm deletion
4. User account and associated data removed
5. User can register again if needed

#### User Status Management
- **Pending:** Awaiting admin approval
- **Approved:** Active and can login
- **Rejected:** Account denied (if feature available)

### Dashboard Features

| Feature | Description |
|---------|-------------|
| User List | View all registered users |
| Approval Queue | See pending users awaiting approval |
| Status Filter | Filter users by approval status |
| Delete Function | Remove user accounts permanently |
| Search | Search users by email (if available) |
| Export | Export user data (if available) |

### Best Practices
- Review pending users regularly
- Verify user information before approval
- Keep admin credentials secure
- Regularly backup database
- Monitor user activities

---

## 🗄️ Database Schema

### users Table
```
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL (bcrypt hashed),
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### products Table
```
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    category VARCHAR(100) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    description TEXT,
    stock INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### cart Table
```
CREATE TABLE cart (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);
```

### orders Table
```
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    status VARCHAR(50) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

### order_items Table
```
CREATE TABLE order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);
```

---

## 🔒 Security Features

### Password Security
- **bcrypt Hashing:** All passwords are hashed using PHP's `password_hash()` function
- **Salt Generation:** Automatic salt generation with bcrypt
- **Password Verification:** Using `password_verify()` for secure comparison

### Database Security
- **Prepared Statements:** SQL parameterized queries prevent SQL injection
- **Input Validation:** All user inputs are validated before processing
- **Escaping:** HTML special characters escaped for output

### Session Security
- **Session Management:** PHP native sessions with secure cookies
- **Session Timeout:** Sessions expire after inactivity
- **CSRF Protection:** (Implement token-based protection if needed)

### Access Control
- **Role-based Access:** Users vs Admins have different permissions
- **Authentication Required:** Protected pages check user session
- **Authorization:** Admin functions only accessible to admin users

### Best Practices
- Keep PHP and MySQL updated
- Use HTTPS in production
- Store credentials in environment variables
- Implement rate limiting for login attempts
- Regular security audits
- Monitor for suspicious activities

---

## ⚙️ Configuration

### Database Configuration
Edit the database connection in your PHP files:

```php
<?php
$host = 'localhost';
$db_user = 'root';
$db_password = '';
$database = 'clothingStore';
$port = 3306;

// Connection
$conn = new mysqli($host, $db_user, $db_password, $database, $port);

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}
?>
```

### Session Configuration
Configure session settings (usually in `php.ini`):

```ini
session.name = PHPSESSID
session.cookie_lifetime = 0
session.gc_maxlifetime = 1440
session.cookie_secure = 1      # Enable in production with HTTPS
session.cookie_httponly = 1    # Prevent JavaScript access
```

### Error Reporting
For development, enable error reporting:

```php
ini_set('display_errors', 1);
error_reporting(E_ALL);
```

For production, log errors instead:

```php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/path/to/error.log');
```

---

## 🐛 Troubleshooting

### Common Issues and Solutions

#### 1. Database Connection Failed
**Error:** "Connection failed: Access denied"

**Solutions:**
- Verify MySQL server is running
- Check database credentials (host, user, password)
- Ensure database `clothingStore` exists
- Verify user permissions in MySQL

```bash
# Test MySQL connection
mysql -u root -p -h localhost
```

#### 2. Tables Not Found
**Error:** "Table 'clothingStore.users' doesn't exist"

**Solution:**
- Run `createTable.php` to create tables
- Verify script executed without errors
- Check database in phpMyAdmin

```bash
php createTable.php
```

#### 3. No Sample Products
**Error:** Products table is empty

**Solution:**
- Run `loadClothing.php` to populate sample data
- Verify script executed successfully

```bash
php loadClothing.php
```

#### 4. Login Not Working
**Possible Causes:**
- User account not approved by admin
- Incorrect email/password
- Session not starting properly
- Cookies disabled in browser

**Solutions:**
1. Check user status in admin dashboard (should be "approved")
2. Verify email and password are correct
3. Clear browser cookies and try again
4. Check browser cookie settings

#### 5. Session Expires Too Quickly
**Solution:**
- Increase `session.gc_maxlifetime` in `php.ini`
- Default is 1440 seconds (24 minutes)

```ini
session.gc_maxlifetime = 3600  ; 1 hour
```

#### 6. Cart Shows Old Items After Logout
**Issue:** Cart data persists across sessions

**Solution:**
- Clear cart on logout: `unset($_SESSION['cart'])`
- Implement proper session cleanup

#### 7. Admin Dashboard Not Accessible
**Possible Causes:**
- Not logged in as admin
- Admin role not assigned
- Incorrect file path

**Solution:**
- Verify user has admin role in database
- Check file is at `admin/dashboard.php`
- Verify authentication session

#### 8. CSS Not Loading
**Error:** Page looks unstyled

**Solutions:**
- Verify `css/style.css` exists
- Check file path in HTML: `<link rel="stylesheet" href="css/style.css">`
- Clear browser cache (Ctrl+Shift+Delete)
- Check server has read permissions on CSS file

### Debug Mode

Enable debugging for development:

```php
<?php
// Debug mode
define('DEBUG', true);

if (DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}
?>
```

### Getting Help

If you encounter issues:
1. Check this troubleshooting section
2. Review error messages in browser console
3. Check PHP error logs: `/var/log/php-errors.log`
4. Check MySQL error logs: `/var/log/mysql/error.log`
5. Open an issue on GitHub with detailed error information

---

## 🤝 Contributing

We welcome contributions! Here's how you can help:

### How to Contribute

1. **Fork the Repository**
   ```bash
   # Click "Fork" on GitHub
   ```

2. **Clone Your Fork**
   ```bash
   git clone https://github.com/YOUR-USERNAME/Pastimes.git
   cd Pastimes
   ```

3. **Create a Feature Branch**
   ```bash
   git checkout -b feature/your-feature-name
   ```

4. **Make Your Changes**
   - Follow existing code style
   - Write clear, descriptive commits
   - Add comments for complex logic
   - Test thoroughly

5. **Commit Your Changes**
   ```bash
   git commit -m "Add: Brief description of changes"
   ```

6. **Push to Your Fork**
   ```bash
   git push origin feature/your-feature-name
   ```

7. **Create a Pull Request**
   - Go to GitHub and create a Pull Request
   - Describe your changes
   - Reference any related issues

### Contribution Guidelines

- **Code Quality:** Write clean, maintainable code
- **Comments:** Document complex sections
- **Testing:** Test features before submitting PR
- **Security:** Never commit sensitive data (passwords, API keys)
- **Naming:** Use clear, descriptive variable/function names
- **Standards:** Follow PSR-12 PHP coding standards

### Areas for Contribution

- Bug fixes and improvements
- Feature enhancements
- Documentation updates
- Test coverage
- Performance optimization
- Security improvements
- UI/UX enhancements

---

## 📝 License

© 2026 Pastimes Clothing Store. All rights reserved.

This project is provided as-is for educational and commercial purposes.

### Permissions
- ✅ Use commercially
- ✅ Modify the code
- ✅ Distribute copies
- ✅ Include in other projects

### Conditions
- ⚠️ Include license notice
- ⚠️ Document significant changes

### Limitations
- ❌ No warranty provided
- ❌ No liability accepted

---

## 📞 Support & Contact

**Project Owner:** [Matshidisonthebe](https://github.com/Matshidisonthebe)  
**Repository:** [GitHub - Pastimes](https://github.com/Matshidisonthebe/Pastimes)

### Get Help

- 📖 **Documentation:** Check README.md and code comments
- 🐛 **Report Issues:** Open an issue on GitHub
- 💬 **Discussions:** Use GitHub Discussions
- 🔍 **Wiki:** Check repository wiki for guides

---

## 🎯 Roadmap

**Future Features:**
- [ ] Payment gateway integration
- [ ] Email notifications
- [ ] Product reviews and ratings
- [ ] Advanced search and filters
- [ ] Inventory management
- [ ] Order tracking
- [ ] User profile customization
- [ ] Two-factor authentication
- [ ] Mobile app version
- [ ] API endpoints

---

**Thank you for using Pastimes! Happy shopping! 🛍️**
