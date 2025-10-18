<?php

namespace Controller;

use Service\TodoService;

/**
 * ✅ TodoController
 * ------------------------------
 * Controller xử lý các request liên quan đến Todo.
 * - Không khởi tạo thủ công Repository/Database.
 * - Toàn bộ dependency được inject tự động qua Container.
 */
class TodoController
{
    private TodoService $service;

    /**
     * 🧩 Dependency Injection:
     * Container sẽ tự động inject TodoService (và bên trong Service sẽ inject Repository & PDO)
     */
    public function __construct(TodoService $service)
    {
        $this->service = $service;
    }

    /**
     * 🟢 Lấy danh sách tất cả todos
     */
    public function listTodos(): array
    {
        return $this->service->getAllTodos();
    }

    /**
     * 🟢 Tạo mới một todo
     */
    public function create(array $data): int
    {
        return $this->service->create($data);
    }

    /**
     * 🟢 Đánh dấu todo là hoàn thành
     */
    public function markAsDone(int $id): bool
    {
        return $this->service->completeTodo($id);
    }
}