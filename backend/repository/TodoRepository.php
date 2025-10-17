<?php

namespace Repository;

/**
 * ✅ TodoRepository
 * ------------------------------
 * Chịu trách nhiệm thao tác trực tiếp với bảng `todos` trong DB.
 * Không chứa logic nghiệp vụ (business logic).
 */
class TodoRepository extends AbstractRepository
{
    /**
     * 🏗 Chỉ định tên bảng cho Repository (được gọi trong AbstractRepository::__construct)
     */
    protected function initTable(): void
    {
        $this->table = 'todos';
    }

    /**
     * ➕ Thêm mới Todo vào DB
     */
    public function add(string $title): int
    {
        return $this->create(['title' => $title]);
    }

    /**
     * 📋 Lấy toàn bộ danh sách Todo
     */
    public function getAll(): array
    {
        return $this->findAll();
    }

    /**
     * ✅ Đánh dấu Todo hoàn thành
     */
    public function markAsDone(int $id): bool
    {
        $stmt = $this->pdo->prepare("UPDATE {$this->table} SET completed = 1 WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount() > 0;
    }
}