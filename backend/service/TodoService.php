<?php

namespace Service;

use Core\Interfaces\ServiceInterface;
use Repository\TodoRepository;

/**
 * ✅ TodoService
 * ------------------------------
 * Tầng xử lý nghiệp vụ (Business Logic Layer) cho Todo.
 * - Không truy cập DB trực tiếp.
 * - Ủy quyền cho Repository xử lý truy vấn.
 * - Thực hiện validate dữ liệu & quy tắc nghiệp vụ.
 */
class TodoService implements ServiceInterface
{
    private TodoRepository $repo;

    /**
     * Dependency Injection: Nhận TodoRepository từ bên ngoài.
     */
    public function __construct(TodoRepository $repo)
    {
        $this->repo = $repo;
    }

    // ============================================================
    // 📋 ĐỌC DỮ LIỆU
    // ============================================================

    /**
     * 🟢 Lấy toàn bộ todos.
     */
    public function getAll(): array
    {
        return $this->repo->getAll();
    }

    /**
     * 🟢 Alias tương thích cho controller cũ.
     */
    public function getAllTodos(): array
    {
        return $this->getAll();
    }

    /**
     * 🟢 Lấy 1 todo theo ID.
     */
    public function getById(int $id): ?array
    {
        return $this->repo->findById($id);
    }

    // ============================================================
    // ✏️ GHI DỮ LIỆU
    // ============================================================

    /**
     * 🟢 Tạo mới một todo từ mảng dữ liệu.
     *
     * @param array $data Dữ liệu chứa key 'title'
     * @return int ID của todo mới tạo
     * @throws \InvalidArgumentException nếu title trống
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
     * 🟢 Alias cho Controller: tạo todo trực tiếp từ chuỗi title.
     */
    public function createTodo(string $title): int
    {
        return $this->create(['title' => $title]);
    }

    /**
     * 🟢 Cập nhật nội dung của todo.
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
     * 🟢 Xóa todo.
     */
    public function delete(int $id): bool
    {
        if ($id <= 0) {
            throw new \InvalidArgumentException('Invalid ID');
        }

        return $this->repo->delete($id);
    }

    /**
     * 🟢 Đánh dấu todo là hoàn thành.
     */
    public function completeTodo(int $id): bool
    {
        if ($id <= 0) {
            throw new \InvalidArgumentException('Invalid ID');
        }

        return $this->repo->markAsDone($id);
    }
}