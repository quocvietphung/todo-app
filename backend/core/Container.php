<?php
namespace Core;

use ReflectionClass;
use ReflectionParameter;
use PDO;

/**
 * Class: Container
 * ---------------------------------------------------------
 * A lightweight Dependency Injection (DI) container implemented as a Singleton.
 * This class automatically creates and manages class instances using PHP Reflection.
 * Dependencies are recursively resolved and injected without manual instantiation.
 *
 * Responsibilities:
 * - Resolves class dependencies automatically via Reflection.
 * - Caches instantiated objects for reuse (local singleton).
 * - Automatically injects a PDO connection from {@see Database::getConnection()}.
 *
 * @package Core
 */
class Container
{
    /**
     * @var self|null  Singleton instance of the container.
     */
    private static ?self $instance = null;

    /**
     * @var array  Cache for already created instances.
     */
    private array $instances = [];

    /**
     * Private constructor to prevent direct instantiation.
     */
    private function __construct() {}

    /**
     * Returns the singleton instance of the container.
     *
     * @return self  The global container instance.
     */
    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }

    /**
     * Returns an instance of the requested class and automatically injects its dependencies.
     *
     * If an instance already exists, it is retrieved from the cache.
     * If the class requires a PDO type, a shared SQLite connection is automatically injected
     * via {@see Database::getConnection()}.
     *
     * @param string $class  Fully qualified class name (e.g., Controller\TodoController).
     *
     * @return object  A fully initialized instance of the given class.
     * @throws \ReflectionException  If the class does not exist or cannot be reflected.
     */
    public function get(string $class)
    {
        // Special case: if PDO is required → return the shared database connection.
        if ($class === PDO::class) {
            return Database::getConnection();
        }

        // If the instance already exists → return it from cache.
        if (isset($this->instances[$class])) {
            return $this->instances[$class];
        }

        // Reflection: analyze constructor and resolve dependencies recursively.
        $reflection = new ReflectionClass($class);
        $constructor = $reflection->getConstructor();

        if (!$constructor) {
            // Class has no constructor → create it directly.
            $object = new $class();
        } else {
            // Resolve all constructor parameters (dependencies) recursively.
            $dependencies = array_map(function (ReflectionParameter $param) {
                $type = $param->getType();
                if ($type && !$type->isBuiltin()) {
                    return $this->get($type->getName());
                }
                return null;
            }, $constructor->getParameters());

            $dependencies = array_filter($dependencies); // Remove null values.
            $object = $reflection->newInstanceArgs($dependencies);
        }

        // Store the created instance in cache (local singleton).
        return $this->instances[$class] = $object;
    }
}