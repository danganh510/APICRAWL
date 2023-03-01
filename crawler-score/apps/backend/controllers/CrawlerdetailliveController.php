<?php

namespace Score\Backend\Controllers;

use Exception;
use GuzzleHttp\Promise\Promise;
use Score\Repositories\CrawlerDetail;
use Score\Repositories\CrawlerScore;
use Score\Repositories\MatchCrawl;


use Score\Models\ScMatchInfo;

class CrawlerdetailliveController extends ControllerBase
{

    public $type_crawl = MatchCrawl::TYPE_FLASH_SCORE;
    public function indexAction()
    {
        ini_set('max_execution_time', 20);
        $start = microtime(true);
        $is_live =  $this->request->get("isLive");
        $this->type_crawl = $this->request->get("type");
        $urlDetail = "https://www.flashscore.com/match/G67uHGC6/#/match-summary/match-summary";

        //tab: info,tracker,statistics
        $crawler = new CrawlerDetail($this->type_crawl,$urlDetail, $is_live);
        $detail = $crawler->getInstance();
        var_dump(microtime(true) - $start);
        exit;
     
        foreach ($dataResult as $id =>  $info) {
            $infoModel = new ScMatchInfo();
            $infoModel->setInfoMatchId($id);
            $infoModel->setInfoTime($info['info']);
            $infoModel->setInfoStats($info['statistics']);
            $infoModel->setInfoSummary($info['tracker']);
            $infoModel->save();
        }
        
    }
}
