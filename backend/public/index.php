<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Core\Container;
use Core\Router;
use Core\Response;
use Controller\TodoController;

// 🌍 Cấu hình CORS & JSON
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ✅ Khởi tạo Container (Singleton)
$container = Container::getInstance();

// ✅ Container sẽ inject TodoService → TodoRepository → PDO tự động
$todoController = $container->get(TodoController::class);

// ✅ Khởi tạo Router
$router = new Router();

/**
 * -----------------------------------------------------
 * 🧭 ROUTES ĐỊNH NGHĨA CÁC API ENDPOINT CHO TODO
 * -----------------------------------------------------
 */

// 📄 GET /api/todos → Lấy danh sách tất cả todo
$router->add('/api/todos', fn() => Response::json([
    'success' => true,
    'todos' => $todoController->listTodos()
]), 'GET');

// ➕ POST /api/todos/add → Tạo mới todo
$router->add('/api/todos/add', function() use ($todoController) {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $todoController->create($input);
    return Response::json(['success' => true, 'id' => $id]);
}, 'POST');

// ✅ POST /api/todos/done → Đánh dấu hoàn thành
$router->add('/api/todos/done', function() use ($todoController) {
    $input = json_decode(file_get_contents('php://input'), true);
    $ok = $todoController->markAsDone((int)$input['id']);
    return Response::json(['success' => $ok]);
}, 'POST');

// ❌ Nếu không khớp route nào → 404
$router->dispatch();