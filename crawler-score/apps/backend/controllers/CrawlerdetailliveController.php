<?php

namespace Score\Backend\Controllers;

use Exception;
use GuzzleHttp\Promise\Promise;
use Score\Models\ScMatch;
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
        $start_time_cron = time();
        $is_live =  $this->request->get("isLive");
        $this->type_crawl = $this->request->get("type");
        if ($is_live) {
            $matchCrawl = ScMatch::findFirst([
                'match_crawl_detail = 0 AND match_status = "S"'
            ]);
        } else {
            $matchCrawl = ScMatch::findFirst([
                'match_crawl_detail = 0 AND match_status != "W"'
            ]);
        }
        if (!$matchCrawl) {
            echo "Not found Match";
            die();
        }
        echo $matchCrawl->getMatchId()."---";
        
        if ($matchCrawl->getMatchLinkDetailFlashscore() == "" || $matchCrawl->getMatchLinkDetailFlashscore() == null) {
            goto end;
        }

        $urlDetail = "https://www.flashscore.com/" . $matchCrawl->getMatchLinkDetailFlashscore() . "/#/match-summary/match-summary";
        //tab: info,tracker,statistics
        $crawler = new CrawlerDetail($this->type_crawl, $urlDetail, $is_live);
        $detail = $crawler->getInstance();
        $infoModel = ScMatchInfo::findFirst([
            'info_match_id = :id:',
            'bind' => [
                'id' => $matchCrawl->getMatchId()
            ]
        ]);
        if (!$infoModel) {
            $infoModel = new ScMatchInfo();
            $infoModel->setInfoMatchId($matchCrawl->getMatchId());
        }
        $infoModel->setInfoTime(json_encode($detail['info']));
        $infoModel->setInfoStats(json_encode($detail['start']));
        $infoModel->setInfoSummary(json_encode($detail['tracker']));

        $result = $infoModel->save();
        if ($result) {
            echo "crawl succes--";
        }
        //lưu thông tin mới của match
        if (!empty($detail['match']) && isset($detail['match']['homeScore']) && isset($detail['match']['awayScore'])) {
            $matchCrawl->setMatchHomeScore($detail['match']['homeScore']);
            $matchCrawl->setMatchAwayScore($detail['match']['awayScore']);
        }
        end: 
        $matchCrawl->setMatchCrawlDetail($matchCrawl->getMatchCrawlDetail() + 1);
        $matchCrawl->save();
        var_dump($matchCrawl->getMessages());exit;
        echo "---finish in " . (time() - $start_time_cron) . " second";
        die();
    }
}
