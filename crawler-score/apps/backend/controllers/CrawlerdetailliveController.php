<?php

namespace Score\Backend\Controllers;

use Exception;
use GuzzleHttp\Promise\Promise;
use Score\Repositories\CrawlerScore;
use Score\Repositories\MatchCrawl;


use Score\Models\ScMatchInfo;
use Score\Repositories\CrawlerDetailAsyn;

class CrawlerdetailliveController extends ControllerBase
{

    public $type_crawl = MatchCrawl::TYPE_FLASH_SCORE;
    public function indexAction()
    {
        ini_set('max_execution_time', 20);
        $start = microtime(true);
        $userUrls = [
            "https://www.livescores.com/football/champions-league/round-of-16-2022-2023/inter-vs-fc-porto/844281/?tz=7",
            "https://www.livescores.com/football/copa-libertadores/qualification/el-nacional-vs-independiente-medellin/866449/?tz=7",
            "https://www.livescores.com/football/copa-libertadores/qualification/boston-river-vs-ca-huracan/866456/?tz=7",
        ];
        //tab: info,tracker,statistics
        $promises = [];
        $crawler = new CrawlerScore();

        foreach ($userUrls as $key => $url) {

            $promises[$key . "_info"] = $crawler->crawlDetailInfo($url . "&tab=info");
            $promises[$key . "_tracker"] = $crawler->crawlDetailTracker($url . "&tab=tracker");
            $promises[$key . "_statistics"] = $crawler->crawlDetailStarts($url . "&tab=statistics");
        }

        foreach ($promises as $key => $data) {
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
