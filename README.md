# Todo-App
Full-stack Todo App (Next.js frontend + PHP backend with SQLite)

## Overview
This is a full-stack Todo application used for an ARTEMEON coding challenge — the frontend is built with Next.js (TypeScript) and the backend is plain PHP using SQLite for persistence.

## Key points
- Frontend: Next.js (App Router) + TypeScript
- Backend: Plain PHP, OOP structure (Controller → Service → Repository → Database)
- Database: SQLite (database file stored in `backend/db/`)
- Migrations: `backend/migrate.php` to apply or rollback migrations

---

## Project structure (summary)

- backend/
  - public/index.php         — HTTP entrypoint (API routes) (uses `Core\Router` to map requests)
  - migrate.php              — migration runner script
  - migrations/              — migration files (e.g. CreateTodosTable.php)
  - db/                      — contains SQLite file (e.g. todos.db)
  - controller/, core/, repository/, service/, tests/ — backend source code
  - composer.json, vendor/   — PHP dependencies

  Design & patterns (brief):
  - Layered architecture: Controller → Service → Repository → Database.
    - Controller: receives HTTP requests, handles input/response, and delegates business logic to a Service.
    - Service: contains application/business logic and orchestrates one or more Repositories.
    - Repository: data-access layer, performs CRUD operations using a PDO connection.
    - Database: a simple SQLite-backed PDO connection located under `backend/core/Database.php`.
  - Dependency Injection: a lightweight DI container (`backend/core/Container.php`) is used to automatically
    resolve and inject class dependencies via PHP Reflection. Typical flow:
    1. The Router calls a Controller handler.
    2. Controller declares a Service in its constructor and the Container constructs/injects it.
    3. Service declares a Repository in its constructor and the Container injects it.
    4. Repository declares `\PDO` in its constructor and the Container injects the shared SQLite connection
       from `Database::getConnection()`.
    This keeps classes small, testable, and easy to mock in unit tests.

- frontend/
  - app/                     — Next.js app (pages, components)
  - package.json             — frontend dependencies and scripts

---

## Backend — API
The actual backend entrypoint is `backend/public/index.php`. When you run the PHP built-in server with the document root set to `backend/public`, the following REST endpoints are available:

- GET  /api/todos
  - Description: Retrieve all todos
  - Response (JSON):
    {
      "success": true,
      "todos": [ {"id":1, "title":"...", "completed":0, "created_at":"..."}, ... ]
    }

- POST /api/todos/add
  - Description: Create a new todo
  - Body (JSON): { "title": "Todo text" }
  - Response (JSON): { "success": true, "id": 1 }

- POST /api/todos/done
  - Description: Mark a todo item as completed
  - Body (JSON): { "id": 1 }
  - Response (JSON): { "success": true }

Note: The backend sets CORS headers allowing any origin (Access-Control-Allow-Origin: *).

---

## Database & Migrations
The main table is `todos`. The current schema (from `CreateTodosTable` migration) is:

```
CREATE TABLE todos (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  title TEXT NOT NULL,
  completed INTEGER DEFAULT 0,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

To apply migrations (create the table), run the following from the project root:

```bash
# install vendor dependencies if not present
cd backend
composer install

# run migrations (default runs `up`)
php migrate.php

# rollback migrations (run `down`)
php migrate.php down
```

The `migrate.php` script loads all files in `backend/migrations` (excluding interfaces) and calls either the `up` or `down` method depending on the provided argument.

---

## Run backend (development)
1. Install PHP dependencies (if needed):

```bash
cd backend
composer install
```

2. Create/migrate the database:

```bash
php migrate.php
```

3. Start the PHP built-in server (document root = `backend/public`):

```bash
# from project root (or cd into backend)
php -S localhost:8000 -t backend/public
```

The API will be available at: http://localhost:8000
- Example: http://localhost:8000/api/todos

---

## Run frontend (development)

1. Install Node dependencies:

```bash
cd frontend
npm install
```

2. Start the dev server:

```bash
npm run dev
```

The frontend runs by default at http://localhost:3000 and communicates with the backend using the endpoints above. If the backend runs on `localhost:8000`, the frontend can call `http://localhost:8000/api/todos`.

---

## Testing (PHPUnit)
If PHPUnit is installed via Composer (the `vendor/` directory), you can run the backend unit tests like this:

```bash
cd backend
./vendor/bin/phpunit -c phpunit.xml
```

---

## License
MIT
