# 🎓 Student Management System — SPA

PHP + MySQL + Vanilla JS Single Page Application

---

## 📁 Project Structure

```
student-management/
├── index.html          ← Main SPA (entry point)
├── database.sql        ← MySQL setup script
├── css/
│   └── style.css       ← All styles (responsive)
├── js/
│   └── app.js          ← SPA logic (CRUD, search, UI)
└── api/
    ├── config.php      ← DB connection & helpers
    └── students.php    ← REST API (GET/POST/PUT/DELETE)
```

---

## ⚙️ Setup Instructions

### Step 1 — Requirements
- PHP 7.4+  (XAMPP / WAMP / LAMP / MAMP)
- MySQL 5.7+
- Web Browser (Chrome / Firefox / Edge)

### Step 2 — Database Setup
1. Open **phpMyAdmin** → http://localhost/phpmyadmin
2. Click **Import** → Choose `database.sql`
3. Click **Go**

   **OR** run in MySQL terminal:
   ```sql
   source /path/to/student-management/database.sql;
   ```

### Step 3 — Configure Database
Open `api/config.php` and update if needed:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');      // your MySQL username
define('DB_PASS', '');          // your MySQL password
define('DB_NAME', 'student_management');
```

### Step 4 — Run the App
1. Copy the `student-management/` folder to your web server root:
   - XAMPP → `C:/xampp/htdocs/student-management/`
   - WAMP  → `C:/wamp64/www/student-management/`
   - Linux → `/var/www/html/student-management/`

2. Open browser: **http://localhost/student-management/**

---

## ✨ Features

| Feature           | Description                                      |
|-------------------|--------------------------------------------------|
| 📋 Student List   | View all students in a sortable table            |
| ➕ Add Student    | Add new student with form validation             |
| ✏️ Edit Student   | Update student details inline                    |
| 🗑️ Delete Student | Confirm & delete with safety modal               |
| 🔍 Search         | Real-time search by name/email/course/dept/phone |
| 📊 Stats Cards    | Live totals: students, courses, departments      |
| 📱 Responsive     | Mobile-friendly sidebar + layout                 |
| 🔔 Toast Alerts   | Success/error notifications                      |

---

## 🌐 API Endpoints

Base URL: `api/students.php`

| Method | Action         | Body / Params                  |
|--------|----------------|-------------------------------|
| GET    | List all       | —                             |
| GET    | Search         | `?search=query`               |
| GET    | Get one        | `?id=1`                       |
| POST   | Add student    | JSON body with student fields |
| PUT    | Update student | JSON body with id + fields    |
| DELETE | Delete student | JSON body `{ "id": 1 }`       |

---

## 📦 Tech Stack

- **Frontend**: HTML5, CSS3 (Custom), Vanilla JavaScript (ES6+)
- **Backend**: PHP 8 (REST API)
- **Database**: MySQL (via mysqli)
- **Fonts**: Inter (Google Fonts)
- **No frameworks** — pure HTML/CSS/JS/PHP

---

## 🛠️ Troubleshooting

| Issue                        | Solution                                          |
|------------------------------|---------------------------------------------------|
| Blank page / no data         | Check PHP server is running                       |
| DB connection error          | Verify credentials in `api/config.php`            |
| CORS error                   | Run from localhost, not file:// protocol          |
| Table not found              | Run `database.sql` in phpMyAdmin                  |
