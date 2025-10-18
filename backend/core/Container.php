<?php
namespace Core;

use ReflectionClass;
use ReflectionParameter;

/**
 * ✅ Lightweight Dependency Injection Container (Singleton)
 * - Tự động tạo & inject các dependency qua Reflection
 * - Lưu cache các instance đã tạo (singleton cục bộ)
 */
class Container
{
    private static ?self $instance = null;
    private array $instances = [];

    private function __construct() {}

    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }

    public function get(string $class)
    {
        if (!isset($this->instances[$class])) {
            $reflection = new ReflectionClass($class);
            $constructor = $reflection->getConstructor();

            if (!$constructor) {
                $this->instances[$class] = new $class();
            } else {
                $dependencies = array_map(function (ReflectionParameter $param) {
                    $type = $param->getType();
                    if ($type && !$type->isBuiltin()) {
                        return $this->get($type->getName());
                    }
                    return null;
                }, $constructor->getParameters());

                $dependencies = array_filter($dependencies); // loại null
                $this->instances[$class] = $reflection->newInstanceArgs($dependencies);
            }
        }

        return $this->instances[$class];
    }
}