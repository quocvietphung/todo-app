<?php

namespace Service;

use Repository\TodoRepository;

// Simple service layer that delegates to the repository. Keeps business logic
// separate from persistence and makes unit testing straightforward by
// allowing the repository to be mocked.
class TodoService
{
    private TodoRepository $repo;

    public function __construct(TodoRepository $repo)
    {
        $this->repo = $repo;
    }

    // Return all todos
    public function getAllTodos(): array
    {
        return $this->repo->getAll();
    }

    // Create a new todo with a title. Returns the new id.
    public function createTodo(string $title): int
    {
        $title = trim($title);
        if ($title === '') {
            throw new \InvalidArgumentException('Title cannot be empty');
        }
        return $this->repo->add($title);
    }

    // Mark a todo as completed. Returns true on success, false otherwise.
    public function completeTodo(int $id): bool
    {
        if ($id <= 0) {
            throw new \InvalidArgumentException('Invalid id');
        }
        return $this->repo->markAsDone($id);
    }
}

