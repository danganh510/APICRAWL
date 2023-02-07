<?php

namespace Score\Repositories;

use Score\Models\ForexcecConfig;
use Phalcon\Mvc\User\Component;
use Symfony\Component\DomCrawler\Crawler;
use Goutte\Client;

class CrawlerSofa extends Component
{
    public function getDataList($url) {
        $client = new Client();
        $crawler = $client->request('GET', $url);
        var_dump($crawler);
        exit;
    }

}
 