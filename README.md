# Todo-App
Full-stack Todo App (Next.js frontend + PHP backend with SQLite)

## Overview
This is a full-stack Todo application used for an ARTEMEON coding challenge — the frontend is built with Next.js (TypeScript) and the backend is plain PHP using SQLite for persistence.

## Key points
- Frontend: Next.js (App Router) + TypeScript
- Backend: Plain PHP, OOP structure Controller → Service → Repository → Database (PDO)
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

  Design patterns:
  - Layered architecture: Controller → Service → Repository → Database.
    - Controller: receives HTTP requests, handles input/response, and delegates business logic to a Service.
    - Service: contains application/business logic and orchestrates one or more Repositories.
    - Repository: data-access layer, performs CRUD operations using a PDO connection.
    - Database: a simple SQLite-backed PDO connection located under `backend/core/Database.php`.
  - Dependency Injection: A small DI container (`backend/core/Container.php`) automatically creates objects and injects their constructor dependencies. When a Controller is requested, the container builds the Controller, its Service, and Repository dependencies, and provides a shared PDO connection for database access (Controller → Service → Repository → PDO). This makes the code modular and easier to test.

- frontend/
  - app/                     — Next.js app (pages, components)
  - package.json             — frontend dependencies and scripts

---

## Backend — API Endpoints

| Method | Endpoint         | Description                      | Request Body               | Response Example                          |
|--------|------------------|--------------------------------|----------------------------|-------------------------------------------|
| GET    | /api/todos       | Retrieve all todos              | None                       | `{ "success": true, "todos": [ ... ] }`  |
| POST   | /api/todos/add   | Create a new todo               | `{ "title": "Todo text" }` | `{ "success": true, "id": 1 }`            |
| POST/PUT | /api/todos/done  | Mark a todo item as completed   | `{ "id": 1 }`              | `{ "success": true }`                      |
| PUT    | /api/todos/update | Update an existing todo         | `{ "id": 1, "title": "New title" }` | `{ "success": true }`                |
| DELETE | /api/todos/delete | Delete a todo item              | `{ "id": 1 }`              | `{ "success": true }`                      |

---

## Database & Migrations

The backend uses SQLite for data persistence, with the database file located in `backend/db/todos.db`. The primary table is `todos`, which stores todo items with fields for id, title, completion status, and creation timestamp.

To manage database schema changes, the project includes a migration system. Migration files reside in `backend/migrations/` and define `up` and `down` methods to apply or rollback changes respectively.

The migration runner script `backend/migrate.php` loads all migration classes (excluding interfaces) and executes their `up` or `down` methods depending on the command-line argument. Running `php migrate.php` applies all migrations (creating tables, indexes, etc.), while `php migrate.php down` rolls back the latest changes.

This structured approach ensures database schema consistency and easy version control of schema changes.

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
