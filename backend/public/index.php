<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Controller\TodoController;

// 🌐 Cho phép gọi từ frontend (React, Postman, v.v.)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// ⚙️ Xử lý preflight request (CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 🚀 Khởi tạo controller
$controller = new TodoController();

// 🧭 Lấy action từ query string
$action = $_GET['action'] ?? 'list';

// 📦 Đọc dữ liệu JSON từ request body
$input = json_decode(file_get_contents('php://input'), true) ?? [];

try {
    switch ($action) {
        case 'list':
            $result = $controller->listTodos();
            echo json_encode([
                'success' => true,
                'todos' => $result  // ✅ đổi từ 'data' → 'todos'
            ]);
            break;

        case 'add':
            $title = $input['title'] ?? '';
            $id = $controller->create($title);
            echo json_encode([
                'success' => true,
                'id' => $id
            ]);
            break;

        case 'done':
            $id = (int)($input['id'] ?? 0);
            $ok = $controller->markAsDone($id);
            echo json_encode([
                'success' => $ok
            ]);
            break;

        default:
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => 'Invalid action'
            ]);
    }
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}