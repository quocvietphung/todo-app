<?php
namespace Controller;

use Service\TodoService;

/**
 * Class TodoController
 * Controller responsible for handling HTTP requests related to todos.
 * It delegates all business logic to {@see TodoService}.
 *
 * Responsibilities:
 * - Parse input and forward validated data to the service layer.
 * - Return structured arrays that will be serialized as JSON in the response.
 *
 * @package Controller
 */
class TodoController
{
    private TodoService $service;

    /**
     * Initializes the controller with a TodoService instance.
     *
     * @param TodoService $service The service layer for business logic.
     */
    public function __construct(TodoService $service)
    {
        $this->service = $service;
    }

    /**
     * Retrieves all todos.
     *
     * @return array List of todo records.
     */
    public function listTodos(): array
    {
        return $this->service->getAllTodos();
    }

    /**
     * Creates a new todo item.
     *
     * @param array $data Request payload containing todo fields.
     * @return int ID of the newly created record.
     */
    public function create(array $data): int
    {
        return $this->service->create($data);
    }

    /**
     * Updates an existing todo item.
     *
     * @param int $id Todo ID.
     * @param array $data Updated data.
     * @return bool True if updated successfully.
     */
    public function update(int $id, array $data): bool
    {
        return $this->service->updateTodo($id, $data);
    }

    /**
     * Marks a todo as completed.
     *
     * @param int $id Todo ID.
     * @return bool True if marked as done.
     */
    public function markAsDone(int $id): bool
    {
        return $this->service->completeTodo($id);
    }

    /**
     * Soft deletes a todo (marks deleted_at timestamp).
     *
     * @param int $id Todo ID.
     * @return bool True if deleted successfully.
     */
    public function delete(int $id): bool
    {
        return $this->service->deleteTodo($id);
    }
}