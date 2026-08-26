<?php
declare(strict_types=1);

/**
 * Admin Configuration Bridge
 * Connects to the shared Database singleton and provides $pdo for legacy admin files.
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/Session.php';

Session::start();

try {
    $pdo = Database::getInstance();
    
    // 1. Auto-create admin_users table if not present
    $pdo->exec("CREATE TABLE IF NOT EXISTS admin_users (
        id int(11) NOT NULL AUTO_INCREMENT,
        name varchar(100) NOT NULL,
        email varchar(150) NOT NULL,
        password varchar(255) NOT NULL,
        role enum('super_admin','admin','editor') NOT NULL DEFAULT 'admin',
        status enum('active','inactive') NOT NULL DEFAULT 'active',
        last_login datetime DEFAULT NULL,
        createdAt datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updatedAt datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY idx_email (email)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // 2. Ensure default Super Admin exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM admin_users WHERE id = 1 OR email = 'ccsduoffice@gmail.com'");
    $stmt->execute();
    if ((int)$stmt->fetchColumn() === 0) {
        $pdo->exec("INSERT IGNORE INTO admin_users (id, name, email, password, role, status, createdAt) VALUES 
        (1, 'CCSA Super Administrator', 'ccsduoffice@gmail.com', '$2y$12\$amJ/rdLNDOcdL3CpYXMv9OSSE1rCjCAE/.jUG6JURCjh/u0Wo8TBW', 'super_admin', 'active', NOW())");
    }

    // 3. AUTO-SYNC: Migrate and sync any professor accounts & passwords from JSON cache into MySQL
    $cacheUsersFile = __DIR__ . '/../cache/admin_users.json';
    if (file_exists($cacheUsersFile)) {
        $cachedUsers = json_decode((string)file_get_contents($cacheUsersFile), true);
        if (is_array($cachedUsers)) {
            $syncUserStmt = $pdo->prepare("INSERT INTO admin_users (name, email, password, role, status, createdAt) 
                VALUES (?, ?, ?, ?, ?, ?) 
                ON DUPLICATE KEY UPDATE password = VALUES(password), role = VALUES(role), status = VALUES(status)");
            foreach ($cachedUsers as $cu) {
                if (!empty($cu['email']) && !empty($cu['password'])) {
                    $syncUserStmt->execute([
                        $cu['name'] ?? 'Administrator',
                        $cu['email'],
                        $cu['password'],
                        $cu['role'] ?? 'admin',
                        $cu['status'] ?? 'active',
                        $cu['createdAt'] ?? date('Y-m-d H:i:s')
                    ]);
                }
            }
        }
    }

    // 4. AUTO-SYNC: Migrate and sync any notices from JSON cache into MySQL
    $cacheNoticesFile = __DIR__ . '/../cache/local_notifications.json';
    if (file_exists($cacheNoticesFile)) {
        $cachedNotices = json_decode((string)file_get_contents($cacheNoticesFile), true);
        if (is_array($cachedNotices)) {
            $checkNoticeStmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE title = ?");
            $insertNoticeStmt = $pdo->prepare("INSERT INTO notifications (title, description, category, creator_email, file_path, file_url, is_pinned, is_new, new_until, createdAt, updatedAt) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            foreach ($cachedNotices as $cn) {
                if (!empty($cn['title'])) {
                    $checkNoticeStmt->execute([$cn['title']]);
                    if ((int)$checkNoticeStmt->fetchColumn() === 0) {
                        $insertNoticeStmt->execute([
                            $cn['title'],
                            $cn['description'] ?? '',
                            $cn['category'] ?? 'General',
                            $cn['creator_email'] ?? 'admin@ccsdu.in',
                            $cn['file_path'] ?? null,
                            $cn['file_url'] ?? null,
                            !empty($cn['is_pinned']) ? 1 : 0,
                            !empty($cn['is_new']) ? 1 : 0,
                            $cn['new_until'] ?? null,
                            $cn['createdAt'] ?? date('Y-m-d H:i:s'),
                            $cn['updatedAt'] ?? date('Y-m-d H:i:s')
                        ]);
                    }
                }
            }
        }
    }
} catch (Throwable $e) {
    error_log('Admin database initialization failed: ' . $e->getMessage());
    $pdo = null;
}
