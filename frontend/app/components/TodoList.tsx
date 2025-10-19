'use client';

import { Todo } from '../types/todo';

interface TodoListProps {
    /**
     * The list of todo items to be displayed.
     */
    todos: Todo[];

    /**
     * Callback triggered when a todo is marked as completed.
     * @param id The ID of the todo item to toggle.
     */
    onToggleDone: (id: number) => Promise<void>;
}

/**
 * Component: TodoList
 * ---------------------------------------------------------
 * Renders the list of todo items.
 *
 * Features:
 * - Displays each todo with its completion status.
 * - Allows marking items as completed via checkbox.
 * - Shows an empty-state message if the list is empty.
 *
 * @param {TodoListProps} props - The component props.
 * @returns JSX.Element
 */
export default function TodoList({ todos, onToggleDone }: TodoListProps) {
    /**
     * Handles toggling of a todo item.
     * Prevents re-marking items that are already completed.
     */
    const handleToggle = async (id: number, completed: boolean) => {
        if (completed) return;
        try {
            await onToggleDone(id);
        } catch (err) {
            console.error('Error toggling todo:', err);
        }
    };

    // Render empty state when no todos are present
    if (!todos || todos.length === 0) {
        return (
            <div className="text-center py-8 text-gray-500 italic">
                No todos yet. Add one above!
            </div>
        );
    }

    return (
        <div className="space-y-2" role="list">
            {todos.map((todo) => (
                <div
                    key={todo.id}
                    role="listitem"
                    className={`flex items-center gap-3 p-4 border rounded-lg shadow-sm transition-all ${
                        todo.completed
                            ? 'bg-gray-50 border-gray-200'
                            : 'bg-white border-gray-300 hover:border-blue-300 hover:scale-[1.01]'
                    }`}
                >
                    {/* Completion checkbox */}
                    <input
                        type="checkbox"
                        checked={todo.completed}
                        onChange={() => handleToggle(todo.id, todo.completed)}
                        className="w-5 h-5 text-blue-500 border-gray-300 rounded focus:ring-2 focus:ring-blue-500 cursor-pointer"
                        disabled={todo.completed}
                        aria-label={`Mark "${todo.title}" as done`}
                    />

                    {/* Todo title text */}
                    <span
                        className={`flex-1 truncate ${
                            todo.completed
                                ? 'line-through text-gray-400'
                                : 'text-gray-800 font-medium'
                        }`}
                    >
            {todo.title}
          </span>

                    {/* Completed tag */}
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