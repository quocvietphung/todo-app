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

    /**
     * Callback triggered when a todo is updated.
     * @param id The ID of the todo item to update.
     * @param newTitle The new title for the todo.
     */
    onUpdate: (id: number, newTitle: string) => Promise<void>;

    /**
     * Callback triggered when a todo is deleted.
     * @param id The ID of the todo item to delete.
     */
    onDelete: (id: number) => Promise<void>;
}

/**
 * Component: TodoList
 * ---------------------------------------------------------
 * Renders the list of todo items.
 *
 * Features:
 * - Displays each todo with its completion status.
 * - Allows marking, editing, or deleting a todo.
 * - Shows an empty-state message if the list is empty.
 *
 * @param {TodoListProps} props - The component props.
 * @returns JSX.Element
 */
export default function TodoList({ todos, onToggleDone, onUpdate, onDelete }: TodoListProps) {
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

    /**
     * Handles updating of a todo title via prompt input.
     */
    const handleEdit = async (id: number, currentTitle: string) => {
        const newTitle = prompt('Edit todo title:', currentTitle);
        if (!newTitle || newTitle.trim() === '' || newTitle === currentTitle) return;
        try {
            await onUpdate(id, newTitle.trim());
        } catch (err) {
            console.error('Error updating todo:', err);
        }
    };

    /**
     * Handles deletion of a todo item with confirmation.
     */
    const handleDelete = async (id: number) => {
        const confirmDelete = confirm('Are you sure you want to delete this todo?');
        if (!confirmDelete) return;
        try {
            await onDelete(id);
        } catch (err) {
            console.error('Error deleting todo:', err);
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
                    className={`flex items-center justify-between gap-3 p-4 border rounded-lg shadow-sm transition-all ${
                        todo.completed
                            ? 'bg-gray-50 border-gray-200'
                            : 'bg-white border-gray-300 hover:border-blue-300 hover:scale-[1.01]'
                    }`}
                >
                    {/* Checkbox + Title */}
                    <div className="flex items-center gap-3 flex-1">
                        <input
                            type="checkbox"
                            checked={todo.completed}
                            onChange={() => handleToggle(todo.id, todo.completed)}
                            className="w-5 h-5 text-blue-500 border-gray-300 rounded focus:ring-2 focus:ring-blue-500 cursor-pointer"
                            disabled={todo.completed}
                            aria-label={`Mark "${todo.title}" as done`}
                        />

                        <span
                            className={`flex-1 truncate ${
                                todo.completed
                                    ? 'line-through text-gray-400'
                                    : 'text-gray-800 font-medium'
                            }`}
                        >
                            {todo.title}
                        </span>
                    </div>

                    {/* Action buttons */}
                    <div className="flex gap-3 text-sm">
                        {!todo.completed && (
                            <button
                                onClick={() => handleEdit(todo.id, todo.title)}
                                className="text-blue-500 hover:underline"
                            >
                                Edit
                            </button>
                        )}
                        <button
                            onClick={() => handleDelete(todo.id)}
                            className="text-red-500 hover:underline"
                        >
                            Delete
                        </button>
                    </div>
                </div>
            ))}
        </div>
    );
}