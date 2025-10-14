# todo-app
Full-stack Todo App with Next.js frontend and PHP (SQLite) backend

## Overview
This is a simple full-stack Todo web application built for the ARTEMEON Coding Challenge.

### Backend
- **Technology**: Pure PHP (no framework)
- **Database**: SQLite for persistence
- **API Endpoints**: 
  - `GET /api.php?action=list` - List all todos
  - `POST /api.php?action=add` - Add a new todo
  - `POST /api.php?action=done` - Mark a todo as done
- **Files**:
  - `backend/api.php` - API endpoint handler
  - `backend/Todo.php` - CRUD logic for todos
  - `backend/todos.db` - SQLite database (auto-created)

### Frontend
- **Technology**: Next.js 15 with TypeScript and Tailwind CSS (App Router)
- **Components**:
  - `AddTodoForm` - Form to submit new todos
  - `TodoList` - Display todos and mark them as done
- **Features**:
  - Fetches todos from backend API
  - Real-time UI updates
  - Clean, responsive design

## Setup Instructions

### Prerequisites
- PHP 8.0+ with SQLite extension
- Node.js 18+ and npm

### Backend Setup

1. Navigate to the backend directory:
   ```bash
   cd backend
   ```

2. Start the PHP built-in server:
   ```bash
   php -S localhost:8000
   ```

The backend API will be available at `http://localhost:8000/api.php`

### Frontend Setup

1. Navigate to the frontend directory:
   ```bash
   cd frontend
   ```

2. Install dependencies:
   ```bash
   npm install
   ```

3. Start the development server:
   ```bash
   npm run dev
   ```

The frontend will be available at `http://localhost:3000`

## Usage

1. Start both the backend and frontend servers
2. Open `http://localhost:3000` in your browser
3. Add todos using the input form
4. Click the checkbox to mark todos as done

## Project Structure

```
todo-app/
├── backend/
│   ├── api.php          # API endpoint handler
│   ├── Todo.php         # Todo CRUD class
│   └── todos.db         # SQLite database (auto-created)
├── frontend/
│   ├── app/
│   │   ├── components/
│   │   │   ├── AddTodoForm.tsx
│   │   │   └── TodoList.tsx
│   │   ├── types/
│   │   │   └── todo.ts
│   │   ├── page.tsx     # Main page
│   │   ├── layout.tsx   # Root layout
│   │   └── globals.css  # Global styles
│   └── package.json
└── README.md
```

## Database Schema

```sql
CREATE TABLE todos (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    completed INTEGER DEFAULT 0
)
```

## API Documentation

### List Todos
- **Endpoint**: `GET /api.php?action=list`
- **Response**: 
  ```json
  {
    "success": true,
    "todos": [
      {"id": 1, "title": "Example todo", "completed": false}
    ]
  }
  ```

### Add Todo
- **Endpoint**: `POST /api.php?action=add`
- **Body**: 
  ```json
  {"title": "New todo"}
  ```
- **Response**: 
  ```json
  {"success": true, "id": 1}
  ```

### Mark Todo as Done
- **Endpoint**: `POST /api.php?action=done`
- **Body**: 
  ```json
  {"id": 1}
  ```
- **Response**: 
  ```json
  {"success": true}
  ```

## License
MIT
