<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

/**
 * PDO Singleton Database Class
 */
class Database
{
    /** @var \PDO|null */
    private static ?\PDO $instance = null;

    /**
     * Private constructor to prevent direct instantiation.
     */
    private function __construct()
    {
    }

    /**
     * Prevent cloning.
     */
    private function __clone()
    {
    }

    /**
     * Get the PDO instance.
     *
     * @return \PDO
     */
    public static function getInstance(): \PDO
    {
        if (self::$instance === null) {
            $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', DB_HOST, DB_NAME, DB_CHARSET);
            
            $options = [
                \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$instance = new \PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (\PDOException $e) {
                // In production, do not echo the exact error message
                throw new \RuntimeException('Database connection failed.');
            }
        }

        return self::$instance;
    }

    /**
     * Begin a transaction.
     *
     * @return bool
     */
    public static function beginTransaction(): bool
    {
        return self::getInstance()->beginTransaction();
    }

    /**
     * Commit a transaction.
     *
     * @return bool
     */
    public static function commit(): bool
    {
        return self::getInstance()->commit();
    }

    /**
     * Rollback a transaction.
     *
     * @return bool
     */
    public static function rollBack(): bool
    {
        return self::getInstance()->rollBack();
    }
}
