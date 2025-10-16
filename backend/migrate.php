<?php
require_once __DIR__ . '/vendor/autoload.php';

use Core\Database;

$pdo = (new Database())->getConnection();
$migrationFiles = glob(__DIR__ . '/migrations/*.php');
$mode = $argv[1] ?? 'up';

foreach ($migrationFiles as $file) {
    // ❌ KHÔNG cần require_once ở đây nữa
    $className = 'Migrations\\' . basename($file, '.php');

    if (!class_exists($className)) {
        echo "⚠️  Migration class not found: {$className}\n";
        continue;
    }

    $migration = new $className();

    if ($mode === 'down') {
        echo "🧹 Rolling back: {$className}\n";
        if (method_exists($migration, 'down')) {
            $migration->down($pdo);
        }
    } else {
        echo "🚀 Running: {$className}\n";
        if (method_exists($migration, 'up')) {
            $migration->up($pdo);
        }
    }
}

echo $mode === 'down'
    ? "🗑️  Rollback completed.\n"
    : "🎉 All migrations executed successfully!\n";