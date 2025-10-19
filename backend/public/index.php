<?php
/**
 * Application Entry Point
 * ---------------------------------------------------------
 * This file serves as the HTTP entry point for the Todo API.
 * It configures CORS headers, initializes the dependency container,
 * sets up routes, and dispatches incoming requests to the appropriate controller.
 *
 * Architecture Overview:
 * - Follows a layered OOP design (Controller → Service → Repository → Database)
 * - Uses a custom lightweight Dependency Injection Container
 * - Implements RESTful API endpoints for Todo operations
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Core\Container;
use Core\Router;
use Core\Response;
use Controller\TodoController;

/**
 * ---------------------------------------------------------
 * HTTP & CORS CONFIGURATION
 * ---------------------------------------------------------
 * Allow cross-origin requests and enforce JSON communication.
 */
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Handle preflight CORS requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

/**
 * ---------------------------------------------------------
 * DEPENDENCY INJECTION SETUP
 * ---------------------------------------------------------
 * The container automatically resolves and injects all dependencies.
 * Example:
 * TodoController → TodoService → TodoRepository → PDO
 */
$container = Container::getInstance();
$todoController = $container->get(TodoController::class);

/**
 * ---------------------------------------------------------
 * ROUTER INITIALIZATION
 * ---------------------------------------------------------
 * The Router is responsible for mapping URIs to controller methods.
 */
$router = new Router();

/**
 * GET /api/todos
 * Fetches all todo items.
 */
$router->add('/api/todos', fn() => Response::json([
    'success' => true,
    'todos' => $todoController->listTodos(),
]), 'GET');

/**
 * POST /api/todos/add
 * Creates a new todo item.
 */
$router->add('/api/todos/add', function() use ($todoController) {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $todoController->create($input);
    return Response::json(['success' => true, 'id' => $id]);
}, 'POST');

/**
 * POST /api/todos/done
 * Marks a todo item as completed.
 */
$router->add('/api/todos/done', function() use ($todoController) {
    $input = json_decode(file_get_contents('php://input'), true);
    $ok = $todoController->markAsDone((int) $input['id']);
    return Response::json(['success' => $ok]);
}, 'POST');

/**
 * Dispatch the incoming request to the appropriate route.
 * If no route matches, a 404 JSON response is returned.
 */
$router->dispatch();