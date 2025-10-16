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
        $repository = new TodoRepository($pdo);
        $this->service = new TodoService($repository);
    }

    public function list(): void
    {
        header('Content-Type: application/json');
        echo json_encode($this->service->getAll());
    }

    public function add(): void
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $this->service->create($input);
        echo json_encode(['id' => $id]);
    }
}