<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/security-validator.php';
require_once __DIR__ . '/check_session.php';
require_once __DIR__ . '/../includes/Security.php';

/**
 * CCSA Admin Action Dispatcher
 * 
 * "Welcome to the Grand Central Station of admin actions.
 *  If this file breaks, please blame caffeine deprivation, not the architecture."
 */

check_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // GET requests here? Nice try, crawler bot.
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
        // "You shall not pass!" - Gandalf, and this RBAC middleware
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
    $show_new = isset($_POST['show_new']) ? 1 : 0;
    $new_until = null;
    if ($show_new) {
        $rawUntil = trim((string)($_POST['new_until'] ?? ''));
        if (!empty($rawUntil) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $rawUntil)) {
            $new_until = $rawUntil;
        } else {
            $new_until = date('Y-m-d', strtotime('+15 days'));
        }
    }

    if (isset($pdo) && $pdo !== null) {
        try {
            $stmt = $pdo->prepare('INSERT INTO notifications (title, description, is_pinned, is_new, new_until, creator_email, file_path, file_url, createdAt, updatedAt) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())');
            $stmt->execute([$title, $description, $is_pinned, $show_new, $new_until, $userEmail, $file_path, $file_url]);
            Security::logAudit($pdo, 'NOTICE_PUBLISHED', 'Published announcement: ' . substr($title, 0, 80), $userEmail);
        } catch (Throwable $e) {
            error_log('Notification DB insert failed: ' . $e->getMessage());
        }
    }

    $cacheDir = __DIR__ . '/../cache';
    if (!is_dir($cacheDir)) {
        mkdir($cacheDir, 0755, true);
    }
    $localNoticeFile = $cacheDir . '/local_notifications.json';
    $localNotices = file_exists($localNoticeFile) ? (json_decode((string)file_get_contents($localNoticeFile), true) ?: []) : [];

    $newNotice = [
        'id' => time() . rand(100, 999),
        'title' => $title,
        'description' => $description,
        'is_pinned' => $is_pinned,
        'is_new' => $show_new,
        'new_until' => $new_until,
        'creator_email' => $userEmail,
        'file_path' => $file_path,
        'file_url' => $file_url,
        'createdAt' => date('Y-m-d H:i:s'),
        'updatedAt' => date('Y-m-d H:i:s')
    ];
    array_unshift($localNotices, $newNotice);
    file_put_contents($localNoticeFile, json_encode(array_values($localNotices), JSON_PRETTY_PRINT));

    header('Location: index.php?success=' . urlencode('Announcement published successfully!') . '&tab=notifications');
    exit;
}

if ($action === 'edit_notification') {
    require_role($userRole, ['super_admin', 'admin', 'editor'], 'notifications');

    $id = $_POST['id'] ?? '';
    $title = trim((string)($_POST['title'] ?? ''));
    $description = trim((string)($_POST['description'] ?? ''));
    $is_pinned = isset($_POST['is_pinned']) ? 1 : 0;
    $show_new = isset($_POST['show_new']) ? 1 : 0;
    $new_until = null;
    if ($show_new) {
        $rawUntil = trim((string)($_POST['new_until'] ?? ''));
        if (!empty($rawUntil) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $rawUntil)) {
            $new_until = $rawUntil;
        } else {
            $new_until = date('Y-m-d', strtotime('+15 days'));
        }
    }

    if (empty($title)) {
        header('Location: index.php?error=' . urlencode('Notice title cannot be empty.') . '&tab=notifications');
        exit;
    }

    $file_path = trim((string)($_POST['existing_file_path'] ?? ''));
    $file_url = trim((string)($_POST['file_url'] ?? ''));

    if (!empty($_FILES['notice_file']['name']) && $_FILES['notice_file']['error'] === UPLOAD_ERR_OK) {
        $validation = SecurityValidator::validateFileUpload(
            $_FILES['notice_file'],
            [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'image/jpeg',
                'image/png'
            ],
            15728640
        );

        if ($validation['valid']) {
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
            }
        }
    }

    if (isset($pdo) && $pdo !== null && is_numeric($id)) {
        try {
            $stmt = $pdo->prepare('UPDATE notifications SET title = ?, description = ?, is_pinned = ?, is_new = ?, new_until = ?, file_path = ?, file_url = ?, updatedAt = NOW() WHERE id = ?');
            $stmt->execute([$title, $description, $is_pinned, $show_new, $new_until, $file_path, $file_url, (int)$id]);
            Security::logAudit($pdo, 'NOTICE_UPDATED', 'Updated announcement: ' . substr($title, 0, 80), $userEmail);
        } catch (Throwable $e) {
            error_log('Notification DB update failed: ' . $e->getMessage());
        }
    }

    $cacheFile = __DIR__ . '/../cache/local_notifications.json';
    if (file_exists($cacheFile)) {
        $localNotices = json_decode((string)file_get_contents($cacheFile), true) ?: [];
        $matched = false;
        foreach ($localNotices as &$ln) {
            if ((string)($ln['id'] ?? '') === (string)$id || (!empty($ln['title']) && trim($ln['title']) === $title)) {
                $ln['title'] = $title;
                $ln['description'] = $description;
                $ln['is_pinned'] = $is_pinned;
                $ln['is_new'] = $show_new;
                $ln['new_until'] = $new_until;
                $ln['file_path'] = $file_path;
                $ln['file_url'] = $file_url;
                $ln['updatedAt'] = date('Y-m-d H:i:s');
                $matched = true;
                break;
            }
        }
        unset($ln);
        file_put_contents($cacheFile, json_encode(array_values($localNotices), JSON_PRETTY_PRINT));
    }

    header('Location: index.php?success=' . urlencode('Announcement updated successfully!') . '&tab=notifications');
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

    $localNoticeFile = __DIR__ . '/../cache/local_notifications.json';
    if (file_exists($localNoticeFile)) {
        $localNotices = json_decode((string)file_get_contents($localNoticeFile), true) ?: [];
        $localNotices = array_filter($localNotices, function($n) use ($id, $filePath) {
            if ((int)($n['id'] ?? 0) === $id) return false;
            if (!empty($filePath) && ($n['file_path'] ?? '') === $filePath) return false;
            return true;
        });
        file_put_contents($localNoticeFile, json_encode(array_values($localNotices), JSON_PRETTY_PRINT));
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

if ($action === 'add_notification_recipient') {
    require_role($userRole, ['super_admin', 'admin'], 'messages');

    $recipientEmail = trim((string)($_POST['recipient_email'] ?? ''));
    if (!filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
        header('Location: index.php?error=' . urlencode('Please provide a valid email address.') . '&tab=messages');
        exit;
    }

    $cacheDir = __DIR__ . '/../cache';
    if (!is_dir($cacheDir)) {
        mkdir($cacheDir, 0755, true);
    }
    $file = $cacheDir . '/notification_recipients.json';
    $list = file_exists($file) ? (json_decode((string)file_get_contents($file), true) ?: []) : [];

    if (!in_array($recipientEmail, $list, true)) {
        $list[] = $recipientEmail;
        file_put_contents($file, json_encode(array_values(array_unique($list)), JSON_PRETTY_PRINT));
        Security::logAudit($pdo, 'ALERT_RECIPIENT_ADDED', "Added inquiry alert recipient: $recipientEmail", $userEmail);
    }

    header('Location: index.php?success=' . urlencode("Added $recipientEmail to inquiry email alert list.") . '&tab=messages');
    exit;
}

if ($action === 'delete_notification_recipient') {
    require_role($userRole, ['super_admin', 'admin'], 'messages');

    $recipientEmail = trim((string)($_POST['recipient_email'] ?? ''));
    $file = __DIR__ . '/../cache/notification_recipients.json';

    if (file_exists($file)) {
        $list = json_decode((string)file_get_contents($file), true) ?: [];
        $list = array_filter($list, function($em) use ($recipientEmail) {
            return strtolower(trim((string)$em)) !== strtolower($recipientEmail);
        });
        file_put_contents($file, json_encode(array_values($list), JSON_PRETTY_PRINT));
        Security::logAudit($pdo, 'ALERT_RECIPIENT_REMOVED', "Removed inquiry alert recipient: $recipientEmail", $userEmail);
    }

    header('Location: index.php?success=' . urlencode("Removed $recipientEmail from inquiry alert list.") . '&tab=messages');
    exit;
}

if ($action === 'save_smtp_config') {
    require_role($userRole, ['super_admin', 'admin'], 'messages');

    require_once __DIR__ . '/../includes/Mailer.php';
    Mailer::saveSmtpConfig($_POST);
    Security::logAudit($pdo, 'SMTP_CONFIG_UPDATED', 'Updated outgoing SMTP server configuration', $userEmail);

    header('Location: index.php?success=' . urlencode('SMTP configuration saved successfully!') . '&tab=messages');
    exit;
}

if ($action === 'send_test_alert_email') {
    require_role($userRole, ['super_admin', 'admin'], 'messages');

    require_once __DIR__ . '/../includes/Mailer.php';
    $targetEmail = trim((string)($_POST['test_email'] ?? ''));
    if (!filter_var($targetEmail, FILTER_VALIDATE_EMAIL)) {
        header('Location: index.php?error=' . urlencode('Invalid test email address.') . '&tab=messages');
        exit;
    }

    $subject = "[CCSA Portal] Test Email Alert Verification";
    $dateStr = date('d M Y, h:i A (T)');
    $body = "This is a test notification alert from Centre for Computer Science & Applications (CCSA) portal.\n\n";
    $body .= "Timestamp : $dateStr\n";
    $body .= "Initiated : $userEmail\n";
    $body .= "Status    : Mail server configuration is working properly!\n";

    $cfg = Mailer::getSmtpConfig();
    if (!empty($cfg['host'])) {
        $res = Mailer::sendViaSmtp($targetEmail, $subject, $body);
        if ($res['success']) {
            Mailer::logDispatch($targetEmail, true, 'Test email delivered via SMTP (' . $cfg['host'] . ')');
            header('Location: index.php?success=' . urlencode("Test email successfully sent to $targetEmail via SMTP!") . '&tab=messages');
            exit;
        } else {
            Mailer::logDispatch($targetEmail, false, 'Test email failed: ' . ($res['error'] ?? 'SMTP Error'));
            header('Location: index.php?error=' . urlencode("SMTP error: " . ($res['error'] ?? 'Failed to connect')) . '&tab=messages');
            exit;
        }
    } else {
        $headers = "From: noreply@ccsdu.in\r\nContent-Type: text/plain; charset=UTF-8\r\n";
        $ok = @mail($targetEmail, $subject, $body, $headers);
        if ($ok) {
            Mailer::logDispatch($targetEmail, true, 'Test email dispatched via PHP mail()');
            header('Location: index.php?success=' . urlencode("Test email dispatched to $targetEmail via PHP mail()!") . '&tab=messages');
            exit;
        } else {
            Mailer::logDispatch($targetEmail, false, 'Test email failed: PHP mail() not configured on local server. Set up SMTP.');
            header('Location: index.php?error=' . urlencode("PHP mail() failed. On local Windows, configure SMTP settings below to send real emails.") . '&tab=messages');
            exit;
        }
    }
}

header('Location: index.php');
exit;
