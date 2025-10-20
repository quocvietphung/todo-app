'use client';

import { useRef, useState } from 'react';

interface AddTodoFormProps {
    /**
     * Callback triggered when a new todo is submitted.
     * @param title The title of the todo item to be added.
     */
    onAdd: (title: string) => Promise<void>;
}

export default function AddTodoForm({ onAdd }: AddTodoFormProps) {
    const [title, setTitle] = useState('');
    const [isLoading, setIsLoading] = useState(false);
    const inputRef = useRef<HTMLInputElement>(null);

    /**
     * Handles form submission.
     * Prevents default page reload and validates input before sending.
     */
    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();

        const trimmed = title.trim();
        if (!trimmed) return;

        setIsLoading(true);
        try {
            await onAdd(trimmed);
            setTitle('');
            inputRef.current?.focus();
        } catch (err) {
            console.error('Error adding todo:', err);
        } finally {
            setIsLoading(false);
        }
    };

    return (
        <form onSubmit={handleSubmit} className="mb-6">
            <div className="flex gap-2">
                <input
                    ref={inputRef}
                    type="text"
                    aria-label="Add new todo"
                    value={title}
                    onChange={(e) => setTitle(e.target.value)}
                    placeholder="What needs to be done?"
                    className="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all disabled:bg-gray-100"
                    disabled={isLoading}
                />

                <button
                    type="submit"
                    disabled={isLoading || !title.trim()}
                    className={`px-6 py-2 rounded-lg text-white transition-colors ${
                        isLoading || !title.trim()
                            ? 'bg-gray-300 cursor-not-allowed'
                            : 'bg-blue-500 hover:bg-blue-600'
                    }`}
                >
                    {isLoading ? 'Adding...' : 'Add'}
                </button>
            </div>
        </form>
    );
}