<?php
namespace Migrations;

use Migrations\MigrationInterface;

class CreateTodosTable implements MigrationInterface
{
    public function up(\PDO $pdo): void
    {
        echo "⚙️  Migrating: CreateTodosTable...\n";

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS todos (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                completed INTEGER DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ");

        echo "✅  Table 'todos' created successfully.\n";
    }

    public function down(\PDO $pdo): void
    {
        echo "🧹 Rolling back: Drop 'todos' table...\n";
        $pdo->exec("DROP TABLE IF EXISTS todos;");
        echo "✅  Table 'todos' dropped successfully.\n";
    }
}