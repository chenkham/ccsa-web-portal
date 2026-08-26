<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

/**
 * Database Connection Manager (PDO Singleton)
 */
class Database
{
    private static ?\PDO $instance = null;

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    /**
     * Returns the active PDO database instance.
     *
     * @throws \RuntimeException If the connection cannot be established
     */
    public static function getInstance(): \PDO
    {
        if (self::$instance === null) {
            $options = [
                \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES   => false,
                \PDO::ATTR_TIMEOUT            => 1,
            ];

            $attempts = [
                ['host' => 'localhost', 'name' => 'u332237642_ccs', 'user' => 'u332237642_ccs', 'pass' => '786004@Ccs'],
                ['host' => 'localhost', 'name' => 'u332237642_ccs', 'user' => 'u332237642_ccs', 'pass' => '786004@ccs'],
                ['host' => 'localhost', 'name' => 'u332237642_ccs', 'user' => 'u332237642_ccsa', 'pass' => '786004@Ccs'],
                ['host' => 'localhost', 'name' => 'u332237642_ccs', 'user' => 'u332237642_ccsa', 'pass' => '786004@ccs'],
                ['host' => 'localhost', 'name' => 'u332237642_ccsa', 'user' => 'u332237642_ccsa', 'pass' => '786004@Ccs'],
                ['host' => 'localhost', 'name' => 'u332237642_ccsa', 'user' => 'u332237642_ccs', 'pass' => '786004@Ccs'],
                ['host' => DB_HOST, 'name' => DB_NAME, 'user' => DB_USER, 'pass' => DB_PASS]
            ];

            $lastError = null;
            $allErrors = [];
            foreach ($attempts as $conn) {
                try {
                    $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', $conn['host'], $conn['name'], DB_CHARSET);
                    self::$instance = new \PDO($dsn, $conn['user'], $conn['pass'], $options);
                    break;
                } catch (\PDOException $e) {
                    $lastError = $e->getMessage();
                    $allErrors[] = "User '{$conn['user']}' on '{$conn['name']}': {$e->getMessage()}";
                }
            }

            if (self::$instance === null) {
                error_log('Database connection error: ' . $lastError);
                throw new \RuntimeException('Database connection failed: ' . $lastError . ' | Details: ' . implode('; ', $allErrors));
            }
        }

        return self::$instance;
    }

    /**
     * Begins a transaction.
     */
    public static function beginTransaction(): bool
    {
        return self::getInstance()->beginTransaction();
    }

    /**
     * Commits the active transaction.
     */
    public static function commit(): bool
    {
        return self::getInstance()->commit();
    }

    /**
     * Rolls back the active transaction.
     */
    public static function rollBack(): bool
    {
        return self::getInstance()->rollBack();
    }
}
