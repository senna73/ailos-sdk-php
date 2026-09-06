<?php

declare(strict_types=1);

namespace Ailos\Sdk\Http;

final readonly class Request
{
    public function __construct(
        public string $path,
        /**
         * @var array<string, mixed>
         */
        public array $query = [],
        /**
         * @var array<string, mixed>
         */
        public array $body = [],
        /**
         * @var array<string, string>
         */
        public array $headers = [],
    ) {
    }

    public function withPath(string $path): self
    {
        return new self($path, $this->query, $this->body, $this->headers);
    }

    /**
     * @param array<string, string> $headers
     */
    public function withHeaders(array $headers): self
    {
        return new self($this->path, $this->query, $this->body, [...$this->headers, ...$headers]);
    }
}
