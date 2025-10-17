<?php
namespace Test;

use PHPUnit\Framework\TestCase;
use Repository\TodoRepository;

class TodoRepositoryTest extends TestCase
{
    private \PDO $pdo;

    protected function setUp(): void
    {
        // ⚙️ Tạo SQLite in-memory cho mỗi test
        $this->pdo = new \PDO('sqlite::memory:');
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        // 🧱 Tạo bảng todos (giống migration thực)
        $this->pdo->exec("
            CREATE TABLE todos (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                completed INTEGER DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ");
    }

    public function testAddAndGetAllUsesInMemoryPdo(): void
    {
        $repo = new TodoRepository($this->pdo);

        $id = $repo->add('First task');
        $this->assertIsInt($id);
        $this->assertGreaterThan(0, $id);

        $rows = $repo->getAll();
        $this->assertCount(1, $rows);
        $this->assertSame('First task', $rows[0]['title']);
        $this->assertEquals(0, $rows[0]['completed']);
    }

    public function testMarkAsDoneUpdatesRow(): void
    {
        $repo = new TodoRepository($this->pdo);

        $id1 = $repo->add('Task A');
        $id2 = $repo->add('Task B');

        $this->assertTrue($repo->markAsDone($id1));

        $rows = $repo->getAll();
        $found = array_filter($rows, fn($r) => (int)$r['id'] === $id1);
        $found = reset($found);

        $this->assertNotNull($found);
        $this->assertEquals(1, $found['completed']);
    }

    public function testGetAllReturnsEmptyWhenNoRows(): void
    {
        $repo = new TodoRepository($this->pdo);
        $rows = $repo->getAll();
        $this->assertIsArray($rows);
        $this->assertCount(0, $rows);
    }
}