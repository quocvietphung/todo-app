<?php
namespace Core;

use PDO;

/**
 * ✅ Database Singleton
 * - Chỉ tạo 1 kết nối SQLite cho toàn app
 * - Dùng chung connection trong mọi repository
 */
class Database
{
    private static ?PDO $connection = null;

    public static function getConnection(): PDO
    {
        if (!self::$connection) {
            $path = __DIR__ . '/../db/todos.db';
            $dsn = 'sqlite:' . $path;
            self::$connection = new PDO($dsn);
            self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        return self::$connection;
    }

    // 🚫 Không cho new
    private function __construct() {}
}