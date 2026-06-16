# MultiVendor E-Commerce System

A complete multi-vendor e-commerce platform with three integrated portals: **Admin**, **Vendor**, and **Customer** — all sharing a single MySQL database.

## Project Structure
```
ecommerce_project/
├── index.php            → Landing page (links to all 3 portals)
├── database.sql         → MySQL Database (shared by all modules)
│
├── admin/                → Admin Module
│   ├── index.php         → Admin Login
│   └── pages/            → dashboard, vendors, products, orders, customers, reports
│
├── vendor/                → Vendor Module
│   ├── index.php          → Vendor Login
│   ├── register.php       → Vendor Registration
│   ├── uploads/products/  → Uploaded product images
│   └── pages/             → dashboard, shop, products, add_product, orders, sales
│
└── customer/              → Customer Module (new)
    ├── index.php          → Customer Login
    ├── register.php       → Customer Registration
    └── pages/
        ├── home.php              → Product browsing (search + category filter)
        ├── product.php           → Product details
        ├── cart.php              → Shopping cart
        ├── cart_action.php       → AJAX add/update cart
        ├── cart_action_remove.php→ Remove cart item
        ├── wishlist.php          → Wishlist
        ├── wishlist_action.php   → AJAX wishlist toggle
        ├── checkout.php          → Place order
        ├── order_success.php     → Order confirmation
        ├── orders.php            → Order history
        ├── order_detail.php      → Order tracking & details
        ├── profile.php           → Profile management
        └── logout.php
```

## Setup Instructions
1. Install XAMPP and start Apache + MySQL
2. Copy the `ecommerce_project/` folder to `C:/xampp/htdocs/`
3. Open phpMyAdmin → Import `database.sql` (this creates all tables for the Admin, Vendor and Customer modules, including `cart` and `wishlist`)
4. Visit: http://localhost/ecommerce_project/

## Portals & Login Credentials

### Landing Page
- URL: http://localhost/ecommerce_project/
- Links to all three portals below.

### Admin Panel
- URL: http://localhost/ecommerce_project/admin/
- Email: admin@example.com
- Password: password

### Vendor Panel
- URL: http://localhost/ecommerce_project/vendor/
- Register a new vendor OR use sample:
  - Email: raj@vendor.com
  - Password: password

### Customer Storefront
- URL: http://localhost/ecommerce_project/customer/
- Register a new account, OR use a sample customer email
  (e.g. arun@gmail.com) with the same sample password used above.

## Customer Module Features
- Customer Registration & Login
- Profile Management (update details, change password)
- Product Browsing (search + category filters, product detail pages)
- Shopping Cart (add/update/remove items, live cart badge)
- Wishlist (add/remove, move to cart)
- Place Orders (shipping address, payment method, order tracking)

## Admin · Product Management Module
- Add Product (`admin/pages/add_product.php`) — choose vendor, category, price, stock, image
- Update Product — edit any product's details, category, image, status
- Delete Product — remove a product (with confirmation)
- Product Categories (`admin/pages/categories.php`) — add/edit/delete/activate categories, with parent categories
- Product Search — search by product name or vendor, filter by category
- Product Details (`admin/pages/product_view.php`) — full product info, vendor info, sales stats

## Technology Stack
- Frontend: HTML, CSS, JavaScript, Bootstrap 5
- Backend: PHP
- Database: MySQL
- Server: XAMPP
