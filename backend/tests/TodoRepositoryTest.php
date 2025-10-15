<?php

namespace Test;

use PHPUnit\Framework\TestCase;
use Repository\TodoRepository;

class TodoRepositoryTest extends TestCase
{
    public function testAddAndGetAllUsesInMemoryPdo(): void
    {
        $pdo = new \PDO('sqlite::memory:');
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $repo = new TodoRepository($pdo);

        $id = $repo->add('First task');
        $this->assertIsInt($id);
        $this->assertGreaterThan(0, $id);

        $rows = $repo->getAll();
        $this->assertCount(1, $rows);
        $this->assertSame('First task', $rows[0]['title']);
        $this->assertFalse($rows[0]['completed']);
    }

    public function testMarkAsDoneUpdatesRow(): void
    {
        $pdo = new \PDO('sqlite::memory:');
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $repo = new TodoRepository($pdo);

        $id1 = $repo->add('Task A');
        $id2 = $repo->add('Task B');

        $this->assertTrue($repo->markAsDone($id1));

        $rows = $repo->getAll();
        // Find the row with id == $id1
        $found = null;
        foreach ($rows as $r) {
            if ((int)$r['id'] === $id1) {
                $found = $r;
                break;
            }
        }

        $this->assertNotNull($found, 'Marked row should exist');
        $this->assertTrue($found['completed']);
    }

    public function testGetAllReturnsEmptyWhenNoRows(): void
    {
        $pdo = new \PDO('sqlite::memory:');
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $repo = new TodoRepository($pdo);
        $rows = $repo->getAll();
        $this->assertIsArray($rows);
        $this->assertCount(0, $rows);
    }
}

