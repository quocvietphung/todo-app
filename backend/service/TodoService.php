<?php

namespace Service;

use Core\Interfaces\ServiceInterface;
use Repository\TodoRepository;

/**
 * ✅ TodoService
 * ------------------------------
 * Lớp xử lý nghiệp vụ (business logic) cho Todo.
 * Tầng này không thao tác trực tiếp với database,
 * mà ủy quyền cho Repository để tách biệt logic & persistence.
 */
class TodoService implements ServiceInterface
{
    private TodoRepository $repo;

    public function __construct(TodoRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * 🟢 Lấy tất cả todos
     */
    public function getAll(): array
    {
        return $this->repo->findAll();
    }

    /**
     * 🟢 Alias cho controller cũ — lấy tất cả todos
     */
    public function getAllTodos(): array
    {
        return $this->getAll();
    }

    /**
     * 🟢 Lấy 1 todo theo ID
     */
    public function getById(int $id): ?array
    {
        return $this->repo->findById($id);
    }

    /**
     * 🟢 Tạo mới 1 todo (chuẩn interface)
     */
    public function create(array $data): int
    {
        $title = trim($data['title'] ?? '');
        if ($title === '') {
            throw new \InvalidArgumentException('Title cannot be empty');
        }

        return $this->repo->create(['title' => $title]);
    }

    /**
     * 🟢 Alias: tạo todo trực tiếp từ chuỗi title
     * Giúp tương thích với TodoController::create($title)
     */
    public function createTodo(string $title): int
    {
        return $this->create(['title' => $title]);
    }

    /**
     * 🟢 Cập nhật nội dung todo
     */
    public function update(int $id, array $data): bool
    {
        if ($id <= 0) {
            throw new \InvalidArgumentException('Invalid ID');
        }

        $title = trim($data['title'] ?? '');
        if ($title === '') {
            throw new \InvalidArgumentException('Title cannot be empty');
        }

        return $this->repo->update($id, ['title' => $title]);
    }

    /**
     * 🟢 Xóa todo
     */
    public function delete(int $id): bool
    {
        if ($id <= 0) {
            throw new \InvalidArgumentException('Invalid ID');
        }

        return $this->repo->delete($id);
    }

    /**
     * 🟢 Đánh dấu hoàn thành todo
     */
    public function completeTodo(int $id): bool
    {
        if ($id <= 0) {
            throw new \InvalidArgumentException('Invalid ID');
        }

        return $this->repo->markAsDone($id);
    }
}