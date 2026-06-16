# MultiVendor Admin Module - Setup Guide

## 📁 Folder Structure
```
admin/
├── index.php              ← Admin Login Page
├── database.sql           ← Import this in phpMyAdmin
├── css/
│   └── admin.css
├── js/
│   └── admin.js
├── includes/
│   ├── db.php             ← Database config
│   ├── header.php
│   └── footer.php
└── pages/
    ├── dashboard.php      ← Dashboard
    ├── vendors.php        ← Vendor Approval
    ├── products.php       ← Product Management
    ├── orders.php         ← Order Management
    ├── customers.php      ← Customer Management
    ├── reports.php        ← Reports & Analytics
    └── logout.php
```

## ⚙️ Setup Steps (XAMPP)

1. **Copy folder** → Paste `admin` folder into `C:\xampp\htdocs\`

2. **Start XAMPP** → Start Apache and MySQL

3. **Import Database**:
   - Open http://localhost/phpmyadmin
   - Click "New" → Create database: `multivendor_db`
   - Click "Import" → Choose `database.sql` → Click "Go"

4. **Open Admin Panel**:
   - URL: http://localhost/admin/
   - Email: `admin@example.com`
   - Password: `password`

## 🔐 Default Login
| Field    | Value               |
|----------|---------------------|
| Email    | admin@example.com   |
| Password | password            |

## 📋 Features
- ✅ Admin Login with session
- ✅ Dashboard with charts
- ✅ Vendor Approval (Approve / Reject / Delete)
- ✅ Product Management (View / Toggle / Delete)
- ✅ Order Management (Update Status)
- ✅ Customer Management (Toggle / Delete)
- ✅ Reports & Analytics (Charts, Date Filter, Top Products, Top Vendors)

## 🛠️ Tech Stack
- Frontend: HTML, CSS, Bootstrap 5, Chart.js, DataTables
- Backend: PHP (PDO-ready, using mysqli)
- Database: MySQL
- Server: XAMPP (Apache + MySQL)
