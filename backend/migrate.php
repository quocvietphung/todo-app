<?php
/**
 * Migration Runner Script
 * ---------------------------------------------------------
 * This script executes all migration files located in the `/migrations` directory.
 * It can run migrations (`up`) or roll them back (`down`), depending on the command argument.
 *
 * Usage:
 *   php migrate.php              → runs all migrations (default: up)
 *   php migrate.php down         → rolls back all migrations
 *
 * Dependencies:
 * - Uses the Core\Container for dependency injection
 * - Retrieves the PDO connection through the Database service
 */

require_once __DIR__ . '/vendor/autoload.php';

use Core\Container;
use Core\Database;

// Initialize the global container (Singleton)
$container = Container::getInstance();

// Retrieve the PDO connection via the Container (auto-injected Database instance)
$pdo = $container->get(PDO::class);

// Scan all migration files in the migrations directory (excluding interfaces)
$migrationFiles = array_filter(
    glob(__DIR__ . '/migrations/*.php'),
    fn($f) => stripos($f, 'Interface') === false
);

// Determine execution mode: "up" (default) or "down"
$mode = $argv[1] ?? 'up';

foreach ($migrationFiles as $file) {
    $baseName = pathinfo($file, PATHINFO_FILENAME);
    $parts = explode('_', $baseName, 2);
    $className = 'Migrations\\' . ($parts[1] ?? $parts[0]);

    echo "Checking migration: {$className}\n";

    // Ensure the migration class exists (autoloaded)
    if (!class_exists($className)) {
        echo "Warning: Migration class not found: {$className}\n";
        continue;
    }

    // Instantiate the migration class
    $migration = new $className();

    // Execute migration based on the selected mode
    if ($mode === 'down') {
        echo "Rolling back: {$className}\n";
        $migration->down($pdo);
    } else {
        echo "Running: {$className}\n";
        $migration->up($pdo);
    }

    echo "-----------------------------------------\n";
}

echo $mode === 'down'
    ? "Rollback completed.\n"
    : "All migrations executed successfully.\n";