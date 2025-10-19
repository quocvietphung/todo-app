<?php
namespace Migrations;

use Migrations\MigrationInterface;

/**
 * Class CreateTodosTable
 * ---------------------------------------------------------
 * Defines the database schema for the "todos" table.
 *
 * This migration is responsible for creating and dropping the
 * main table used to store todo items in the application.
 * It follows a simple SQLite-compatible SQL definition.
 *
 * @package Migrations
 */
class CreateTodosTable implements MigrationInterface
{
    /**
     * Runs the migration: creates the "todos" table if it does not exist.
     *
     * @param \PDO $pdo The PDO connection used to execute SQL commands.
     * @return void
     */
    public function up(\PDO $pdo): void
    {
        echo "Migrating: CreateTodosTable...\n";

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS todos (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                completed INTEGER DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ");

        echo "Table 'todos' created successfully.\n";
    }

    /**
     * Rolls back the migration: drops the "todos" table.
     *
     * @param \PDO $pdo The PDO connection used to execute SQL commands.
     * @return void
     */
    public function down(\PDO $pdo): void
    {
        echo "Rolling back: Drop 'todos' table...\n";

        $pdo->exec("DROP TABLE IF EXISTS todos;");

        echo "Table 'todos' dropped successfully.\n";
    }
}