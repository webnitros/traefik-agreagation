<?php

use Symfony\Component\Dotenv\Dotenv;
use TraefikAgreagation\ParserHost;

require 'vendor/autoload.php'; // Подключение Guzzle


$dotenv = new Dotenv(true);
$dotenv->loadEnv('.env');


// Создание экземпляра клиента Guzzle
$ClientConsul = new \TraefikAgreagation\Consul\Client(getenv('CONSUL_SERVER'), getenv('CONSUL_TOKEN'));
$ClientTraefik = new \TraefikAgreagation\Traefik\Client(getenv('AGENT_USERNAME'), getenv('AGENT_PASSWORD'));
// Выполнение запроса к Consul API для получения сервисов с тегом "type=traefik"
$nodes = $ClientConsul->request('catalog/nodes');


$traefik = null;

foreach ($nodes as $node) {
    $Node = $node['Node'];

    $data = [];
    switch ($Node) {
        case 'agent-consul-dev-176':
            break;
        default:
            $response = $ClientConsul->request("catalog/node/{$Node}");
            if (!empty($response['Services'])) {

                foreach ($response['Services'] as $service) {
                    $ServiceName = $service['Service'];

                    if ($ServiceName === 'traefik') {
                        if (!empty($service['Meta']['type']) && $service['Meta']['type'] == 'traefik') {


                            $router = $service['Meta']['traefik_api'];
                            $urls = [];
                            if (!empty($router)) {

                                $url = "https://{$router}/http/routers?per_page=9999";
                                $services = $ClientTraefik->request($url);
                                foreach ($services as $service) {
                                    $rule = $service['rule'];
                                    if ($domains = ParserHost::parse($rule)) {
                                        $urls = array_merge($urls, $domains);
                                    }
                                }
                            }

                            $data = [
                                'Node' => $Node,
                                'Address' => $node['Address'],
                                'TraefikApi' => $router,
                                'Urls' => $urls,
                            ];
                        }
                    }
                }
            }
            break;
    }

    if ($data) {
        $traefik[] = $data;
    }

}

echo '<pre>';
print_r($traefik);
die;



// Получение тела ответа в формате JSON
