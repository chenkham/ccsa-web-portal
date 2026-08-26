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
    
    // Auto-create admin_users table if not present
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

    // Ensure default Super Admin exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM admin_users WHERE id = 1 OR email = 'ccsduoffice@gmail.com'");
    $stmt->execute();
    if ((int)$stmt->fetchColumn() === 0) {
        $pdo->exec("INSERT IGNORE INTO admin_users (id, name, email, password, role, status, createdAt) VALUES 
        (1, 'CCSA Super Administrator', 'ccsduoffice@gmail.com', '$2y$12\$amJ/rdLNDOcdL3CpYXMv9OSSE1rCjCAE/.jUG6JURCjh/u0Wo8TBW', 'super_admin', 'active', NOW())");
    }
} catch (Throwable $e) {
    error_log('Admin database initialization failed: ' . $e->getMessage());
    $pdo = null;
}
