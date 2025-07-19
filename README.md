# Lost and Found Website

A simple web application for university students and staff to report, search, and retrieve lost or found items.

## Features
- User registration and login
- Report lost or found items with contact details
- Search and filter items
- Contact information display for easy communication
- Admin panel for approvals and management
- Notifications for item status

## Technology Stack
- PHP (backend)
- MySQL (database)
- JavaScript (client-side validation)
- HTML, CSS, Tailwind CSS (UI)

## Setup Instructions
1. Import the `db.sql` file into your MySQL server to create the database and tables.
2. If you have an existing database, run the `update_db_contact_details.sql` script to add contact details columns.
3. Configure your PHP environment (e.g., XAMPP, WAMP, or LAMP).
4. Place the project files in your web server's root directory.
5. Update database connection settings in `config.php` (to be created).
6. Access the site via your browser.

## New Features - Contact Details
- **Phone Number**: Users can provide a phone number for contact (optional)
- **Email Address**: Users can provide an email address for contact (optional)
- **Validation**: At least one contact method (phone or email) is required
- **Display**: Contact information is shown on search results and dashboard
- **Clickable Links**: Phone numbers and emails are clickable for easy contact

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
- `update_db_contact_details.sql` – Database update script for existing installations

## Security Notes
- Passwords are hashed using `password_hash()`
- SQL injection is prevented using prepared statements
- Role-based access control for admin and users
- Contact information is validated and sanitized

---

*For educational purposes. Customize as needed for your institution.* 