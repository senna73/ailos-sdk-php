<?php

declare(strict_types=1);

require __DIR__ . '/../src/Store.php';

use Ailos\Sdk\Catcher\Store;

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];
$secret = getenv('CATCHER_SECRET') ?: '';

header('Content-Type: application/json');

if ($path === '/callback' && $method === 'POST') {
    $correlationId = $_GET['correlation_id'] ?? null;
    if (!$correlationId) {
        http_response_code(400);
        echo json_encode(['error' => 'missing_correlation_id']);
        return true;
    }

    Store::put($correlationId, file_get_contents('php://input'));

    http_response_code(200);
    echo json_encode(['status' => 'ok']);
    return true;
}

if ($path === '/events' && $method === 'GET') {
    $provided = $_SERVER['HTTP_X_CATCHER_SECRET'] ?? '';
    if (!hash_equals($secret, $provided)) {
        http_response_code(403);
        echo json_encode(['error' => 'forbidden']);
        return true;
    }

    $correlationId = $_GET['correlation_id'] ?? null;
    $payload = $correlationId ? Store::get($correlationId) : null;

    if ($payload === null) {
        http_response_code(404);
        echo json_encode(['error' => 'not_found']);
        return true;
    }

    Store::delete($correlationId); // consome uma vez só, evita leitura de dado velho
    echo $payload;
    return true;
}

http_response_code(404);
echo json_encode(['error' => 'not_found']);
return true;