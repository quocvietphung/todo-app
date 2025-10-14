<?php

class Todo {
    private $db;

    public function __construct($dbPath = __DIR__ . '/db/todos.db') {
        $this->db = new SQLite3($dbPath);
        $this->initDatabase();
    }

    private function initDatabase() {
        $this->db->exec('
            CREATE TABLE IF NOT EXISTS todos (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                completed INTEGER DEFAULT 0
            )
        ');
    }

    public function getAll() {
        $result = $this->db->query('SELECT id, title, completed FROM todos ORDER BY id DESC');
        $todos = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $row['completed'] = (bool)$row['completed'];
            $todos[] = $row;
        }
        return $todos;
    }

    public function add($title) {
        $stmt = $this->db->prepare('INSERT INTO todos (title) VALUES (:title)');
        $stmt->bindValue(':title', $title, SQLITE3_TEXT);
        $stmt->execute();
        return $this->db->lastInsertRowID();
    }

    public function markAsDone($id) {
        $stmt = $this->db->prepare('UPDATE todos SET completed = 1 WHERE id = :id');
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        return $stmt->execute();
    }
}
