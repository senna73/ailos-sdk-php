<?php

declare(strict_types=1);

namespace Ailos\Sdk\Cobranca\Auth;

use Ailos\Sdk\Config\AilosContext;
use Ailos\Sdk\Http\Request;
use DomainException;

final class Auth
{
    public function __construct(private AilosContext $context)
    {
    }

    public function getAccessToken(): ?AccessToken
    {
        $accessToken = $this->context->config->storage->get('access_token');

        if ($accessToken !== null && !($accessToken instanceof AccessToken)) {
            throw new DomainException('Tipo de access token armazenado incorreto');
        }

        return $accessToken;
    }

    public function setAccessToken(AccessToken $accessToken): void
    {
        $this->context->config->storage->set('access_token', $accessToken, $accessToken->expiresIn);
    }

    public function deleteAccessToken(): void
    {
        $this->context->config->storage->delete('access_token');
    }

    public function getId(): ?string
    {
        $id = $this->context->config->storage->get('id');

        if ($id !== null && !is_string($id)) {
            throw new DomainException('Tipo de ID armazenado incorreto');
        }

        return $id;
    }

    public function setId(string $id): void
    {
        $this->context->config->storage->set('id', $id, 3600);
    }

    public function deleteId(): void
    {
        $this->context->config->storage->delete('id');
    }

    public function getState(): ?string
    {
        $state = $this->context->config->storage->get('state');

        if ($state !== null && !is_string($state)) {
            throw new DomainException('Tipo de estado armazenado incorreto');
        }

        return $state;
    }

    public function setState(string $state): void
    {
        $this->context->config->storage->set('state', $state, 3600);
    }

    public function deleteState(): void
    {
        $this->context->config->storage->delete('state');
    }

    public function getJwt(): ?Jwt
    {
        $jwt = $this->context->config->storage->get('jwt');

        if ($jwt !== null && !($jwt instanceof Jwt)) {
            throw new DomainException('Tipo de JWT armazenado incorreto');
        }

        return $jwt;
    }

    public function setJwt(Jwt $jwt): void
    {
        $this->context->config->storage->set('jwt', $jwt, 3600);
    }

    public function deleteJwt(): void
    {
        $this->context->config->storage->delete('jwt');
    }

    public function auth(): void
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
            $jwt = $this->context->config->catcherService ? $this->waitForJwtViaCatcher() : $this->waitForJwtViaStorage();

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
            $jwt = $this->requestCatcher();

            if ($jwt !== null) {
                return $jwt;
            }

            if ((microtime(true) - $startTime) >= 30) {
                throw new \RuntimeException('Tempo para receber o JWT via catcher excedido.');
            }

            usleep(200000);
        }
    }

    private function requestAccessToken(): AccessToken
    {
        $authorization = 'Basic ' . base64_encode(
            "{$this->context->environment->consumerKey}:{$this->context->environment->consumerSecret}"
        );

        $request = new Request(
            path: $this->context->environment->baseUrl . '/token',
            headers: [
                'Authorization' => $authorization,
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            body: [
                'grant_type' => 'client_credentials',
            ]
        );

        $response = $this->context->config->http->post($request);

        return AccessToken::fromArray($response->json());
    }

    private function requestId(AccessToken $accessToken): string
    {
        $state = bin2hex(random_bytes(16));

        $this->setState($state);

        $request = new Request(
            path: $this->context->environment->baseUrl . '/ailos/identity/api/v1/autenticacao/login/obter/id',
            headers: [
                'Content-Type' => 'application/json',
                'Accept' => 'text/plain',
                'Authorization' => 'Bearer ' . $accessToken->accessToken,
            ],
            body: [
                'urlCallback' => $this->context->environment->urlCallback,
                'ailosApiKeyDeveloper' => $this->context->environment->developerKey,
                'state' => $state,
            ]
        );

        return $this->context->config->http->post($request)->text();
    }

    private function requestJwt(AccessToken $accessToken, string $id): void
    {
        $request = new Request(
            path: $this->context->environment->baseUrl . '/ailos/identity/api/v1/login/index?id=' . rawurlencode($id),
            headers: [
                'Authorization' => 'Bearer ' . $accessToken->accessToken,
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            body: [
                'Login.CodigoCooperativa' => $this->context->environment->codigoCooperativa,
                'Login.CodigoConta' => $this->context->environment->codigoConta,
                'Login.Senha' => $this->context->environment->senha,
            ]
        );

        $response = $this->context->config->http->post($request)->text();

        if ($response === '') {
            throw new \RuntimeException('Resposta vazia ao tentar gerar o JWT.');
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

        $successNodes = $xpath->query("//h4[contains(normalize-space(.), 'Login realizado com sucesso')]");

        if ($successNodes instanceof \DOMNodeList && $successNodes->length > 0) {
            return;
        }

        throw new \RuntimeException('Resposta inesperada ao tentar gerar o JWT.');
    }

    private function requestCatcher(): ?Jwt
    {
        $state = $this->getState();

        if ($state === null) {
            throw new \RuntimeException('Estado não gerado. Chame o método auth() antes de aguardar o JWT.');
        }

        $catcherUrl = $this->context->config->catcherUrl;

        if ($catcherUrl === null) {
            throw new \RuntimeException('URL do catcher não configurada.');
        }

        $request = new Request(
            path: $catcherUrl,
            headers: [
                'X-Catcher-Secret' => (string) $this->context->config->catcherSecret,
            ],
            query: [
                'state' => $state,
            ]
        );

        $response = $this->context->config->http->get($request)->json();

        if ($response['code'] == null) {
            return null;
        }

        return Jwt::fromArray([
            'state' => $state,
            'code' => $response['code'],
        ]);
    }

    private function requestJwtRefresh(AccessToken $accessToken, Jwt $jwt): Jwt
    {
        $request = new Request(
            path: $this->context->environment->baseUrl . "/ailos/identity/api/v1/autenticacao/token/refresh?code={$jwt->code}",
            headers: [
                'Authorization' => 'Bearer ' . $accessToken->accessToken,
            ]
        );

        $response = $this->context->config->http->get($request)->text();

        return new Jwt($jwt->state, $response);
    }

    public function logout(): void
    {
        $this->deleteAccessToken();
        $this->deleteId();
        $this->deleteState();
        $this->deleteJwt();
    }
}
