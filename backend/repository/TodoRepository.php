<?php

namespace Repository;

use PDO;

/**
 * Class TodoRepository
 * ---------------------------------------------------------
 * Repository class responsible for all database operations
 * related to the "todos" table.
 *
 * This class extends AbstractRepository to inherit generic CRUD
 * behavior and adds domain-specific methods for Todo operations
 * such as marking items as done, updating titles, or soft deleting.
 *
 * @package Repository
 */
class TodoRepository extends AbstractRepository
{
    /**
     * Initializes the repository by setting the target table name.
     *
     * @return void
     */
    protected function initTable(): void
    {
        $this->table = 'todos';
    }

    /**
     * Inserts a new todo record into the database.
     *
     * @param string $title The title of the todo item.
     * @return int The ID of the newly created record.
     */
    public function add(string $title): int
    {
        return $this->create(['title' => $title]);
    }

    /**
     * Retrieves all non-deleted todos from the database.
     *
     * @return array List of todos (each as associative array).
     */
    public function getAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM {$this->table} WHERE deleted_at IS NULL ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Marks a todo as completed by setting completed = 1.
     *
     * @param int $id The ID of the todo to mark as done.
     * @return bool True if the record was updated successfully.
     */
    public function markAsDone(int $id): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE {$this->table}
            SET completed = 1, updated_at = CURRENT_TIMESTAMP
            WHERE id = :id AND deleted_at IS NULL
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Updates the title of a todo item.
     *
     * @param int $id The ID of the todo to update.
     * @param string $title The new title text.
     * @return bool True if the record was updated successfully.
     */
    public function updateTitle(int $id, string $title): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE {$this->table}
            SET title = :title, updated_at = CURRENT_TIMESTAMP
            WHERE id = :id AND deleted_at IS NULL
        ");
        $stmt->execute(['id' => $id, 'title' => $title]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Soft deletes a todo item by setting deleted_at timestamp.
     *
     * @param int $id The ID of the todo to delete.
     * @return bool True if the record was marked as deleted.
     */
    public function remove(int $id): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE {$this->table}
            SET deleted_at = CURRENT_TIMESTAMP
            WHERE id = :id AND deleted_at IS NULL
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount() > 0;
    }
}