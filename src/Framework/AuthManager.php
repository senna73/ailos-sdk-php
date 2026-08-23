<?php

declare(strict_types=1);

namespace Ailos\Sdk\Framework;

use Ailos\Sdk\Entities\AccessToken;
use Ailos\Sdk\Entities\Enviroment;
use Ailos\Sdk\Entities\Jwt;
use Ailos\Sdk\Framework\Storage\IStorage;
use DomainException;

final class AuthManager
{
    private ?AccessToken $accessToken;

    private ?string $id;

    private ?Jwt $jwt;

    public function __construct(
        private Enviroment $enviroment,
        private HttpClient $httpClient,
        private IStorage $storage
    ) {
        $this->accessToken = $storage->get('access_token');
        $this->id = $storage->get('id');
        $this->jwt = $storage->get('jwt');
    }

    public function auth(): void
    {
        if ($this->accessToken === null || !$this->accessToken->isValid()) {
            $this->accessToken = $this->requestAccessToken();

            $this->storage->set(
                'access_token',
                $this->accessToken,
                $this->accessToken->expiresIn
            );
        }

        if ($this->id === null) {
            $this->id = $this->requestId();
            
            $this->storage->set(
                'id',
                $this->id,
                3600
            );
        }

        /**
         * Não armazenamos o JWT aqui pois ele chega em outra requisição,
         * apenas aguardamos ele chegar no storage para usarmos
         */
        if ($this->jwt === null) {
            $this->requestJwt();
    
            $startTime = microtime(true);
    
            while (true) {
                $this->jwt = $this->storage->get('jwt');
    
                if ($this->jwt != null) {
                    return;
                }
    
                if ((microtime(true) - $startTime) >= 30) {
                    return;
                    // throw new \RuntimeException('Timeout JWT search.');
                }
    
                usleep(200000);
            }
        }

        // JWT pode existir mas estar invalido, então vamos fazer refresh
        if (!$this->jwt->isValid()) {
            $jwt = $this->requestJwtRefresh();

            $this->storage->set(
                'jwt',
                $jwt,
                3600
            );
        }

        return;
    }

    private function requestAccessToken(): AccessToken
    {
        $response = $this->httpClient->post(
            $this->enviroment->baseUrl . '/token',
            [
                'Authorization' => 'Basic ' . base64_encode(
                    "{$this->enviroment->consumerKey}:{$this->enviroment->consumerSecret}"
                ),
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            [
                'grant_type' => 'client_credentials',
            ]
        );

        if (!($response instanceof \stdClass)) {
            throw new DomainException('Tipo de retorno incorreto');
        }

        return AccessToken::fromObject($response);
    }

    private function requestId(): string
    {
        if ($this->accessToken === null) {
            throw new \RuntimeException('AccessToken não gerado.');
        }

        $response = $this->httpClient->post(
            $this->enviroment->baseUrl . '/ailos/identity/api/v1/autenticacao/login/obter/id',
            [
                'Content-Type' => 'application/json',
                'Accept' => 'text/plain',
                'Authorization' => 'Bearer ' . $this->accessToken->accessToken,
            ],
            [
                'urlCallback' => $this->enviroment->urlCallback,
                'ailosApiKeyDeveloper' => $this->enviroment->developerKey,
                'state' => 'sdk',
            ]
        );

        if (!is_string($response)) {
            throw new DomainException('Tipo de retorno incorreto');
        }

        return $response;
    }

    private function requestJwt(): void
    {
        if ($this->accessToken === null) {
            throw new \RuntimeException('AccessToken não gerado.');
        }

        $response = $this->httpClient->post(
            $this->enviroment->baseUrl . "/ailos/identity/api/v1/login/index?id={$this->id}",
            [
                'Authorization' => 'Bearer ' . $this->accessToken->accessToken,
            ],
            [
                'Login.CodigoCooperativa' => $this->enviroment->codigoCooperativa,
                'Login.CodigoConta' => $this->enviroment->codigoConta,
                'Login.Senha' => $this->enviroment->senha,
            ]
        );

        if (!is_string($response)) {
            throw new DomainException('Tipo de retorno incorreto');
        }

        libxml_use_internal_errors(true);

        $dom = new \DOMDocument();
        $dom->loadHTML($response);

        $xpath = new \DOMXPath($dom);

        $nodes = $xpath->query("//div[contains(@class,'validation-summary-errors')]//li");

        if ($nodes instanceof \DOMNodeList && $nodes->length > 0) {
            $firstNode = $nodes->item(0);

            if ($firstNode instanceof \DOMNode) {
                $message = trim($firstNode->textContent);

                if ($message !== '') {
                    throw new \RuntimeException('Erro ao tentar gerar o JWT. ' . $message);
                }
            }
        }
    }

    private function requestJwtRefresh(): Jwt
    {
        $response = $this->httpClient->get(
            $this->enviroment->baseUrl . "/ailos/identity/api/v1/autenticacao/token/refresh?code={$this->jwt->code}",
            [
                'Authorization' => 'Bearer ' . $this->accessToken->accessToken,
            ]
        );

        return new Jwt($this->jwt->state, $response);
    }

    /**
     * @return array{
     *  x-ailos-authentication: string,
     *  Authorization: string
     * }
     */
    public function getAuthHeader(): array
    {
        if ($this->jwt == null) {
            throw new \RuntimeException('JWT não gerado. Chame o método auth() antes de fazer requisições.');
        }

        if ($this->accessToken == null) {
            throw new \RuntimeException('AccessToken não gerado. Chame o método auth() antes de fazer requisições.');
        }

        return [
            'x-ailos-authentication' => $this->jwt->code,
            'Authorization' => $this->accessToken->tokenType . ' ' . $this->accessToken->accessToken,
        ];
    }

    public function logout(): void
    {
        $this->storage->delete('access_token');
        $this->storage->delete('id');
        $this->storage->delete('jwt');

        $this->accessToken = null;
        $this->id = null;
        $this->jwt = null;
    }
}