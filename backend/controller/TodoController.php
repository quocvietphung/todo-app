<?php

namespace Controller;

use Service\TodoService;

class TodoController
{
    private TodoService $service;

    public function __construct(TodoService $service)
    {
        $this->service = $service;
    }

    public function listTodos(): array
    {
        return $this->service->getAllTodos();
    }

    public function create(array $data): int
    {
        return $this->service->create($data);
    }

    public function markAsDone(int $id): bool
    {
        return $this->service->completeTodo($id);
    }
}