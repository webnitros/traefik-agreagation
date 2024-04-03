<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 03.04.2024
 * Time: 13:01
 */

namespace TraefikAgreagation\Consul;

use GuzzleHttp\Client as GuzzleClient;

class Client
{
    private string $token;
    /**
     * @var \GuzzleHttp\Client
     */
    private GuzzleClient $client;

    public function __construct(string $server, string $token)
    {

        $this->token = $token;
        // Создание экземпляра клиента Guzzle
        $client = new GuzzleClient([
            'base_uri' => $server . '/v1/', // Замените на адрес вашего Consul-сервера
        ]);
        $this->client = $client;
    }

    public function request(string $uri)
    {
        $headers = [
            'headers' => [
                'X-Consul-Token' => $this->token
            ]
        ];
        /* @var  \GuzzleHttp\Psr7\Response $response */
        $response = $this->client->request('GET', $uri, $headers);

        // Получение тела ответа в формате JSON

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Error request');
        }

        $body = $response->getBody();
        return json_decode($body, true);

    }
}
