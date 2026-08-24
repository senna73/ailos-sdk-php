<?php

namespace Ailos\Sdk\Tests\Support;

class NgrokTunnelResolver
{
    public function __construct(
        private string $ngrokApiUrl = 'http://webhook-tunnel:4040/api/tunnels'
    ) {}

    public function getPublicUrl(int $retries = 10): string
    {
        for ($i = 0; $i < $retries; $i++) {
            $response = @file_get_contents($this->ngrokApiUrl);
            if ($response !== false) {
                $data = json_decode($response, true);
                foreach ($data['tunnels'] ?? [] as $tunnel) {
                    if ($tunnel['proto'] === 'https') {
                        return $tunnel['public_url'];
                    }
                }
            }
            sleep(1); // ngrok pode levar um instante pra subir o túnel
        }

        throw new \RuntimeException('Túnel ngrok não ficou disponível a tempo.');
    }
}