# Centre for Computer Science and Applications (CCSA)
### Dibrugarh University, Assam, India

<div align="center">
  <img src="src/faculty/du.png" alt="Dibrugarh University Logo" width="120" height="120" />

  <p align="center">
    <strong>Official Web Portal & Academic Management System</strong><br />
    Approved by AICTE | Accredited by NAAC | GIGW 3.0 & UX4G Compliant
  </p>

  <p align="center">
    <img src="https://img.shields.io/badge/PHP-8.1%2B-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.1+" />
    <img src="https://img.shields.io/badge/MySQL-8.0%2B-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL 8.0+" />
    <img src="https://img.shields.io/badge/TailwindCSS-v3.4-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS" />
    <img src="https://img.shields.io/badge/Security-OWASP%20Hardened-green?style=for-the-badge&logo=shield" alt="Security Hardened" />
    <img src="https://img.shields.io/badge/GIGW-3.0%20Compliant-008080?style=for-the-badge" alt="GIGW 3.0" />
  </p>
</div>

---

## 📌 About the Project

This repository contains the complete source code for the official website and student/academic management portal for the **Centre for Computer Science and Applications (CCSA)**, Dibrugarh University ([ccsdu.in](https://ccsdu.in)).

The portal provides:
- **Academic Program Portals:** Detailed course curriculum, eligibility criteria, and fee structures for **BCA**, **MCA (AICTE Approved)**, **PGDCA**, and **Ph.D.**
- **Central University Proxy Synchronization:** Live integration with Dibrugarh University's central APIs for real-time faculty profiles, publications, and student directories.
- **Admin Management Suite:** Role-based administrative dashboard for publishing circulars/notices with PDF attachments, pinning urgent announcements, and reviewing contact inquiries.
- **Spotlight Search (`Ctrl + K` / `⌘K`):** Instant client-side search engine indexing courses, faculty members, notices, and institutional links.
- **GIGW 3.0 & UX4G Accessibility:** High-contrast accessibility standards, font resizing (`A- / A / A+`), and mobile responsiveness.
- **Alumni Portal Redirection:** Direct routing to the official alumni network portal ([ccsaalumni.in](https://ccsaalumni.in)).

---

## 🛠️ Tech Stack & Requirements

- **Backend:** PHP 8.1+ (PDO, OpenSSL, cURL, Fileinfo, Mbstring extensions)
- **Database:** MySQL 8.0+ / MariaDB 10.4+ (InnoDB engine, `utf8mb4_unicode_ci`)
- **Frontend:** Tailwind CSS v3.4, Vanilla JavaScript (ES6+), FontAwesome 6
- **Web Server:** Apache with `mod_rewrite` enabled (or Nginx / PHP CLI server for development)

---

## 📁 Repository Structure

```text
├── .htaccess                   # Root Apache rewrite and security rules
├── .gitignore                  # Git ignore rules for temporary and private files
├── README.md                   # Project documentation
├── INSTRUCTION.md              # Detailed setup walkthrough
└── src/
    ├── .htaccess               # Subdirectory routing & security headers
    ├── index.php               # Homepage & Hero Showcase
    ├── database.sql            # MySQL Database Schema & Default Seed Data
    ├── undergraduate.php       # BCA Programme Details
    ├── postgraduate.php        # MCA Programme Details
    ├── pgdca.php               # PGDCA Programme Details
    ├── phd.php                 # Ph.D. Programme Details
    ├── faculty.php             # Faculty Directory (Proxy-synced)
    ├── Present_Stu.php         # Current Enrolled Students Directory
    ├── research.php            # Departmental Research & Labs
    ├── publication.php         # Faculty & Research Publications
    ├── notices.php             # Official Notice Board
    ├── alumni.php              # Direct 301 Redirect to ccsaalumni.in
    ├── proxy.php               # API Proxy for central university data
    ├── admin/                  # Secure Admin Control Panel
    │   ├── index.php           # Admin Dashboard (Notices, Messages, Logs)
    │   ├── login.php           # Admin Login
    │   ├── config.php          # Admin DB configuration
    │   ├── actions.php         # Secure CRUD actions handler
    │   └── uploads/            # Document and notice uploads directory
    ├── includes/               # Core classes (Database, Security, Session, Cache)
    └── templates/              # Shared components (header.php, footer.php, nav-data.php)
```

---

## 🚀 Quick Setup & Installation Guide

### 1. Clone the Repository
```bash
git clone https://github.com/YOUR_USERNAME/NEW_REPO_NAME.git
cd NEW_REPO_NAME
```

### 2. Database Setup
1. Create a database named `ccsa_db` in MySQL:
   ```sql
   CREATE DATABASE ccsa_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
2. Import the schema and seed data from `src/database.sql`:
   ```bash
   mysql -u root -p ccsa_db < src/database.sql
   ```
   *(Or import `src/database.sql` through phpMyAdmin).*

### 3. Configure Database Credentials
Check `src/includes/config.php` and `src/admin/config.php`:
```php
// Local Development Defaults
define('DB_HOST', 'localhost');
define('DB_NAME', 'ccsa_db');
define('DB_USER', 'root');
define('DB_PASS', '');
```
*(Environment variables `DB_HOST`, `DB_NAME`, `DB_USER`, and `DB_PASS` can also be used).*

### 4. Set Directory Permissions (Linux / macOS / Production)
Make sure the upload and cache directories are writable:
```bash
chmod -R 755 src/admin/uploads/
chmod -R 755 src/cache/
```

### 5. Run the Local Development Server
From the root of the repository:
```bash
php -S localhost:8000 -t src
```

Open your browser and navigate to:
- **Public Website:** [http://localhost:8000](http://localhost:8000)
- **Admin Suite:** [http://localhost:8000/admin](http://localhost:8000/admin)

---

## 🔐 Default Admin Access

After importing `src/database.sql`, default admin credentials are:
- **Email:** `admin@ccsdu.in`
- **Password:** `Admin@12345` *(Change this immediately upon first login)*

---

## 🌐 Production Deployment Notes

1. **Hostinger / cPanel Shared Hosting:**
   - Upload the contents of `src/` into your `public_html` directory (or point the document root to `src/`).
   - Ensure `.htaccess` is uploaded to enable URL rewrites (`mod_rewrite`).
   - Import `database.sql` into your hosting MySQL database.
   - Update `src/includes/config.php` with production database credentials.

2. **Security Features Configured:**
   - CSRF protection on forms.
   - Session timeout protection (30 minutes idle timeout).
   - Rate limiting on authentication endpoints (5 failed attempts max).
   - Strict file upload checks (MIME verification, random file hashing, execution prevention in `/uploads`).
   - Production HTTP Security Headers: `X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy`.

---

## 🏛️ Attribution & Contact

- **Institution:** Centre for Computer Science and Applications (CCSA), Dibrugarh University
- **Official University Portal:** [https://dibru.ac.in](https://dibru.ac.in)
- **Official Department Portal:** [https://ccsdu.in](https://ccsdu.in)
