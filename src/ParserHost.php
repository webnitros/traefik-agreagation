<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 03.04.2024
 * Time: 13:53
 */

namespace TraefikAgreagation;


class ParserHost
{

    public static function parse($record)
    {

        if (strripos($record, '/.well-known/acme-challenge/') !== false) {
            return null;
        }

        if (strripos($record, 'PathPrefix(`/`)') !== false) {
            return null;
        }

        if (strripos($record, 'PathPrefix(`/metrics`)') !== false) {
            return null;
        }

        // Извлечение хоста из записи
        preg_match("/Host\(`([^`]+)`\)/", $record, $matches);


        if (empty($matches[1])) {
           echo '<pre>';
           print_r($record); die;
        }

        $host = $matches[1];

        // Извлечение префикса пути из записи
        preg_match_all("/PathPrefix\(`([^`]+)`\)/", $record, $matches);
        $pathPrefixes = $matches[1];

        // Формирование ссылок на сайты
        $links = [];
        if (empty($pathPrefixes)) {
            $links[] = $host;
        } else {
            foreach ($pathPrefixes as $prefix) {
                $links[] = $host . $prefix;
            }
        }

        return $links;
    }
}
