<?php

namespace Controller;

use Core\Database;
use Repository\TodoRepository;
use Service\TodoService;

class TodoController
{
    private TodoService $service;

    public function __construct()
    {
        $pdo = (new Database())->getConnection();
        $repo = new TodoRepository($pdo);
        $this->service = new TodoService($repo);
    }

    // ✅ Lấy danh sách todos
    public function listTodos(): array
    {
        return $this->service->getAllTodos();
    }

    // ✅ Thêm mới todo
    public function create(string $title): int
    {
        return $this->service->createTodo($title);
    }

    // ✅ Đánh dấu hoàn thành
    public function markAsDone(int $id): bool
    {
        return $this->service->completeTodo($id);
    }
}