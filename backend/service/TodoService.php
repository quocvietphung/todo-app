<?php
namespace Service;

use Service\AbstractService;
use Repository\TodoRepository;

/**
 * ✅ TodoService
 * ------------------------------
 * Tầng nghiệp vụ cho Todo — kế thừa AbstractService
 */
class TodoService extends AbstractService
{
    public function __construct(TodoRepository $repo)
    {
        parent::__construct($repo);
    }

    /**
     * 🟢 Alias cho controller cũ.
     */
    public function getAllTodos(): array
    {
        return $this->getAll();
    }

    /**
     * 🟢 Tạo Todo với validate riêng.
     */
    public function create(array $data): int
    {
        $title = trim($data['title'] ?? '');
        if ($title === '') {
            throw new \InvalidArgumentException('Title cannot be empty');
        }

        // Gọi repository cụ thể (có thể khác so với create() gốc)
        return $this->repo->add($title);
    }

    /**
     * 🟢 Đánh dấu hoàn thành todo.
     */
    public function completeTodo(int $id): bool
    {
        if ($id <= 0) {
            throw new \InvalidArgumentException('Invalid ID');
        }

        return $this->repo->markAsDone($id);
    }
}