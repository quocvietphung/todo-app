<?php
namespace Core;

class Database
{
    private static ?\PDO $instance = null;
    private string $dbPath;

    public function __construct(string $dbPath = __DIR__ . '/../db/todos.db')
    {
        // 📦 Đường dẫn file SQLite (có thể thay bằng tên khác nếu cần)
        $this->dbPath = $dbPath;

        // 🧱 Nếu thư mục db chưa tồn tại thì tự động tạo
        $dir = dirname($this->dbPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
    }

    public function getConnection(): \PDO
    {
        if (self::$instance === null) {
            // 🔗 Tạo kết nối SQLite (file-based)
            self::$instance = new \PDO('sqlite:' . $this->dbPath);
            self::$instance->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }

        return self::$instance;
    }
}