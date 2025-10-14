<?php

header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'Todo.php';

$todo = new Todo(__DIR__ . '/db/todos.db');

$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'list':
            echo json_encode(['success' => true, 'todos' => $todo->getAll()]);
            break;

        case 'add':
            $input = json_decode(file_get_contents('php://input'), true);
            $title = $input['title'] ?? '';
            
            if (empty($title)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Title is required']);
                break;
            }
            
            $id = $todo->add($title);
            echo json_encode(['success' => true, 'id' => $id]);
            break;

        case 'done':
            $input = json_decode(file_get_contents('php://input'), true);
            $id = $input['id'] ?? null;
            
            if ($id === null) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'ID is required']);
                break;
            }
            
            $todo->markAsDone($id);
            echo json_encode(['success' => true]);
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid action']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
