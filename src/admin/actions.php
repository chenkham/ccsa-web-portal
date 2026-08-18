<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/security-validator.php';
require_once __DIR__ . '/check_session.php';
require_once __DIR__ . '/../includes/Security.php';

check_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$csrf_token = $_POST['csrf_token'] ?? '';
if (!SecurityValidator::validateCSRFToken($csrf_token)) {
    header('Location: index.php?error=' . urlencode('Security token mismatch. Please refresh and try again.'));
    exit;
}

$action = (string)($_POST['action'] ?? '');
$userEmail = (string)($_SESSION['user_email'] ?? 'admin@ccsdu.in');
$userRole = (string)($_SESSION['user_role'] ?? 'admin');

function require_role(string $currentRole, array $allowedRoles, string $redirectTab = 'notifications'): void {
    if (!in_array($currentRole, $allowedRoles, true)) {
        header('Location: index.php?error=' . urlencode('Access Denied: Your role (' . htmlspecialchars($currentRole) . ') does not have permission for this operation.') . '&tab=' . urlencode($redirectTab));
        exit;
    }
}

if ($action === 'add_notification') {
    require_role($userRole, ['super_admin', 'admin', 'editor'], 'notifications');

    $title = trim((string)($_POST['title'] ?? ''));
    $description = trim((string)($_POST['description'] ?? ''));
    $file_url = trim((string)($_POST['file_url'] ?? ''));
    $file_path = null;

    if (empty($title)) {
        header('Location: index.php?error=' . urlencode('Notification title is required.') . '&tab=notifications');
        exit;
    }

    if (isset($_FILES['notice_file']) && $_FILES['notice_file']['error'] !== UPLOAD_ERR_NO_FILE) {
        $validation = Security::validateUploadedFile(
            $_FILES['notice_file'],
            ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'],
            [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'image/jpeg',
                'image/png'
            ],
            15728640
        );

        if (!$validation['valid']) {
            header('Location: index.php?error=' . urlencode($validation['error'] ?? 'Invalid file upload.') . '&tab=notifications');
            exit;
        }

        $uploadDir = __DIR__ . '/uploads/notification_docs/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $origName = pathinfo($_FILES['notice_file']['name'], PATHINFO_FILENAME);
        $safeBase = Security::sanitizeFilename($origName);
        $ext = $validation['ext'];
        $uniqueFilename = bin2hex(random_bytes(8)) . '_' . substr($safeBase, 0, 40) . '.' . $ext;
        $destPath = $uploadDir . $uniqueFilename;

        if (move_uploaded_file($_FILES['notice_file']['tmp_name'], $destPath)) {
            $file_path = 'uploads/notification_docs/' . $uniqueFilename;
        } else {
            header('Location: index.php?error=' . urlencode('Failed to save uploaded document on server.') . '&tab=notifications');
            exit;
        }
    }

    $is_pinned = isset($_POST['is_pinned']) ? 1 : 0;

    if (isset($pdo) && $pdo !== null) {
        try {
            $stmt = $pdo->prepare('INSERT INTO notifications (title, description, is_pinned, creator_email, file_path, file_url, createdAt, updatedAt) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())');
            $stmt->execute([$title, $description, $is_pinned, $userEmail, $file_path, $file_url]);
            Security::logAudit($pdo, 'NOTICE_PUBLISHED', 'Published announcement: ' . substr($title, 0, 80), $userEmail);
        } catch (Throwable $e) {
            error_log('Notification DB insert failed: ' . $e->getMessage());
        }
    }

    header('Location: index.php?success=' . urlencode('Announcement published successfully!') . '&tab=notifications');
    exit;
}

if ($action === 'delete_notification') {
    require_role($userRole, ['super_admin', 'admin', 'editor'], 'notifications');

    $id = (int)($_POST['id'] ?? 0);
    $filePath = (string)($_POST['file_path'] ?? '');

    if ($id > 0 && isset($pdo) && $pdo !== null) {
        try {
            $stmt = $pdo->prepare('DELETE FROM notifications WHERE id = ?');
            $stmt->execute([$id]);
            Security::logAudit($pdo, 'NOTICE_DELETED', "Removed notification ID #$id", $userEmail);
        } catch (Throwable $e) {
            error_log('Notification delete failed: ' . $e->getMessage());
        }
    }

    if (!empty($filePath) && str_starts_with($filePath, 'uploads/notification_docs/')) {
        $safePath = Security::sanitizeFilename(basename($filePath));
        $fullPath = __DIR__ . '/uploads/notification_docs/' . $safePath;
        if (file_exists($fullPath)) {
            @unlink($fullPath);
        }
    }

    header('Location: index.php?success=' . urlencode('Notification deleted successfully.') . '&tab=notifications');
    exit;
}

if ($action === 'add_admin') {
    require_role($userRole, ['super_admin'], 'admin');

    $name = trim((string)($_POST['name'] ?? ''));
    $email = trim((string)($_POST['email'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    $role = (string)($_POST['role'] ?? 'admin');

    if (!in_array($role, ['super_admin', 'admin', 'editor'], true)) {
        $role = 'admin';
    }

    if (empty($name) || empty($email) || empty($password)) {
        header('Location: index.php?error=' . urlencode('All administrator fields are required.') . '&tab=admin');
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: index.php?error=' . urlencode('Please provide a valid email address.') . '&tab=admin');
        exit;
    }

    if (strlen($password) < 8) {
        header('Location: index.php?error=' . urlencode('Administrator password must be at least 8 characters long.') . '&tab=admin');
        exit;
    }

    $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

    if (isset($pdo) && $pdo !== null) {
        try {
            $stmt = $pdo->prepare('INSERT INTO admin_users (name, email, password, role, status, createdAt) VALUES (?, ?, ?, ?, "active", NOW())');
            $stmt->execute([$name, $email, $hash, $role]);
            Security::logAudit($pdo, 'ADMIN_CREATED', "Created new $role account: $email ($name)", $userEmail);
        } catch (Throwable $e) {
            error_log('Admin insert failed: ' . $e->getMessage());
            header('Location: index.php?error=' . urlencode('Email address is already registered in the administrator directory.') . '&tab=admin');
            exit;
        }
    }

    header('Location: index.php?success=' . urlencode('Administrator user registered successfully!') . '&tab=admin');
    exit;
}

if ($action === 'delete_admin') {
    require_role($userRole, ['super_admin'], 'admin');

    $id = (int)($_POST['id'] ?? 0);
    if ($id === 1) {
        header('Location: index.php?error=' . urlencode('Primary Super Administrator (ID #1) cannot be deleted.') . '&tab=admin');
        exit;
    }

    if ($id === (int)($_SESSION['user_id'] ?? 0)) {
        header('Location: index.php?error=' . urlencode('You cannot delete your own active administrator account.') . '&tab=admin');
        exit;
    }

    if ($id > 0 && isset($pdo) && $pdo !== null) {
        try {
            $stmt = $pdo->prepare('DELETE FROM admin_users WHERE id = ?');
            $stmt->execute([$id]);
            Security::logAudit($pdo, 'ADMIN_DELETED', "Deleted administrator account ID #$id", $userEmail);
        } catch (Throwable $e) {
            error_log('Admin delete failed: ' . $e->getMessage());
        }
    }
    header('Location: index.php?success=' . urlencode('Administrator account removed.') . '&tab=admin');
    exit;
}

if ($action === 'delete_message') {
    require_role($userRole, ['super_admin', 'admin'], 'messages');

    $id = (int)($_POST['id'] ?? 0);
    if ($id > 0 && isset($pdo) && $pdo !== null) {
        try {
            $stmt = $pdo->prepare('DELETE FROM contact_messages WHERE id = ?');
            $stmt->execute([$id]);
            Security::logAudit($pdo, 'MESSAGE_DELETED', "Deleted contact inquiry message ID #$id", $userEmail);
        } catch (Throwable $e) {
            error_log('Message delete failed: ' . $e->getMessage());
        }
    }

    $fallbackFile = __DIR__ . '/../cache/messages_fallback.json';
    if (file_exists($fallbackFile)) {
        $fallbackMessages = json_decode((string)file_get_contents($fallbackFile), true) ?: [];
        $fallbackMessages = array_filter($fallbackMessages, function($m) use ($id) {
            return (int)($m['id'] ?? 0) !== $id;
        });
        file_put_contents($fallbackFile, json_encode(array_values($fallbackMessages), JSON_PRETTY_PRINT));
    }

    header('Location: index.php?success=' . urlencode('Contact inquiry deleted.') . '&tab=messages');
    exit;
}

header('Location: index.php');
exit;
