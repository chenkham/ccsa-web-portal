# First-Time Setup & Installation Guide
### CCSA Dibrugarh University Web Portal

Follow these step-by-step instructions to set up, configure, and run the project locally on your machine or deploy it to a web server.

---

## 📋 System Requirements

Before starting, ensure you have the following installed:

1. **PHP:** Version `8.1` or higher.
   - Required PHP Extensions: `pdo_mysql`, `curl`, `mbstring`, `fileinfo`, `openssl`, `json`.
2. **Database:** MySQL `5.7+` / MySQL `8.0+` or MariaDB `10.4+`.
3. **Web Server (Optional for local testing):** Apache with `mod_rewrite` / Nginx or PHP's built-in CLI server.
4. **Git:** For repository cloning and version control.

---

## 🚀 Step 1: Clone the Repository

Clone the project to your local machine using Git:

```bash
git clone https://github.com/chenkham/ccsa-web-portal.git
cd ccsa-web-portal
```

---

## 🗄️ Step 2: Database Setup & Migration

1. Open your MySQL client (e.g. **phpMyAdmin**, **MySQL Workbench**, or the command line).
2. Create a new database named `ccsa_db`:
   ```sql
   CREATE DATABASE ccsa_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
3. Import the database schema and default records from `src/database.sql`:
   - **Using Command Line:**
     ```bash
     mysql -u root -p ccsa_db < src/database.sql
     ```
   - **Using phpMyAdmin:**
     - Select `ccsa_db` from the left sidebar.
     - Click the **Import** tab.
     - Choose file: `src/database.sql` and click **Go**.

---

## ⚙️ Step 3: Configure Database Credentials

Open `src/admin/config.php` in your code editor and verify your database connection settings:

```php
// src/admin/config.php
$dbHost = 'localhost';
$dbName = 'ccsa_db';
$dbUser = 'root';        // Your MySQL username
$dbPass = '';            // Your MySQL password
```

---

## 🔒 Step 4: Configure Folder Permissions

Ensure that the web server has write permissions to the upload and cache directories:

- `src/admin/uploads/notification_docs/` (Used for notice PDF uploads)
- `src/cache/` (Used for proxy caching)

**On Linux / macOS:**
```bash
chmod -R 755 src/admin/uploads/
chmod -R 755 src/cache/
```

**On Windows (XAMPP/WAMP/Laragon):**
Windows standard user permissions are typically sufficient by default.

---

## 💻 Step 5: Run the Project Locally

### Option A: Using PHP Built-In Server (Recommended for Quick Start)

Run the following command from the root project directory:

```bash
php -S localhost:8000 -t src
```

Open your browser and visit:
- **Public Portal:** [http://localhost:8000](http://localhost:8000)
- **Admin Suite:** [http://localhost:8000/admin](http://localhost:8000/admin)

---

### Option B: Using XAMPP / WAMP / Laragon

1. Copy or move the `src/` folder contents into your webroot:
   - **XAMPP:** `C:\xampp\htdocs\ccsa\`
   - **Laragon:** `C:\laragon\www\ccsa\`
   - **WampServer:** `C:\wamp64\www\ccsa\`
2. Start **Apache** and **MySQL** from your control panel.
3. Access the portal at: `http://localhost/ccsa/`

---

## 🔑 Step 6: Default Admin Login

To log in to the administrative control panel:

- **Admin Login URL:** `http://localhost:8000/admin/login.php`
- **Default Email:** `admin@ccsdu.in`
- **Default Password:** `admin123`

> [!IMPORTANT]
> **Production Security Note:** After logging in for the first time, immediately navigate to the **Administrators** tab and update the default password.

---

## 🧪 Step 7: Verifying Key Modules

1. **Faculty Directory ([/faculty.php](http://localhost:8000/faculty.php)):**
   - Automatically loads live teaching faculty from the Dibrugarh University central server.
   - If offline, gracefully falls back to the local database roster.
2. **Notice Board & Urgent Pinning ([/notices.php](http://localhost:8000/notices.php)):**
   - Test publishing a new notice from the admin panel and toggling the **Pin to Top** checkbox.
3. **Spotlight Search:**
   - Press **`Ctrl + K`** (or click the search button in the top bar) and type a professor's name or a program keyword (*e.g. BCA, Rizwan, Generative AI*).
4. **Offline Course Printing:**
   - Open any course page ([BCA](http://localhost:8000/undergraduate.php), [MCA](http://localhost:8000/postgraduate.php)) and click **Print / Save PDF**.

---

## ❓ Troubleshooting & Common Issues

| Issue | Cause | Solution |
| :--- | :--- | :--- |
| **"Database Connection Failed"** | Incorrect MySQL credentials in `src/admin/config.php`. | Check `$dbUser`, `$dbPass`, and ensure the MySQL server service is running. |
| **"Upload Failed / Permission Denied"** | Upload directory lacks write permissions. | Ensure `src/admin/uploads/notification_docs/` has `755` permissions. |
| **Faculty list shows "Loading..." or Error** | Outgoing cURL requests blocked by firewall. | Verify that PHP has `extension=curl` enabled in `php.ini`. |
| **Admin login shows "Too many failed attempts"** | Brute-force rate limiter triggered. | Wait 15 minutes or clear the failed login records from the `audit_logs` table. |

---

## 📞 Support & Inquiries

For technical assistance or reporting issues:
- **Email:** chenkhamchowlu@gmail.com
- **Managed By:** Chenkham
