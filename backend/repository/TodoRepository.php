<?php
namespace Repository;

class TodoRepository extends AbstractRepository
{
    protected function initTable(): void
    {
        $this->table = 'todos';
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS todos (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                completed INTEGER DEFAULT 0
            )
        ");
    }

    public function markAsDone(int $id): bool
    {
        $stmt = $this->pdo->prepare("UPDATE {$this->table} SET completed = 1 WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount() > 0;
    }
}