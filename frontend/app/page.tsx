'use client';

import { useEffect, useState } from 'react';
import AddTodoForm from './components/AddTodoForm';
import TodoList from './components/TodoList';
import { Todo } from './types/todo';

/**
 * Page Component: Home
 * ---------------------------------------------------------
 * The main entry point of the Todo application.
 *
 * Responsibilities:
 * - Fetches and displays all todo items from the backend.
 * - Handles adding new todos and marking them as completed.
 * - Manages loading and error states for better UX.
 *
 * This component serves as the parent container for:
 * - {@link AddTodoForm}: handles user input for new todos.
 * - {@link TodoList}: displays the list of existing todos.
 *
 * @returns JSX.Element
 */
export default function Home() {
    const [todos, setTodos] = useState<Todo[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    /**
     * Fetches all todos from the backend API.
     * Called initially on component mount and after any updates.
     */
    const fetchTodos = async () => {
        try {
            const res = await fetch('/api/todos');
            const data = await res.json();

            if (data.success) {
                setTodos(data.todos);
                setError(null);
            } else {
                setError(data.error || 'Failed to fetch todos');
            }
        } catch (err) {
            console.error('Error fetching todos:', err);
            setError('Failed to connect to backend');
        } finally {
            setIsLoading(false);
        }
    };

    // Automatically load todos on first render
    useEffect(() => {
        fetchTodos();
    }, []);

    /**
     * Adds a new todo by sending a POST request to the API.
     * @param title The title of the new todo item.
     */
    const handleAddTodo = async (title: string) => {
        try {
            const res = await fetch('/api/todos', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'add', title }),
            });
            const data = await res.json();

            if (data.success) {
                await fetchTodos();
            } else {
                setError(data.error || 'Failed to add todo');
            }
        } catch (err) {
            console.error('Error adding todo:', err);
            setError('Failed to add todo');
        }
    };

    /**
     * Marks a todo as completed by sending an update to the backend.
     * @param id The ID of the todo item to mark as done.
     */
    const handleToggleDone = async (id: number) => {
        try {
            const res = await fetch('/api/todos', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'done', id }),
            });
            const data = await res.json();

            if (data.success) {
                await fetchTodos();
            } else {
                setError(data.error || 'Failed to mark todo as done');
            }
        } catch (err) {
            console.error('Error marking todo as done:', err);
            setError('Failed to mark todo as done');
        }
    };

    return (
        <div className="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-8 px-4">
            <div className="max-w-2xl mx-auto">
                <div className="bg-white rounded-2xl shadow-xl p-8">
                    <h1 className="text-4xl font-bold text-gray-800 mb-2 text-center">
                        Todo App
                    </h1>
                    <p className="text-gray-500 text-center mb-8">
                        ARTEMEON Coding Challenge
                    </p>

                    {/* Error message */}
                    {error && (
                        <div className="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700">
                            {error}
                        </div>
                    )}

                    {/* Add new todo form */}
                    <AddTodoForm onAdd={handleAddTodo} />

                    {/* Todo list or loading state */}
                    {isLoading ? (
                        <div className="text-center py-8 text-gray-500">
                            Loading todos...
                        </div>
                    ) : (
                        <TodoList todos={todos} onToggleDone={handleToggleDone} />
                    )}
                </div>
            </div>
        </div>
    );
}