<?php

namespace Repository;

class TodoRepository extends AbstractRepository
{
    protected function initTable(): void
    {
        $this->table = 'todos';
    }

    public function add(string $title): int
    {
        return $this->create(['title' => $title]);
    }

    public function getAll(): array
    {
        return $this->findAll();
    }

    public function markAsDone(int $id): bool
    {
        $stmt = $this->pdo->prepare("UPDATE {$this->table} SET completed = 1 WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount() > 0;
    }
}