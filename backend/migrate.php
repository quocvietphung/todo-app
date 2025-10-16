<?php
require_once __DIR__ . '/vendor/autoload.php';

use Core\Database;

// 🧱 Kết nối SQLite thông qua lớp Database
$pdo = (new Database())->getConnection();

// 🗂️ Quét tất cả file migration trong thư mục /migrations, bỏ qua interface
$migrationFiles = array_filter(
    glob(__DIR__ . '/migrations/*.php'),
    fn($f) => stripos($f, 'Interface') === false
);

// ⚙️ Xác định chế độ chạy (up hoặc down)
$mode = $argv[1] ?? 'up';

foreach ($migrationFiles as $file) {
    // 🔹 Lấy tên file gốc, bỏ phần .php
    $baseName = pathinfo($file, PATHINFO_FILENAME);

    // 🔹 Nếu tên file có prefix ngày (vd: 20251015_CreateTodosTable)
    // thì chỉ lấy phần sau dấu "_" làm tên class
    $parts = explode('_', $baseName, 2);
    $className = 'Migrations\\' . ($parts[1] ?? $parts[0]);

    echo "🔍 Checking migration: {$className}\n";

    // ✅ Kiểm tra class có tồn tại (autoload)
    if (!class_exists($className)) {
        echo "⚠️  Migration class not found: {$className}\n";
        continue;
    }

    // 🔧 Khởi tạo migration class
    $migration = new $className();

    // 🧩 Thực thi tùy theo chế độ
    if ($mode === 'down') {
        echo "🧹 Rolling back: {$className}\n";
        if (method_exists($migration, 'down')) {
            $migration->down($pdo);
        } else {
            echo "⚠️  No 'down()' method defined.\n";
        }
    } else {
        echo "🚀 Running: {$className}\n";
        if (method_exists($migration, 'up')) {
            $migration->up($pdo);
        } else {
            echo "⚠️  No 'up()' method defined.\n";
        }
    }

    echo "-----------------------------------------\n";
}

// 🎯 Kết thúc
echo $mode === 'down'
    ? "🗑️  Rollback completed.\n"
    : "🎉 All migrations executed successfully!\n";