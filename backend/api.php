<?php

require_once __DIR__ . '/vendor/autoload.php';

use Controller\TodoController;

$controller = new TodoController();

header('Content-Type: application/json');

$action = $_GET['action'] ?? 'list';

switch ($action) {
    case 'add':
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $controller->add($data);
        echo json_encode(['success' => true, 'id' => $id]);
        break;
    case 'done':
        $data = json_decode(file_get_contents('php://input'), true);
        $ok = $controller->done((int)$data['id']);
        echo json_encode(['success' => $ok]);
        break;
    default:
        echo json_encode(['todos' => $controller->list()]);
        break;
}