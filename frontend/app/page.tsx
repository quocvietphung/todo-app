'use client';

import { useEffect, useState } from 'react';
import AddTodoForm from './components/AddTodoForm';
import TodoList from './components/TodoList';
import { Todo } from './types/todo';

const API_URL = 'http://localhost:8000/api.php';

export default function Home() {
  const [todos, setTodos] = useState<Todo[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  const fetchTodos = async () => {
    try {
      const response = await fetch(`${API_URL}?action=list`);
      const data = await response.json();
      if (data.success) {
        setTodos(data.todos);
        setError(null);
      } else {
        setError(data.error || 'Failed to fetch todos');
      }
    } catch (err) {
      setError('Failed to connect to backend');
      console.error('Error fetching todos:', err);
    } finally {
      setIsLoading(false);
    }
  };

  useEffect(() => {
    fetchTodos();
  }, []);

  const handleAddTodo = async (title: string) => {
    try {
      const response = await fetch(`${API_URL}?action=add`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ title }),
      });
      const data = await response.json();
      if (data.success) {
        await fetchTodos();
      } else {
        setError(data.error || 'Failed to add todo');
      }
    } catch (err) {
      setError('Failed to add todo');
      console.error('Error adding todo:', err);
    }
  };

  const handleToggleDone = async (id: number) => {
    try {
      const response = await fetch(`${API_URL}?action=done`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ id }),
      });
      const data = await response.json();
      if (data.success) {
        await fetchTodos();
      } else {
        setError(data.error || 'Failed to mark todo as done');
      }
    } catch (err) {
      setError('Failed to mark todo as done');
      console.error('Error marking todo as done:', err);
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

          {error && (
            <div className="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700">
              {error}
            </div>
          )}

          <AddTodoForm onAdd={handleAddTodo} />

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
