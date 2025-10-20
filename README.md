# Todo-App
Full-stack Todo App — **Next.js frontend + PHP backend with SQLite**

---

## Overview
This project is a **full-stack Todo application** developed as part of the **ARTEMEON Coding Challenge**.  

It demonstrates the ability to design a lightweight, framework-free backend in PHP, combined with a modern frontend using **Next.js (TypeScript)**.

The main goal was to show clear, maintainable architecture and clean code — not UI design perfection.

---

## Key points
- **Frontend:** Next.js (App Router) + TypeScript
- **Backend:** Plain PHP with layered OOP architecture  
  → **Controller → Service → Repository → Database (PDO)**
- **Database:** SQLite (`backend/db/todos.db`)
- **Migrations:** `backend/migrate.php` to apply or rollback schema changes
- **No frameworks:** implemented from scratch (no Laravel, no Symfony)

---

## Architecture

### Layered Design
Each layer has a single responsibility:

| Layer | Responsibility |
|-------|----------------|
| **Controller** | Receives HTTP requests, validates input, and calls the service layer. |
| **Service** | Contains business logic, validation, and calls the repository. |
| **Repository** | Performs all CRUD database operations using PDO. |
| **Database** | SQLite connection, configured once and shared. |

---

### Dependency Injection
The backend uses a **custom singleton Dependency Injection container** located in  
`backend/core/Container.php`.

When a controller is requested:
1. The container inspects its constructor using **Reflection**.
2. It automatically resolves and instantiates dependencies (Service → Repository → PDO).
3. The shared **PDO** instance ensures one database connection across the app.

This structure makes the app:
- ✅ **Modular** (each layer independent)
- ✅ **Easily testable** (dependencies can be mocked)
- ✅ **Simple to extend** (add more controllers or services with no config)

Example flow:
```
TodoController → TodoService → TodoRepository → PDO (Database)
```

---

## Project structure

```
todo-app/
│
├── backend/
│   ├── public/index.php         # HTTP entry point (routes + CORS)
│   ├── migrate.php              # Migration runner
│   ├── migrations/              # Migration files (CreateTodosTable.php)
│   ├── controller/              # Controllers (TodoController.php)
│   ├── service/                 # Business logic layer
│   ├── repository/              # Data access (PDO)
│   ├── core/                    # Container, Router, Database, Response
│   ├── db/                      # SQLite database file (todos.db)
│   └── composer.json            # Dependencies
│
└── frontend/
    ├── app/                     # Next.js App Router structure
    ├── components/              # Reusable UI components
    ├── package.json             # Frontend dependencies
    └── tsconfig.json
```

---

## Backend — API Endpoints

| Method | Endpoint | Description | Request Body | Response Example |
|:-------|:----------|:-------------|:--------------|:------------------|
| **GET** | `/api/todos` | Retrieve all todos | _None_ | `{ "success": true, "todos": [ { "id": 1, "title": "Buy milk", "completed": 0 } ] }` |
| **POST** | `/api/todos/add` | Create a new todo | `{ "title": "Buy groceries" }` | `{ "success": true, "id": 3 }` |
| **POST** | `/api/todos/done` | Mark a todo as completed | `{ "id": 3 }` | `{ "success": true }` |
| **PUT** | `/api/todos/update` | Update a todo’s title | `{ "id": 3, "title": "Updated text" }` | `{ "success": true }` |
| **DELETE** | `/api/todos/delete` | Permanently delete a todo | `{ "id": 3 }` | `{ "success": true }` |

✅ All endpoints return JSON responses and include full **CORS** headers.  
✅ `/api/todos/update` and `/api/todos/delete` now perform **real database updates and deletions**.

---

## Database & Migrations

The backend uses **SQLite** for data storage.  
Main table: `todos`

| Column | Type | Description |
|---------|------|-------------|
| `id` | INTEGER (PK) | Auto-increment primary key |
| `title` | TEXT | Todo title |
| `completed` | INTEGER | 0 = not done, 1 = done |
| `created_at` | DATETIME | Timestamp (default: current) |
| `updated_at` | DATETIME | Auto-updated on changes |

Migration example (`CreateTodosTable.php`):
```php
public function up(\PDO $pdo): void {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS todos (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            completed INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
    ");
}
```

Run migrations:
```bash
cd backend
php migrate.php
```

---

## Run backend (development)

You can run the backend in two ways: using the project's Composer scripts (recommended) or running PHP commands directly.

Option 1 — Composer scripts (recommended):

```bash
cd backend
composer install            # install PHP dependencies
composer run dump           # refresh autoload (optional)
composer run migrate        # apply migrations (same as php migrate.php)
composer run serve          # start the dev server (php -S localhost:8000 -t public)
```

Option 2 — Direct PHP (no Composer scripts):

```bash
cd backend
composer install            # still recommended to install vendor/bin/phpunit etc.
php migrate.php             # run migrations directly with PHP
php -S localhost:8000 -t public
```

✅ Server runs at [http://localhost:8000](http://localhost:8000)  
Example: `http://localhost:8000/api/todos`

---

## Run frontend (development)

```bash
cd frontend
npm install
npm run dev
```

Frontend runs at [http://localhost:3000](http://localhost:3000)  
and communicates with the backend via REST endpoints.

---

## Testing (PHPUnit)

Run backend unit tests (recommended via Composer):

```bash
cd backend
composer install
composer run test
```

Common alternatives (run directly with PHPUnit):

- Run all tests:

```bash
cd backend
./vendor/bin/phpunit -c phpunit.xml --testdox
```

Tests use the DI container so dependencies are mocked for isolated unit tests. If `vendor/bin/phpunit` is missing, run `composer install` first.

---

## Coverage

Generate an HTML coverage report (writes to `backend/coverage-report/`):

```bash
cd backend
composer install
composer run coverage
# then open the report
open backend/coverage-report/index.html
```

Coverage requires a coverage driver (e.g., Xdebug). If Xdebug is not enabled you can run:

```bash
php -d xdebug.mode=coverage ./vendor/bin/phpunit -c phpunit.coverage.xml --coverage-html coverage-report
```

To run tests without coverage use `composer run test` (from `backend/`).

---

## License
MIT © Viet Phung
