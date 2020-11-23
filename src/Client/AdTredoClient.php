<?php
declare(strict_types=1);

namespace Enzode\AdPanelConnector\Client;

use Symfony\Component\HttpClient\Exception\InvalidArgumentException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AdTredoClient
{
    /**
     * @var HttpClientInterface
     */
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public static function createDefault(string $endpoint, string $key): self
    {
        $client = $client = HttpClient::createForBaseUri($endpoint, [
            'headers' => [
                "X-Intredo-Application" => $key,
            ],
        ]);

        return new self($client);
    }

    public function getRefData(string $advertRef): ?array
    {
        try {
            $data = $this->client->request('GET', '/api/reflink', [
                'query' => ['ref' => $advertRef],
                'timeout' => 3,
            ])->toArray(false);

            if (isset($data['status']) && $data['status'] === 'error') {
                return null;
            }

            return $data;
        } catch (InvalidArgumentException $e) {
            throw $e;
        } catch (ExceptionInterface $e) {
            return null;
        }
    }

    public function getPath($id): ?array
    {
        try {
            $response = $this->client->request('GET', "/api/path/$id", ['timeout' => 3]);
            return $response->toArray();
        } catch (InvalidArgumentException $e) {
            throw $e;
        } catch (ExceptionInterface $e) {
            return null;
        }
    }

    public function getSiteRef(string $sSite): ?array
    {
        try {
            return $this->client->request(
                'GET',
                '/api/default_ref',
                [
                    'query' => ['page' => base64_encode($sSite)],
                ]
            )->toArray(false);
        } catch (TransportExceptionInterface $e) {
            return null;
        }
    }
}
