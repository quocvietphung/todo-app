<?php
namespace Test;

use PHPUnit\Framework\TestCase;
use Service\TodoService;
use Repository\TodoRepository;

class TodoServiceTest extends TestCase
{
    public function testCreateThrowsOnEmptyTitle(): void
    {
        $repo = $this->createMock(TodoRepository::class);
        $service = new TodoService($repo);

        $this->expectException(\InvalidArgumentException::class);
        $service->create(['title' => '   ']);
    }

    public function testCreateReturnsIdAndCallsRepo(): void
    {
        $repo = $this->createMock(TodoRepository::class);
        $repo->expects($this->once())
            ->method('add')
            ->with('Buy milk')
            ->willReturn(42);

        $service = new TodoService($repo);
        $id = $service->create(['title' => '  Buy milk  ']);

        $this->assertSame(42, $id);
    }

    public function testCompleteTodoDelegatesToRepo(): void
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

    public function testUpdateTodoValidatesAndCallsRepo(): void
    {
        $repo = $this->createMock(TodoRepository::class);
        $repo->expects($this->once())
            ->method('updateTitle')
            ->with(5, 'Updated Task')
            ->willReturn(true);

        $service = new TodoService($repo);
        $result = $service->updateTodo(5, ['title' => 'Updated Task']);

        $this->assertTrue($result);
    }

    public function testUpdateTodoThrowsIfTitleEmpty(): void
    {
        $repo = $this->createMock(TodoRepository::class);
        $service = new TodoService($repo);

        $this->expectException(\InvalidArgumentException::class);
        $service->updateTodo(1, ['title' => '   ']);
    }

    public function testDeleteTodoDelegatesToRepo(): void
    {
        $repo = $this->createMock(TodoRepository::class);
        $repo->expects($this->once())
            ->method('remove')
            ->with(7)
            ->willReturn(true);

        $service = new TodoService($repo);
        $this->assertTrue($service->deleteTodo(7));
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