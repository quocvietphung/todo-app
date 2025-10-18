<?php
namespace Core;

use ReflectionClass;
use ReflectionParameter;
use PDO;

/**
 * ✅ Lightweight Dependency Injection Container (Singleton)
 * ---------------------------------------------------------
 * - Tự động tạo & inject dependency qua Reflection
 * - Cache instance để dùng lại (singleton cục bộ)
 * - Tự động inject PDO (dựa trên Database::getConnection)
 */
class Container
{
    private static ?self $instance = null;
    private array $instances = [];

    private function __construct() {}

    /** 🔁 Singleton accessor */
    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }

    /**
     * 🧩 Lấy hoặc tạo instance của class (kèm auto dependency injection)
     */
    public function get(string $class)
    {
        // ✅ Trường hợp đặc biệt: Nếu cần PDO → lấy từ Database::getConnection()
        if ($class === PDO::class) {
            return Database::getConnection();
        }

        // Nếu đã có instance → dùng lại
        if (isset($this->instances[$class])) {
            return $this->instances[$class];
        }

        // Reflection: đọc constructor và các dependency
        $reflection = new ReflectionClass($class);
        $constructor = $reflection->getConstructor();

        if (!$constructor) {
            // Class không có dependency
            $object = new $class();
        } else {
            // Lấy danh sách dependency và tạo chúng
            $dependencies = array_map(function (ReflectionParameter $param) {
                $type = $param->getType();
                if ($type && !$type->isBuiltin()) {
                    return $this->get($type->getName());
                }
                return null;
            }, $constructor->getParameters());

            $dependencies = array_filter($dependencies); // loại null
            $object = $reflection->newInstanceArgs($dependencies);
        }

        // Lưu vào cache (singleton cục bộ)
        return $this->instances[$class] = $object;
    }
}