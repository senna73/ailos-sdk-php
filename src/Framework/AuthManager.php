<?php

declare(strict_types=1);

namespace Ailos\Sdk\Framework;

use Ailos\Sdk\Entities\AccessToken;
use Ailos\Sdk\Entities\Enviroment;
use Ailos\Sdk\Entities\Jwt;
use DomainException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

final class AuthManager
{
    private FilesystemAdapter $storage;

    public ?AccessToken $accessToken;

    public ?string $id;

    public ?Jwt $jwt;

    public function __construct(
        private Enviroment $enviroment,
        private HttpClient $httpClient
    ) {
        $this->storage = Storage::storage();
    }

    public function auth(): void
    {
        $this->accessToken = $this->storage->get(
            'access_token',
            function (ItemInterface $item): AccessToken {
                $accessToken = $this->requestAccessToken();
                $item->expiresAfter($accessToken->expiresIn);
                return $accessToken;
            }
        );

        $this->id = $this->storage->get(
            'id',
            function (ItemInterface $item) {
                $id = $this->requestId();
                $item->expiresAfter(3600);
                return $id;
            }
        );

        $this->requestJwt();

        $startTime = microtime(true);

        while (true) {
            $item = $this->storage->getItem('jwt');

            if ($item->isHit()) {
                $item = $item->get();

                if (!($item instanceof Jwt)) {
                    throw new \RuntimeException('Item obtido com tipagem errada');
                }

                $this->jwt = $item;
                return;
            }

            if ((microtime(true) - $startTime) >= 30) {
                return;
                // throw new \RuntimeException('Timeout JWT search.');
            }

            usleep(200000);
        }
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
        $this->storage->deleteItem('access_token');
        $this->storage->deleteItem('id');
        $this->storage->deleteItem('jwt');

        $this->accessToken = null;
        $this->id = null;
        $this->jwt = null;
    }
}
