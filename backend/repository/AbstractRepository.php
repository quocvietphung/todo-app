<?php

namespace Repository;

use Core\Interfaces\RepositoryInterface;
use PDO;
use PDOException;
use InvalidArgumentException;

abstract class AbstractRepository implements RepositoryInterface
{
    protected PDO $pdo;
    protected string $table;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->initTable();
    }

    public function findAll(): array
    {
        try {
            $stmt = $this->pdo->query("SELECT * FROM {$this->table}");
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            throw new \RuntimeException("Failed to fetch all from {$this->table}: " . $e->getMessage());
        }
    }

    public function findById(int $id): ?array
    {
        if ($id <= 0) {
            throw new InvalidArgumentException("Invalid ID value: $id");
        }

        try {
            $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE id = :id");
            $stmt->execute(['id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            throw new \RuntimeException("Failed to find record with ID {$id}: " . $e->getMessage());
        }
    }

    public function create(array $data): int
    {
        if (empty($data)) {
            throw new InvalidArgumentException('Cannot insert empty data array.');
        }

        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        try {
            $stmt = $this->pdo->prepare("INSERT INTO {$this->table} ($columns) VALUES ($placeholders)");
            $stmt->execute($data);
            return (int)$this->pdo->lastInsertId();
        } catch (PDOException $e) {
            throw new \RuntimeException("Failed to insert into {$this->table}: " . $e->getMessage());
        }
    }

    public function update(int $id, array $data): bool
    {
        if ($id <= 0) {
            throw new InvalidArgumentException("Invalid ID value: $id");
        }

        if (empty($data)) {
            throw new InvalidArgumentException('Cannot update with empty data array.');
        }

        $set = implode(', ', array_map(fn($k) => "$k = :$k", array_keys($data)));

        try {
            $stmt = $this->pdo->prepare("UPDATE {$this->table} SET $set WHERE id = :id");
            $data['id'] = $id;
            $stmt->execute($data);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            throw new \RuntimeException("Failed to update ID {$id} in {$this->table}: " . $e->getMessage());
        }
    }

    public function delete(int $id): bool
    {
        if ($id <= 0) {
            throw new InvalidArgumentException("Invalid ID value: $id");
        }

        try {
            $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = :id");
            $stmt->execute(['id' => $id]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            throw new \RuntimeException("Failed to delete ID {$id} in {$this->table}: " . $e->getMessage());
        }
    }

    abstract protected function initTable(): void;
}