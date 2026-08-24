<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Ailos\Sdk\Ailos;

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($path === '/callback/jwt' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $rawBody = file_get_contents('php://input');
    $payload = json_decode($rawBody);

    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(['error' => 'invalid_json']);
        return true;
    }

    Ailos::handleJwtCallback($payload);

    http_response_code(200);
    echo json_encode(['status' => 'ok']);
    return true;
}

http_response_code(404);
echo json_encode(['error' => 'not_found']);
return true;