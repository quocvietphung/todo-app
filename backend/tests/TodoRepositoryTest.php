<?php
namespace Test;

use PHPUnit\Framework\TestCase;
use Repository\TodoRepository;

class TodoRepositoryTest extends TestCase
{
    private \PDO $pdo;

    protected function setUp(): void
    {
        // Create a fresh in-memory SQLite database
        $this->pdo = new \PDO('sqlite::memory:');
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        // Create todos table (matching migration schema)
        $this->pdo->exec("
            CREATE TABLE todos (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                completed INTEGER DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME,
                deleted_at DATETIME
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

    public function testUpdateTitleChangesTodoText(): void
    {
        $repo = new TodoRepository($this->pdo);

        $id = $repo->add('Old title');
        $this->assertTrue($repo->updateTitle($id, 'Updated title'));

        $rows = $repo->getAll();
        $updated = array_filter($rows, fn($r) => (int)$r['id'] === $id);
        $todo = reset($updated);

        $this->assertSame('Updated title', $todo['title']);
    }

    public function testSoftDeleteRemovesFromGetAll(): void
    {
        $repo = new TodoRepository($this->pdo);

        $id = $repo->add('Task to delete');
        $this->assertTrue($repo->remove($id));

        $rows = $repo->getAll();
        $this->assertCount(0, $rows, 'Deleted todos should not appear in getAll()');
    }

    public function testGetAllReturnsEmptyWhenNoRows(): void
    {
        $repo = new TodoRepository($this->pdo);
        $rows = $repo->getAll();
        $this->assertIsArray($rows);
        $this->assertCount(0, $rows);
    }
}