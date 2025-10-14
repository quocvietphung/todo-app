'use client';

import { Todo } from '../types/todo';

interface TodoListProps {
  todos: Todo[];
  onToggleDone: (id: number) => Promise<void>;
}

export default function TodoList({ todos, onToggleDone }: TodoListProps) {
  if (todos.length === 0) {
    return (
      <div className="text-center py-8 text-gray-500">
        No todos yet. Add one above!
      </div>
    );
  }

  return (
    <div className="space-y-2">
      {todos.map((todo) => (
        <div
          key={todo.id}
          className={`flex items-center gap-3 p-4 border rounded-lg transition-all ${
            todo.completed
              ? 'bg-gray-50 border-gray-200'
              : 'bg-white border-gray-300 hover:border-blue-300'
          }`}
        >
          <input
            type="checkbox"
            checked={todo.completed}
            onChange={() => !todo.completed && onToggleDone(todo.id)}
            className="w-5 h-5 text-blue-500 border-gray-300 rounded focus:ring-2 focus:ring-blue-500 cursor-pointer"
            disabled={todo.completed}
          />
          <span
            className={`flex-1 ${
              todo.completed
                ? 'line-through text-gray-400'
                : 'text-gray-800'
            }`}
          >
            {todo.title}
          </span>
          {todo.completed && (
            <span className="text-xs text-green-600 font-semibold">✓ Done</span>
          )}
        </div>
      ))}
    </div>
  );
}
