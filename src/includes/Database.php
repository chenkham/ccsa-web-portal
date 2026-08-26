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
            $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', DB_HOST, DB_NAME, DB_CHARSET);

            $options = [
                \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$instance = new \PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (\PDOException $e) {
                error_log('Database connection error: ' . $e->getMessage());
                throw new \RuntimeException('Database connection failed.');
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
