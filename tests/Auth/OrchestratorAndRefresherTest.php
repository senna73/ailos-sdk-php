<?php

declare(strict_types=1);

namespace Ailos\Sdk\Tests\Auth;

use Ailos\Sdk\Auth\AuthOrchestrator;
use Ailos\Sdk\Auth\Credentials\ClientCredentials;
use Ailos\Sdk\Auth\Credentials\CooperadoCredentials;
use Ailos\Sdk\Auth\Steps\AuthenticateCooperadoStep;
use Ailos\Sdk\Auth\Steps\FetchAccessTokenStep;
use Ailos\Sdk\Auth\Steps\FetchAuthIdStep;
use Ailos\Sdk\Auth\TokenRefresher;
use Ailos\Sdk\Auth\Tokens\AccessToken;
use Ailos\Sdk\Auth\Tokens\AuthId;
use Ailos\Sdk\Auth\Tokens\JwtToken;
use Ailos\Sdk\Exceptions\AuthenticationException;
use Ailos\Sdk\Exceptions\TokenExpiredException;
use Ailos\Sdk\Http\Contracts\HttpClientInterface;
use Ailos\Sdk\Http\Environment;
use Ailos\Sdk\Storage\InMemoryTokenStore;
use Ailos\Sdk\Storage\TokenKeys;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class OrchestratorAndRefresherTest extends TestCase
{
    private InMemoryTokenStore   $store;
    private ClientCredentials    $clientCredentials;
    private CooperadoCredentials $cooperadoCredentials;
    private Environment          $environment;

    protected function setUp(): void
    {
        $this->store                = new InMemoryTokenStore();
        $this->environment          = new Environment('homologacao');
        $this->clientCredentials    = new ClientCredentials('key', 'secret');
        $this->cooperadoCredentials = new CooperadoCredentials(
            urlCallback:          'https://callback.url',
            ailosApiKeyDeveloper: 'uuid-key',
            codigoCooperativa:    '0001',
            codigoConta:          '12345',
            senha:                'senha123',
        );
    }

    // AuthOrchestrator — resolveAccessToken
    public function test_orchestrator_fetches_access_token_when_not_cached(): void
    {
        $fetchStep = $this->createMock(FetchAccessTokenStep::class);
        $fetchStep->expects($this->once())
            ->method('execute')
            ->willReturn(new AccessToken('token-abc', 3600));

        $orchestrator = $this->makeOrchestrator(fetchAccessTokenStep: $fetchStep);
        $result       = $orchestrator->resolveAccessToken($this->clientCredentials);

        $this->assertSame('token-abc', $result->value());
    }

    public function test_orchestrator_returns_cached_access_token_without_fetching(): void
    {
        $cachedToken = new AccessToken('cached-token', 3600);
        $this->store->set(TokenKeys::ACCESS_TOKEN, $cachedToken);

        $fetchStep = $this->createMock(FetchAccessTokenStep::class);
        $fetchStep->expects($this->never())->method('execute');

        $orchestrator = $this->makeOrchestrator(fetchAccessTokenStep: $fetchStep);
        $result       = $orchestrator->resolveAccessToken($this->clientCredentials);

        $this->assertSame('cached-token', $result->value());
    }

    public function test_orchestrator_fetches_new_token_when_cached_is_expired(): void
    {
        $expiredToken = new AccessToken('expired', 3600, new DateTimeImmutable('-2 hours'));
        $this->store->set(TokenKeys::ACCESS_TOKEN, $expiredToken);

        $fetchStep = $this->createMock(FetchAccessTokenStep::class);
        $fetchStep->expects($this->once())
            ->method('execute')
            ->willReturn(new AccessToken('fresh-token', 3600));

        $orchestrator = $this->makeOrchestrator(fetchAccessTokenStep: $fetchStep);
        $result       = $orchestrator->resolveAccessToken($this->clientCredentials);

        $this->assertSame('fresh-token', $result->value());
    }

    // AuthOrchestrator — resolveAuthId
    public function test_orchestrator_returns_cached_auth_id_without_fetching(): void
    {
        $cachedId = new AuthId('cached-id');
        $this->store->set(TokenKeys::AUTH_ID, $cachedId);

        $fetchIdStep = $this->createMock(FetchAuthIdStep::class);
        $fetchIdStep->expects($this->never())->method('execute');

        $orchestrator = $this->makeOrchestrator(fetchAuthIdStep: $fetchIdStep);
        $result       = $orchestrator->resolveAuthId(
            new AccessToken('token', 3600),
            $this->cooperadoCredentials,
        );

        $this->assertSame('cached-id', $result->value());
    }

    // AuthOrchestrator — storeJwt
    public function test_orchestrator_stores_jwt_correctly(): void
    {
        $orchestrator = $this->makeOrchestrator();
        $orchestrator->storeJwt('my.jwt.token');

        $jwt = $this->store->get(TokenKeys::JWT);

        $this->assertInstanceOf(JwtToken::class, $jwt);
        $this->assertSame('my.jwt.token', $jwt->value());
    }

    // TokenRefresher — getValidJwt
    public function test_refresher_returns_valid_jwt_without_refresh(): void
    {
        $validJwt = new JwtToken('valid-jwt');
        $this->store->set(TokenKeys::JWT, $validJwt);

        $refresher = $this->makeRefresher();
        $result    = $refresher->getValidJwt($this->clientCredentials, $this->cooperadoCredentials);

        $this->assertSame('valid-jwt', $result->value());
    }

    public function test_refresher_throws_when_no_jwt_in_store(): void
    {
        $this->expectException(AuthenticationException::class);

        $refresher = $this->makeRefresher();
        $refresher->getValidJwt($this->clientCredentials, $this->cooperadoCredentials);
    }

    public function test_refresher_performs_refresh_when_jwt_needs_refresh(): void
    {
        $expiringJwt = new JwtToken('expiring-jwt', new DateTimeImmutable('-26 minutes'));
        $this->store->set(TokenKeys::JWT, $expiringJwt);
        $this->store->set(TokenKeys::ACCESS_TOKEN, new AccessToken('access-token', 3600));

        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->method('get')->willReturn([
            'raw'          => 'refreshed-jwt',
            '_status_code' => 200,
        ]);

        $refresher = $this->makeRefresher(httpClient: $httpClient);
        $result    = $refresher->getValidJwt($this->clientCredentials, $this->cooperadoCredentials);

        $this->assertSame('refreshed-jwt', $result->value());
    }

    public function test_refresher_throws_when_refresh_response_is_empty(): void
    {
        $this->expectException(TokenExpiredException::class);

        $expiringJwt = new JwtToken('expiring-jwt', new DateTimeImmutable('-26 minutes'));
        $this->store->set(TokenKeys::JWT, $expiringJwt);
        $this->store->set(TokenKeys::ACCESS_TOKEN, new AccessToken('access-token', 3600));

        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->method('get')->willReturn(['_status_code' => 200]);

        $refresher = $this->makeRefresher(httpClient: $httpClient);
        $refresher->getValidJwt($this->clientCredentials, $this->cooperadoCredentials);
    }

    // Helpers
    private function makeOrchestrator(
        ?FetchAccessTokenStep      $fetchAccessTokenStep = null,
        ?FetchAuthIdStep           $fetchAuthIdStep = null,
        ?AuthenticateCooperadoStep $authenticateCooperadoStep = null,
    ): AuthOrchestrator {
        $httpClient = $this->createMock(HttpClientInterface::class);

        return new AuthOrchestrator(
            fetchAccessTokenStep:      $fetchAccessTokenStep
            ?? new FetchAccessTokenStep($httpClient, $this->environment),
            fetchAuthIdStep:           $fetchAuthIdStep
            ?? new FetchAuthIdStep($httpClient, $this->environment),
            authenticateCooperadoStep: $authenticateCooperadoStep
            ?? new AuthenticateCooperadoStep($httpClient, $this->environment),
            tokenStore:                $this->store,
        );
    }

    private function makeRefresher(?HttpClientInterface $httpClient = null): TokenRefresher
    {
        $http = $httpClient ?? $this->createMock(HttpClientInterface::class);

        return new TokenRefresher(
            httpClient:    $http,
            environment:   $this->environment,
            tokenStore:    $this->store,
            orchestrator:  $this->makeOrchestrator(),
        );
    }
}
