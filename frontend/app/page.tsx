'use client';

import {JSX, useEffect, useState} from 'react';
import AddTodoForm from './components/AddTodoForm';
import TodoList from './components/TodoList';
import { Todo } from './types/todo';

export default function Home(): JSX.Element {
    const [todos, setTodos] = useState<Todo[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    /** Fetch all todos */
    const fetchTodos = async () => {
        try {
            const res = await fetch('/api/todos');
            const data = await res.json();
            if (data.success) {
                setTodos(data.todos);
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

    useEffect(() => {
        fetchTodos();
    }, []);

    /** Add new todo */
    const handleAddTodo = async (title: string) => {
        await fetch('/api/todos', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'add', title }),
        });
        await fetchTodos();
    };

    /** Mark as done */
    const handleToggleDone = async (id: number) => {
        await fetch('/api/todos', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'done', id }),
        });
        await fetchTodos();
    };

    /** Update todo title */
    const handleUpdateTodo = async (id: number, newTitle: string) => {
        await fetch('/api/todos', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'update', id, title: newTitle }),
        });
        await fetchTodos();
    };

    /** Delete todo */
    const handleDeleteTodo = async (id: number) => {
        await fetch('/api/todos', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'delete', id }),
        });
        await fetchTodos();
    };

    return (
        <div className="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-8 px-4">
            <div className="max-w-2xl mx-auto">
                <div className="bg-white rounded-2xl shadow-xl p-8">
                    <h1 className="text-4xl font-bold text-gray-800 mb-2 text-center">Todo App</h1>
                    <p className="text-gray-500 text-center mb-8">ARTEMEON Coding Challenge</p>

                    {error && (
                        <div className="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700">
                            {error}
                        </div>
                    )}

                    <AddTodoForm onAdd={handleAddTodo} />

                    {isLoading ? (
                        <div className="text-center py-8 text-gray-500">Loading todos...</div>
                    ) : (
                        <TodoList
                            todos={todos}
                            onToggleDone={handleToggleDone}
                            onUpdate={handleUpdateTodo}
                            onDelete={handleDeleteTodo}
                        />
                    )}
                </div>
            </div>
        </div>
    );
}