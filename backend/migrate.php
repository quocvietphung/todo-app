<?php
require_once __DIR__ . '/vendor/autoload.php';

use Core\Container;
use Core\Database;

// ✅ Lấy instance container (Singleton)
$container = Container::getInstance();

// ✅ Lấy kết nối PDO thông qua Container (auto inject Database)
$pdo = $container->get(PDO::class);

// 🗂️ Quét tất cả file migration trong thư mục /migrations
$migrationFiles = array_filter(
    glob(__DIR__ . '/migrations/*.php'),
    fn($f) => stripos($f, 'Interface') === false
);

// ⚙️ Xác định chế độ chạy (up hoặc down)
$mode = $argv[1] ?? 'up';

foreach ($migrationFiles as $file) {
    $baseName = pathinfo($file, PATHINFO_FILENAME);
    $parts = explode('_', $baseName, 2);
    $className = 'Migrations\\' . ($parts[1] ?? $parts[0]);

    echo "🔍 Checking migration: {$className}\n";

    if (!class_exists($className)) {
        echo "⚠️  Migration class not found: {$className}\n";
        continue;
    }

    $migration = new $className();

    if ($mode === 'down') {
        echo "🧹 Rolling back: {$className}\n";
        $migration->down($pdo);
    } else {
        echo "🚀 Running: {$className}\n";
        $migration->up($pdo);
    }

    echo "-----------------------------------------\n";
}

echo $mode === 'down'
    ? "🗑️  Rollback completed.\n"
    : "🎉 All migrations executed successfully!\n";