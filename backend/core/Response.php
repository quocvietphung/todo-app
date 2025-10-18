<?php

namespace Core;

class Response {
    public static function json(array $data, int $status = 200): string {
        http_response_code($status);
        header('Content-Type: application/json');
        return json_encode($data);
    }
}