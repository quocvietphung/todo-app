'use client';

import { Todo } from '../types/todo';
import {
    AlertDialog,
    AlertDialogTrigger,
    AlertDialogContent,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogTitle,
    AlertDialogDescription,
} from '@/components/ui/alert-dialog';

interface TodoListProps {
    todos: Todo[];
    onToggleDone: (id: number) => Promise<void>;
    onUpdate: (id: number, newTitle: string) => Promise<void>;
    onDelete: (id: number) => Promise<void>;
}

/**
 * Component: TodoList
 * ---------------------------------------------------------
 * - Hiển thị danh sách todos.
 * - Cho phép đánh dấu hoàn thành, chỉnh sửa, xoá.
 * - Dùng ShadCN <AlertDialog> để confirm xoá (thay confirm()).
 */
export default function TodoList({
                                     todos,
                                     onToggleDone,
                                     onUpdate,
                                     onDelete,
                                 }: TodoListProps) {
    /** Toggle todo */
    const handleToggle = async (id: number, completed: boolean) => {
        if (completed) return;
        try {
            await onToggleDone(id);
        } catch (err) {
            console.error('Error toggling todo:', err);
        }
    };

    /** Edit todo */
    const handleEdit = async (id: number, currentTitle: string) => {
        const newTitle = prompt('Edit todo title:', currentTitle);
        if (!newTitle || newTitle.trim() === '' || newTitle === currentTitle) return;
        try {
            await onUpdate(id, newTitle.trim());
        } catch (err) {
            console.error('Error updating todo:', err);
        }
    };

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

                    {/* Buttons */}
                    <div className="flex gap-3 text-sm">
                        {!todo.completed && (
                            <button
                                onClick={() => handleEdit(todo.id, todo.title)}
                                className="text-blue-500 hover:underline"
                            >
                                Edit
                            </button>
                        )}

                        {/* ✅ ShadCN AlertDialog cho Delete */}
                        <AlertDialog>
                            <AlertDialogTrigger asChild>
                                <button className="text-red-500 hover:underline">Delete</button>
                            </AlertDialogTrigger>
                            <AlertDialogContent className="max-w-sm">
                                <AlertDialogTitle className="text-lg font-semibold text-gray-900">
                                    Confirm Deletion
                                </AlertDialogTitle>
                                <AlertDialogDescription className="text-gray-700 mb-4">
                                    Are you sure you want to delete this todo? This action cannot
                                    be undone.
                                </AlertDialogDescription>

                                <div className="flex justify-end gap-3">
                                    <AlertDialogCancel className="px-4 py-2 text-gray-500 hover:text-gray-700">
                                        Cancel
                                    </AlertDialogCancel>
                                    <AlertDialogAction
                                        className="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600"
                                        onClick={() => onDelete(todo.id)}
                                    >
                                        Confirm
                                    </AlertDialogAction>
                                </div>
                            </AlertDialogContent>
                        </AlertDialog>
                    </div>
                </div>
            ))}
        </div>
    );
}