<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 03.04.2024
 * Time: 13:01
 */

namespace TraefikAgreagation\Traefik;

use GuzzleHttp\Client as GuzzleClient;

class Client
{
    /**
     * @var \GuzzleHttp\Client
     */
    private GuzzleClient $client;

    public function __construct(string $username, string $password)
    {
        $client = new GuzzleClient([
            'auth' => [$username, $password],
        ]);
        $this->client = $client;
    }

    public function request(string $uri)
    {


        /* @var  \GuzzleHttp\Psr7\Response $response */
        $response = $this->client->request('GET', $uri);

        // Получение тела ответа в формате JSON

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Error request');
        }

        $body = $response->getBody();
        return json_decode($body, true);

    }



}
