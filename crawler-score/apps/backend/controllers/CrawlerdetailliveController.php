<?php

namespace Score\Backend\Controllers;

use Exception;

use GuzzleHttp\Psr7\Request;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;

use Psr\Http\Message\ResponseInterface;
use Score\Repositories\CrawlerScore;
use Score\Repositories\CrawlerSofaDetail;
use Score\Repositories\Team;

use Score\Models\ScMatch;
use Score\Repositories\CrawlerFlashScore;
use Score\Repositories\CrawlerSofa;
use Score\Repositories\MatchCrawl;
use Score\Repositories\MatchRepo;
use Score\Repositories\MyRepo;
use Score\Repositories\Selenium;
use Score\Repositories\Tournament;
use GuzzleHttp\Client;
use GuzzleHttp\Promise;




class CrawlerdetailliveController extends ControllerBase
{

    public $type_crawl = MatchCrawl::TYPE_FLASH_SCORE;
    public function indexAction()
    {
        require_once(__DIR__ . "/../library/simple_html_dom.php");

        ini_set('max_execution_time', 20);
        $start = microtime(true);
        $userUrls = [
            "https://www.livescores.com/football/champions-league/round-of-16-2022-2023/inter-vs-fc-porto/844281/?tz=7",
            "https://www.livescores.com/football/copa-libertadores/qualification/el-nacional-vs-independiente-medellin/866449/?tz=7",
            "https://www.livescores.com/football/copa-libertadores/qualification/boston-river-vs-ca-huracan/866456/?tz=7",
        ];
        //tab: info,tracker,statistics
        $client = new Client();
        $promises = [];
        foreach ($userUrls as $key => $url) {
            //key sau được thay bằng match_id
            $promises[$key . '_info'] = $client->getAsync($url . "&tab=info");
            $promises[$key . '_tracker'] = $client->getAsync($url . "&tab=tracker");
            $promises[$key . '_statistics'] = $client->getAsync($url . "&tab=statistics");
        }

        $data = [];
        $results = Promise\Utils::unwrap($promises);
        foreach ($results as $key => $html) {
            $matchId = explode("_", $key)[0];
            $type = explode("_", $key)[1];
            $parentDiv =  str_get_html($html);
            if (!$parentDiv) {
                continue;
            }
            switch ($type) {
                case "info":
                case "tracker":
                case "statistics":
            }
        }
    }
}
