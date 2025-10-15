<?php

namespace Repository;

// Simple TodoRepository using PDO (SQLite). Constructor accepts a PDO instance
// so tests can inject an in-memory database. Repository ensures the todos table
// exists and provides basic CRUD operations used by the service/controller.
class TodoRepository
{
    private \PDO $pdo;

    public function __construct(?\PDO $pdo = null, string $dbPath = __DIR__ . '/../db/todos.db')
    {
        if ($pdo !== null) {
            $this->pdo = $pdo;
        } else {
            // create a file-based sqlite PDO by default (keeps compatibility)
            $this->pdo = new \PDO('sqlite:' . $dbPath);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }

        $this->initDatabase();
    }

    // Ensure table exists. Idempotent.
    private function initDatabase(): void
    {
        $this->pdo->exec(
            'CREATE TABLE IF NOT EXISTS todos (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                completed INTEGER DEFAULT 0
            )'
        );
    }

    // Return all todos as associative arrays. Convert completed to boolean.
    public function getAll(): array
    {
        $stmt = $this->pdo->query('SELECT id, title, completed FROM todos ORDER BY id DESC');
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($rows as &$r) {
            $r['completed'] = (bool)$r['completed'];
        }
        return $rows;
    }

    // Insert a todo and return the new id.
    public function add(string $title): int
    {
        $stmt = $this->pdo->prepare('INSERT INTO todos (title) VALUES (:title)');
        $stmt->bindValue(':title', $title, \PDO::PARAM_STR);
        $stmt->execute();
        return (int)$this->pdo->lastInsertId();
    }

    // Mark a todo as done. Returns true if a row was updated.
    public function markAsDone(int $id): bool
    {
        $stmt = $this->pdo->prepare('UPDATE todos SET completed = 1 WHERE id = :id');
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}

