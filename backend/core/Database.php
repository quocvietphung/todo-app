<?php
namespace Core;

use PDO;

/**
 * Class: Database
 * ---------------------------------------------------------
 * Manages a single SQLite database connection (Singleton).
 *
 * This class ensures that only one PDO connection is created
 * and reused throughout the entire application.
 * It serves as the central data source for all repository classes.
 *
 * Responsibilities:
 * - Automatically creates a file-based SQLite connection.
 * - Enables PDO exception mode for error handling.
 * - Prevents multiple instantiations through a private constructor.
 *
 * @package Core
 */
class Database
{
    /**
     * @var PDO|null  Singleton instance of the PDO connection.
     */
    private static ?PDO $connection = null;

    /**
     * Returns the singleton instance of the PDO connection.
     *
     * If no connection exists, a new SQLite database file
     * will be automatically created in `/db/todos.db`.
     *
     * @return PDO  An active SQLite PDO connection.
     * @throws \PDOException  If establishing the connection fails.
     */
    public static function getConnection(): PDO
    {
        if (!self::$connection) {
            $path = __DIR__ . '/../db/todos.db';
            $dsn = 'sqlite:' . $path;

            // Establish a connection to the SQLite database file
            self::$connection = new PDO($dsn);

            // Configure PDO to throw exceptions on errors
            self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        return self::$connection;
    }

    /**
     * Private constructor prevents direct instantiation.
     *
     * The class should only be accessed through {@see Database::getConnection()}.
     */
    private function __construct() {}
}