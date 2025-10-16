'use client';

import { Todo } from '../types/todo';

interface TodoListProps {
    todos: Todo[];
    onToggleDone: (id: number) => Promise<void>;
}

/**
 * 🧩 TodoList Component
 * Hiển thị danh sách các việc cần làm.
 * - Nếu chưa có việc nào → hiển thị thông báo.
 * - Nếu có → render từng dòng với checkbox + trạng thái hoàn thành.
 */
export default function TodoList({ todos, onToggleDone }: TodoListProps) {
    // Trường hợp chưa có todo nào
    if (!todos || todos.length === 0) {
        return (
            <div className="text-center py-8 text-gray-500">
                No todos yet. Add one above!
            </div>
        );
    }

    // Trường hợp có todos
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
                    {/* Checkbox toggle */}
                    <input
                        type="checkbox"
                        checked={todo.completed}
                        onChange={() => !todo.completed && onToggleDone(todo.id)}
                        className="w-5 h-5 text-blue-500 border-gray-300 rounded focus:ring-2 focus:ring-blue-500 cursor-pointer"
                        disabled={todo.completed}
                    />

                    {/* Tiêu đề công việc */}
                    <span
                        className={`flex-1 truncate ${
                            todo.completed
                                ? 'line-through text-gray-400'
                                : 'text-gray-800'
                        }`}
                    >
            {todo.title}
          </span>

                    {/* Dấu hoàn thành */}
                    {todo.completed && (
                        <span className="text-xs text-green-600 font-semibold">
              ✓ Done
            </span>
                    )}
                </div>
            ))}
        </div>
    );
}