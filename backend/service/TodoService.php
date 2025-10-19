<?php
namespace Service;

use Repository\TodoRepository;

/**
 * Class TodoService
 * ---------------------------------------------------------
 * Service layer responsible for business logic related to "Todo" entities.
 *
 * Responsibilities:
 * - Acts as an intermediary between the Controller and the Repository layer.
 * - Implements domain-specific rules such as validation, updating, and deletion logic.
 * - Never interacts with the database directly — only via the repository.
 *
 * Inherits common CRUD methods from {@see AbstractService}.
 *
 * @package Service
 */
class TodoService extends AbstractService
{
    /**
     * Initializes the TodoService with its corresponding repository.
     *
     * @param TodoRepository $repo The repository instance responsible for data access.
     */
    public function __construct(TodoRepository $repo)
    {
        parent::__construct($repo);
    }

    /**
     * Returns all todos (for backward compatibility with older controllers).
     *
     * @return array A list of all todo records.
     */
    public function getAllTodos(): array
    {
        return $this->repo->getAll();
    }

    /**
     * Creates a new todo item after validating input data.
     *
     * @param array $data The input data containing at least the 'title' field.
     * @return int The ID of the newly created todo record.
     *
     * @throws \InvalidArgumentException If the 'title' field is empty.
     */
    public function create(array $data): int
    {
        $title = trim($data['title'] ?? '');
        if ($title === '') {
            throw new \InvalidArgumentException('Title cannot be empty');
        }

        return $this->repo->add($title);
    }

    /**
     * Updates the title of an existing todo.
     *
     * @param int $id The ID of the todo to update.
     * @param array $data The input data containing the new 'title'.
     * @return bool True if the update was successful, false otherwise.
     *
     * @throws \InvalidArgumentException If the ID or title is invalid.
     */
    public function updateTodo(int $id, array $data): bool
    {
        if ($id <= 0) {
            throw new \InvalidArgumentException('Invalid ID');
        }

        $title = trim($data['title'] ?? '');
        if ($title === '') {
            throw new \InvalidArgumentException('Title cannot be empty');
        }

        return $this->repo->updateTitle($id, $title);
    }

    /**
     * Marks a specific todo item as completed.
     *
     * @param int $id The ID of the todo to be marked as done.
     * @return bool True if the update was successful, false otherwise.
     *
     * @throws \InvalidArgumentException If the provided ID is invalid.
     */
    public function completeTodo(int $id): bool
    {
        if ($id <= 0) {
            throw new \InvalidArgumentException('Invalid ID');
        }

        return $this->repo->markAsDone($id);
    }

    /**
     * Soft deletes a todo item by setting the deleted_at timestamp.
     *
     * @param int $id The ID of the todo to delete.
     * @return bool True if the record was marked as deleted.
     *
     * @throws \InvalidArgumentException If the ID is invalid.
     */
    public function deleteTodo(int $id): bool
    {
        if ($id <= 0) {
            throw new \InvalidArgumentException('Invalid ID');
        }

        return $this->repo->remove($id);
    }
}