<?php

declare(strict_types=1);

namespace Ailos\Sdk\Framework;

use Ailos\Sdk\Entities\AccessToken;
use Ailos\Sdk\Entities\Enviroment;
use Ailos\Sdk\Entities\Jwt;
use Ailos\Sdk\Entities\SdkConfig;
use DomainException;

final class AuthManager
{
    public function __construct(private Enviroment $enviroment, private SdkConfig $config)
    {
    }

    public function getAccessToken(): ?AccessToken
    {
        $accessToken = $this->config->storage->get('access_token');

        if ($accessToken !== null && !($accessToken instanceof AccessToken)) {
            throw new DomainException('Tipo de access token armazenado incorreto');
        }

        return $accessToken;
    }

    public function setAccessToken(AccessToken $accessToken): void
    {
        $this->config->storage->set('access_token', $accessToken, $accessToken->expiresIn);
    }

    public function deleteAccessToken(): void
    {
        $this->config->storage->delete('access_token');
    }

    public function getId(): ?string
    {
        $id = $this->config->storage->get('id');

        if ($id !== null && !is_string($id)) {
            throw new DomainException('Tipo de ID armazenado incorreto');
        }

        return $id;
    }

    public function setId(string $id): void
    {
        $this->config->storage->set('id', $id, 3600);
    }

    public function deleteId(): void
    {
        $this->config->storage->delete('id');
    }

    public function getState(): ?string
    {
        $state = $this->config->storage->get('state');

        if ($state !== null && !is_string($state)) {
            throw new DomainException('Tipo de estado armazenado incorreto');
        }

        return $state;
    }

    public function setState(string $state): void
    {
        $this->config->storage->set('state', $state, 3600);
    }

    public function deleteState(): void
    {
        $this->config->storage->delete('state');
    }

    public function getJwt(): ?Jwt
    {
        $jwt = $this->config->storage->get('jwt');

        if ($jwt !== null && !($jwt instanceof Jwt)) {
            throw new DomainException('Tipo de JWT armazenado incorreto');
        }

        return $jwt;
    }

    public function setJwt(Jwt $jwt): void
    {
        $this->config->storage->set('jwt', $jwt, 3600);
    }

    public function deleteJwt(): void
    {
        $this->config->storage->delete('jwt');
    }

    public function auth(bool $useCatcherService = false): void
    {
        $accessToken = $this->getAccessToken();

        if ($accessToken === null || !$accessToken->isValid()) {
            $accessToken = $this->requestAccessToken();
            $this->setAccessToken($accessToken);
        }

        $id = $this->getId();
        if ($id === null) {
            $id = $this->requestId($accessToken);
            $this->setId($id);
        }

        $jwt = $this->getJwt();

        /**
         * Não armazenamos o JWT aqui pois ele chega em outra requisição,
         * apenas aguardamos ele chegar no storage para usarmos
         */
        if ($jwt === null) {
            $this->requestJwt($accessToken, $id);
            $jwt = $useCatcherService ? $this->waitForJwtViaCatcher() : $this->waitForJwtViaStorage();

            $this->setJwt($jwt);
        }

        if (!$jwt->isValid()) {
            $jwt = $this->requestJwtRefresh($accessToken, $jwt);
            $this->setJwt($jwt);
        }

        return;
    }

    private function waitForJwtViaStorage(): Jwt
    {
        $startTime = microtime(true);

        while (true) {
            $jwt = $this->getJwt();

            if ((microtime(true) - $startTime) >= 30) {
                throw new \RuntimeException('Tempo para receber o JWT excedido.');
            }

            usleep(200000);
        }
    }

    private function waitForJwtViaCatcher(): Jwt
    {
        $startTime = microtime(true);

        while (true) {
            try {
                return $this->requestCatcher();
            } catch (\Throwable $e) {
                // 404 esperado enquanto o callback ainda não chegou; ignora e tenta de novo
            }

            if ((microtime(true) - $startTime) >= 30) {
                throw new \RuntimeException('Tempo para receber o JWT via catcher excedido.');
            }

            usleep(200000);
        }
    }

    private function requestAccessToken(): AccessToken
    {
        $response = $this->config->http->post(
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

        return AccessToken::fromArray($response->json());
    }

    private function requestId(AccessToken $accessToken): string
    {
        $state = bin2hex(random_bytes(16));

        $response = $this->config->http->post(
            $this->enviroment->baseUrl . '/ailos/identity/api/v1/autenticacao/login/obter/id',
            [
                'Content-Type' => 'application/json',
                'Accept' => 'text/plain',
                'Authorization' => 'Bearer ' . $accessToken->accessToken,
            ],
            [
                'urlCallback' => $this->enviroment->urlCallback,
                'ailosApiKeyDeveloper' => $this->enviroment->developerKey,
                'state' => $state,
            ]
        );

        $this->setState($state);

        return $response->getBody();
    }

    private function requestJwt(AccessToken $accessToken, string $id): void
    {
        $response = $this->config->http->post(
            $this->enviroment->baseUrl . '/ailos/identity/api/v1/login/index?id=' . rawurlencode($id),
            [
                'Authorization' => 'Bearer ' . $accessToken->accessToken,
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            [
                'Login.CodigoCooperativa' => $this->enviroment->codigoCooperativa,
                'Login.CodigoConta' => $this->enviroment->codigoConta,
                'Login.Senha' => $this->enviroment->senha,
            ]
        );

        $data = $response->getBody();

        if ($data === '') {
            throw new \RuntimeException('Resposta vazia ao tentar gerar o JWT.');
        }

        libxml_use_internal_errors(true);

        $dom = new \DOMDocument();
        $dom->loadHTML($data);

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

        $successNodes = $xpath->query("//h4[contains(normalize-space(.), 'Login realizado com sucesso')]");

        if ($successNodes instanceof \DOMNodeList && $successNodes->length > 0) {
            return;
        }

        throw new \RuntimeException('Resposta inesperada ao tentar gerar o JWT.');
    }

    private function requestCatcher(): Jwt
    {
        $state = $this->getState();
        $catcherUrl = $this->enviroment->catcherUrl;

        if ($state === null) {
            throw new \RuntimeException('Estado não gerado. Chame o método auth() antes de aguardar o JWT.');
        }

        if ($catcherUrl === null) {
            throw new \RuntimeException('URL do catcher não configurada.');
        }

        $response = $this->config->http->get(
            $catcherUrl,
            ['X-Catcher-Secret' => $this->enviroment->catcherSecret],
            ['state' => $state]
        );

        return Jwt::fromArray($response->json());
    }

    private function requestJwtRefresh(AccessToken $accessToken, Jwt $jwt): Jwt
    {
        $response = $this->config->http->get(
            $this->enviroment->baseUrl . "/ailos/identity/api/v1/autenticacao/token/refresh?code={$jwt->code}",
            [
                'Authorization' => 'Bearer ' . $accessToken->accessToken,
            ]
        );

        return new Jwt($jwt->state, $response->getBody());
    }

    /**
     * @return array{
     *  x-ailos-authentication: string,
     *  Authorization: string
     * }
     */
    public function getAuthHeader(): array
    {
        $jwt = $this->getJwt();
        $accessToken = $this->getAccessToken();

        if ($jwt == null) {
            throw new \RuntimeException('JWT não gerado. Chame o método auth() antes de fazer requisições.');
        }

        if ($accessToken == null) {
            throw new \RuntimeException('AccessToken não gerado. Chame o método auth() antes de fazer requisições.');
        }

        return [
            'x-ailos-authentication' => $jwt->code,
            'Authorization' => $accessToken->tokenType . ' ' . $accessToken->accessToken,
        ];
    }

    public function logout(): void
    {
        $this->deleteAccessToken();
        $this->deleteId();
        $this->deleteState();
        $this->deleteJwt();
    }
}
