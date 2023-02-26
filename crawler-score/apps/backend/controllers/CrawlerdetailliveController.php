<?php

namespace Score\Backend\Controllers;

use Exception;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Promise\PromiseInterface;

use Score\Repositories\CrawlerScore;
use Score\Repositories\MatchCrawl;
use GuzzleHttp\Promise\Utils;


use Score\Models\ScMatchInfo;
use Score\Repositories\CrawlerDetailAsyn;


class CrawlerdetailliveController extends ControllerBase
{

    public $type_crawl = MatchCrawl::TYPE_FLASH_SCORE;
    public function indexAction()
    {
        require_once(__DIR__ . "/../../library/simple_html_dom.php");
        ini_set('max_execution_time', 20);
        $start = microtime(true);
        $userUrls = [
            "https://www.livescores.com/football/champions-league/round-of-16-2022-2023/inter-vs-fc-porto/844281/?tz=7",
            "https://www.livescores.com/football/copa-libertadores/qualification/el-nacional-vs-independiente-medellin/866449/?tz=7",
            "https://www.livescores.com/football/copa-libertadores/qualification/boston-river-vs-ca-huracan/866456/?tz=7",
        ];
        //tab: info,tracker,statistics
        $promises = [];
        foreach ($userUrls as $key => $url) {
            //key sau được thay bằng match_id
            $promisesInfo = new Promise(function (PromiseInterface  $resolve) use ($url) {
                $crawler = new CrawlerScore();
                $result = $crawler->crawlDetailInfo($url . "&tab=info");
                $resolve($result);
            });
            $promisesTracker = new Promise(function (PromiseInterface  $resolve) use ($url) {
                $crawler = new CrawlerScore();
                $result = $crawler->crawlDetailTracker($url . "&tab=tracker");
                $resolve($result);
            });
            $promisesStatistics = new Promise(function (PromiseInterface  $resolve) use ($url) {
                $crawler = new CrawlerScore();
                $result = $crawler->crawlDetailStarts($url . "&tab=statistics");
                $resolve($result);
            });
            $promises[$key . "_info"] = $promisesInfo;
            $promises[$key . "_tracker"] = $promisesTracker;
            $promises[$key . "_statistics"] = $promisesStatistics;
        }

        $dataResult = [];
        //Promise\Utils::all()
        $allPromises =  Promise\Utils::all($promises);


        try {
            $results = $allPromises->wait();
        } catch (Exception $e) {
            echo $e->getMessage();
            exit;
        }
var_dump($results);exit;
        foreach ($results as $key => $data) {
            $type = explode("_", $key)[1];
            $id = explode("_", $key)[0];

            switch ($type) {
                case "info":
                    $dataResult[$id]['info'] = json_encode($data);
                    break;
                case "tracker":
                    $dataResult[$id]['tracker'] = json_encode($data);
                    break;
                case "statistics":
                    $dataResult[$id]['statistics'] = json_encode($data);
                    break;
            }
        }
        foreach ($dataResult as $id =>  $info) {
            $infoModel = new ScMatchInfo();
            $infoModel->setInfoMatchId($id);
            $infoModel->setInfoTime($info['info']);
            $infoModel->setInfoStats($info['statistics']);
            $infoModel->setInfoSummary($info['tracker']);
            $infoModel->save();
        }
        var_dump(microtime(true) - $start);
        exit;
    }
}
