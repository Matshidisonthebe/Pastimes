# Pastimes - Clothing Store

A PHP-based e-commerce application for buying and selling clothing items with admin controls.

## Features

- **User Registration & Authentication**
  - User registration with email and password
  - Admin approval system for new users
  - Secure password hashing

- **Shopping Functionality**
  - Browse products by category
  - Add/remove items from shopping cart
  - **Cart displays total amount** ✓
  - Product details with pricing

- **Admin Dashboard**
  - **Approve pending user accounts**
  - **Delete user accounts**
  - View all registered users
  - User status management (pending, approved, rejected)

- **Database**
  - 5 integrated tables: users, products, cart, orders, order_items
  - Sample clothing products pre-loaded

## Project Structure

```
Pastimes/
├── index.php              # Homepage
├── shop.php               # Product listing
├── cart.php               # Shopping cart with total display
├── login.php              # User login
├── register.php           # User registration
├── logout.php             # Session management
├── add_to_cart.php        # Cart operations
├── remove_from_cart.php   # Remove from cart
├── createTable.php        # Database initialization
├── loadClothing.php       # Load sample products
├── admin/
│   └── dashboard.php      # Admin controls for user management
├── css/
│   └── style.css          # Responsive styling
└── README.md              # Documentation
```

## Installation & Setup

### 1. Create Database
```bash
php createTable.php
```

### 2. Load Sample Products
```bash
php loadClothing.php
```

### 3. Export Database
- Open phpMyAdmin → clothingStore
- Export as `myClothingStore.sql`

### 4. Access the Application
- Homepage: `http://localhost/Pastimes/index.php`
- Shop: `http://localhost/Pastimes/shop.php`
- Admin Dashboard: `http://localhost/Pastimes/admin/dashboard.php`

## User Workflow

1. **Register** → User account created (status: pending)
2. **Wait for Admin Approval** → Admin approves user
3. **Login** → Access shopping cart
4. **Browse & Shop** → Add items to cart
5. **View Cart** → See items and **total amount**
6. **Checkout** → Complete order

## Admin Workflow

1. Login as admin user
2. Navigate to **Admin Dashboard**
3. View pending user approvals
4. **Approve** or **Delete** users
5. Manage user accounts and statuses

## Database Tables

### users
- User accounts with role and approval status

### products
- Clothing items with categories and pricing

### cart
- Shopping cart items per user

### orders
- Order history and totals

### order_items
- Individual items in each order

## Technology Stack

- **Backend:** PHP 7.4+
- **Database:** MySQL
- **Frontend:** HTML5, CSS3
- **Features:** Session Management, Password Hashing, CRUD Operations

## Security Features

- Password hashing with bcrypt
- SQL parameterized queries (prepared statements)
- Session management
- User approval workflow
- HTML escaping for output

## Requirements

- PHP 7.4+
- MySQL 5.7+
- phpMyAdmin (for database management)
- Web Server (Apache/Nginx)

## License

© 2026 Pastimes Clothing Store. All rights reserved.
