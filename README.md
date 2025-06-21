# Lost and Found Website

A simple web application for university students and staff to report, search, and retrieve lost or found items.

## Features
- User registration and login
- Report lost or found items
- Search and filter items
- Admin panel for approvals and management
- Notifications for item status

## Technology Stack
- PHP (backend)
- MySQL (database)
- JavaScript (client-side validation)
- HTML, CSS, Bootstrap (UI)

## Setup Instructions
1. Import the `db.sql` file into your MySQL server to create the database and tables.
2. Configure your PHP environment (e.g., XAMPP, WAMP, or LAMP).
3. Place the project files in your web server's root directory.
4. Update database connection settings in `config.php` (to be created).
5. Access the site via your browser.

## File Structure
- `index.php` – Homepage
- `register.php` – User registration
- `login.php` – User login
- `dashboard.php` – User dashboard
- `report_lost.php` – Report lost item
- `report_found.php` – Report found item
- `search.php` – Search items
- `admin_dashboard.php` – Admin panel
- `logout.php` – Logout
- `db.sql` – Database schema

## Security Notes
- Passwords are hashed using `password_hash()`
- SQL injection is prevented using prepared statements
- Role-based access control for admin and users

---

*For educational purposes. Customize as needed for your institution.* 