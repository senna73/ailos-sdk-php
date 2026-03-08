<?php

declare(strict_types=1);

namespace Ailos\Sdk\Tests\Auth\Callback;

use Ailos\Sdk\AilosSdk;
use Ailos\Sdk\Auth\Callback\CallbackHandler;
use Ailos\Sdk\Auth\Callback\CallbackPayload;
use Ailos\Sdk\Auth\Credentials\ClientCredentials;
use Ailos\Sdk\Auth\Credentials\CooperadoCredentials;
use Ailos\Sdk\Auth\Tokens\JwtToken;
use Ailos\Sdk\Exceptions\AuthenticationException;
use Ailos\Sdk\Http\Contracts\HttpClientInterface;
use Ailos\Sdk\Storage\InMemoryTokenStore;
use Ailos\Sdk\Storage\TokenKeys;
use PHPUnit\Framework\TestCase;

class CallbackTest extends TestCase
{
    // CallbackPayload — fromJson
    public function test_payload_parses_valid_json(): void
    {
        $json = json_encode([
            'code'  => 'jwt-token-value',
            'state' => 'state-abc',
        ]);

        $payload = CallbackPayload::fromJson($json);

        $this->assertSame('jwt-token-value', $payload->code());
        $this->assertSame('state-abc', $payload->state());
    }

    public function test_payload_throws_on_invalid_json(): void
    {
        $this->expectException(AuthenticationException::class);

        CallbackPayload::fromJson('not-a-json');
    }

    public function test_payload_throws_when_code_is_missing(): void
    {
        $this->expectException(AuthenticationException::class);

        CallbackPayload::fromJson(json_encode(['state' => 'abc']));
    }

    public function test_payload_throws_when_code_is_empty(): void
    {
        $this->expectException(AuthenticationException::class);

        CallbackPayload::fromArray(['code' => '', 'state' => 'abc']);
    }

    // CallbackPayload — fromArray
    public function test_payload_from_array_extracts_code_and_state(): void
    {
        $payload = CallbackPayload::fromArray([
            'code'  => 'my.jwt.token',
            'state' => 'my-state',
        ]);

        $this->assertSame('my.jwt.token', $payload->code());
        $this->assertSame('my-state', $payload->state());
    }

    public function test_payload_state_defaults_to_empty_string(): void
    {
        $payload = CallbackPayload::fromArray(['code' => 'my.jwt.token']);

        $this->assertSame('', $payload->state());
    }

    // CallbackHandler — handleFromJson
    public function test_handler_stores_jwt_from_json(): void
    {
        $store = new InMemoryTokenStore();
        $sdk   = $this->makeSdk($store);

        $handler = new CallbackHandler($sdk);
        $handler->handleFromJson(json_encode([
            'code'  => 'my.jwt.token',
            'state' => 'state-abc',
        ]));

        $jwt = $store->get(TokenKeys::JWT);

        $this->assertInstanceOf(JwtToken::class, $jwt);
        $this->assertSame('my.jwt.token', $jwt->value());
    }

    public function test_handler_returns_payload_after_processing(): void
    {
        $sdk     = $this->makeSdk();
        $handler = new CallbackHandler($sdk);

        $payload = $handler->handleFromJson(json_encode([
            'code'  => 'my.jwt.token',
            'state' => 'state-xyz',
        ]));

        $this->assertInstanceOf(CallbackPayload::class, $payload);
        $this->assertSame('state-xyz', $payload->state());
    }

    // CallbackHandler — handleFromArray
    public function test_handler_stores_jwt_from_array(): void
    {
        $store = new InMemoryTokenStore();
        $sdk   = $this->makeSdk($store);

        $handler = new CallbackHandler($sdk);
        $handler->handleFromArray([
            'code'  => 'array.jwt.token',
            'state' => 'state-123',
        ]);

        $jwt = $store->get(TokenKeys::JWT);

        $this->assertSame('array.jwt.token', $jwt->value());
    }

    // AilosSdk::callbackHandler()
    public function test_sdk_returns_callback_handler_instance(): void
    {
        $sdk     = $this->makeSdk();
        $handler = $sdk->callbackHandler();

        $this->assertInstanceOf(CallbackHandler::class, $handler);
    }

    // Helpers
    private function makeSdk(?InMemoryTokenStore $store = null): AilosSdk
    {
        $httpClient = $this->createMock(HttpClientInterface::class);

        return new AilosSdk(
            clientCredentials: new ClientCredentials('key', 'secret'),
            cooperadoCredentials: new CooperadoCredentials(
                urlCallback:          'https://callback.url',
                ailosApiKeyDeveloper: 'uuid-key',
                codigoCooperativa:    '0001',
                codigoConta:          '12345',
                senha:                'senha123',
            ),
            environment: 'homologacao',
            httpClient:  $httpClient,
            tokenStore:  $store ?? new InMemoryTokenStore(),
        );
    }
}
