<?php

namespace Repository;

use Core\Interfaces\RepositoryInterface;

abstract class AbstractRepository implements RepositoryInterface
{
    protected \PDO $pdo;
    protected string $table;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->initTable();
    }

    public function findAll(): array {
        $stmt = $this->pdo->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    public function create(array $data): int {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $stmt = $this->pdo->prepare("INSERT INTO {$this->table} ($columns) VALUES ($placeholders)");
        $stmt->execute($data);
        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $set = implode(', ', array_map(fn($k) => "$k = :$k", array_keys($data)));
        $stmt = $this->pdo->prepare("UPDATE {$this->table} SET $set WHERE id = :id");
        $data['id'] = $id;
        return $stmt->execute($data);
    }

    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    abstract protected function initTable(): void;
}