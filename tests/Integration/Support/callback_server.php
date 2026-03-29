<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../vendor/autoload.php';

use Ailos\Sdk\AilosSdk;
use Ailos\Sdk\Collection\Auth\Credentials\ClientCredentials;
use Ailos\Sdk\Collection\Auth\Credentials\CooperadoCredentials;
use Ailos\Sdk\Http\Environment;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../../');
$dotenv->load();

header('Access-Control-Allow-Origin: https://webhook.site');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$sdk = new AilosSdk(
    clientCredentials: new ClientCredentials(
        consumerKey:    $_ENV['AILOS_CONSUMER_KEY'],
        consumerSecret: $_ENV['AILOS_CONSUMER_SECRET'],
    ),
    cooperadoCredentials: new CooperadoCredentials(
        urlCallback:          $_ENV['AILOS_URL_CALLBACK'],
        ailosApiKeyDeveloper: $_ENV['AILOS_API_KEY_DEVELOPER'],
        codigoCooperativa:    $_ENV['AILOS_CODIGO_COOPERATIVA'],
        codigoConta:          $_ENV['AILOS_CODIGO_CONTA'],
        senha:                $_ENV['AILOS_SENHA'],
    ),
    environment: new Environment($_ENV['AILOS_ENVIRONMENT'] ?? 'homologacao'),
);

$method = $_SERVER['REQUEST_METHOD'];
$path   = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($method === 'POST' && $path === '/callback') {
    try {
        $sdk->auth->callbackHandler()->handleFromGlobals();

        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode(['status' => 'ok']);
    } catch (\Throwable $e) {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode([
            'error' => $e->getMessage(),
            'file'  => $e->getFile(),
            'line'  => $e->getLine(),
        ]);
    }
    exit;
}

http_response_code(404);
header('Content-Type: application/json');
echo json_encode([
    'error'  => 'not found',
    'method' => $method,
    'path'   => $path,
    'uri'    => $_SERVER['REQUEST_URI'],
    'body'   => file_get_contents('php://input'),
]);
