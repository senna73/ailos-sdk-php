<?php

declare(strict_types=1);

require __DIR__ . '/../src/Store.php';

use Ailos\Sdk\Catcher\Store;

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];
$secret = getenv('CATCHER_SECRET') ?: '';

header('Content-Type: application/json');

// Ailos chama aqui — POST com {"state": "...", "code": "..."}
if ($path === '/callback' && $method === 'POST') {
    $rawBody = file_get_contents('php://input');
    $data = json_decode($rawBody, true);

    if (json_last_error() !== JSON_ERROR_NONE || !isset($data['state'], $data['code'])) {
        http_response_code(422);
        echo json_encode(['error' => 'malformed_payload']);
        return true;
    }

    Store::put($data['state'], json_encode(['jwt' => $data['code']]));

    http_response_code(200);
    echo json_encode(['status' => 'ok']);
    return true;
}

// Seus testes consultam aqui, passando o mesmo 'state' que geraram
if ($path === '/events' && $method === 'GET') {
    $provided = $_SERVER['HTTP_X_CATCHER_SECRET'] ?? '';
    if (!hash_equals($secret, $provided)) {
        http_response_code(403);
        echo json_encode(['error' => 'forbidden']);
        return true;
    }

    $state = $_GET['state'] ?? null;
    $payload = $state ? Store::get($state) : null;

    if ($payload === null) {
        http_response_code(404);
        echo json_encode(['error' => 'not_found']);
        return true;
    }

    Store::delete($state);
    echo $payload;
    return true;
}

http_response_code(404);
echo json_encode(['error' => 'not_found']);
return true;