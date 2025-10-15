<?php

namespace Test;

use PHPUnit\Framework\TestCase;
use Service\TodoService;
use Repository\TodoRepository;

class TodoServiceTest extends TestCase
{
    public function testCreateTodoThrowsOnEmptyTitle(): void
    {
        $repo = $this->createMock(TodoRepository::class);
        $service = new TodoService($repo);

        $this->expectException(\InvalidArgumentException::class);
        $service->createTodo('   ');
    }

    public function testCreateTodoReturnsIdAndCallsRepo(): void
    {
        $repo = $this->createMock(TodoRepository::class);
        $repo->expects($this->once())
             ->method('add')
             ->with('Buy milk')
             ->willReturn(42);

        $service = new TodoService($repo);
        $id = $service->createTodo('  Buy milk  ');

        $this->assertSame(42, $id);
    }

    public function testCompleteTodoValidatesIdAndDelegates(): void
    {
        $repo = $this->createMock(TodoRepository::class);
        $repo->expects($this->once())
             ->method('markAsDone')
             ->with(3)
             ->willReturn(true);

        $service = new TodoService($repo);
        $result = $service->completeTodo(3);

        $this->assertTrue($result);
    }

    public function testGetAllTodosDelegatesToRepo(): void
    {
        $expected = [
            ['id' => 1, 'title' => 'Task 1', 'completed' => false],
            ['id' => 2, 'title' => 'Task 2', 'completed' => true],
        ];

        $repo = $this->createMock(TodoRepository::class);
        $repo->expects($this->once())
             ->method('getAll')
             ->willReturn($expected);

        $service = new TodoService($repo);
        $this->assertSame($expected, $service->getAllTodos());
    }
}

