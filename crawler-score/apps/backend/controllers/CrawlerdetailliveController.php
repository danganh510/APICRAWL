<?php

namespace Score\Backend\Controllers;

use Exception;
use GuzzleHttp\Promise\Promise;
use Score\Models\ScMatch;
use Score\Repositories\CrawlerDetail;
use Score\Repositories\CrawlerScore;
use Score\Repositories\MatchCrawl;


use Score\Models\ScMatchInfo;
use Score\Models\ScTeam;
use Score\Models\ScTournament;
use Score\Repositories\MyRepo;
use Score\Repositories\Team;
use Travelnercom\Repositories\CacheTeam;

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
            //nếu crawl live thì crawl các trận đấu có tournament_crawl = Y;
            // $arrTourNammentCrawlID = ScTournament::getTourIdCrawl();
            // if (empty($arrTourNammentCrawlID)) {
            //     echo "Not found tournament";
            //     die();
            // }
            //AND  FIND_IN_SET(match_tournament_id,:arrTour:)
            $this->db->begin();
            $matchCrawl = ScMatch::findFirst([
                ' match_status = "S" AND match_crawl_detail_live = "0" ',
                // 'bind' => [
                //     'arrTour' => implode(",", $arrTourNammentCrawlID)
                // ]
            ]);
            if (!$matchCrawl) {
                $sql = 'UPDATE Score\Models\ScMatch SET match_crawl_detail_live = "0" WHERE match_status = "S"';
                $this->modelsManager->executeQuery($sql);
                echo "--All restart: ";
                echo strftime(' %H:%M', time()).
                die();
            }
        } else {
            $matchCrawl = ScMatch::findFirst([
                'match_crawl_detail = 0 AND match_status != "W" '
            ]);
        }
        if (!$matchCrawl) {
            echo "Not found Match";
            die();
        }
        echo $matchCrawl->getMatchId() . "---";

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
        if (
            !empty($detail['match']) && isset($detail['match']['homeScore']) && isset($detail['match']['awayScore'])
            && is_numeric($detail['match']['homeScore']) && is_numeric($detail['match']['homeScore'])
        ) {
            $matchCrawl->setMatchHomeScore($detail['match']['homeScore']);
            $matchCrawl->setMatchAwayScore($detail['match']['awayScore']);
        }
        end:
        if ($is_live) {
            if ($matchCrawl->getMatchCrawlDetailLive() == 1) {
                $matchCrawl->setMatchCrawlDetailLive(0);
            } else {
                $matchCrawl->setMatchCrawlDetailLive(1);
            }
        } else {
            $matchCrawl->setMatchCrawlDetail($matchCrawl->getMatchCrawlDetail() + 1);
        }
        //save logo team:
        $homeTeam = ScTeam::findFirstById($matchCrawl->getMatchHomeId());
        if ($homeTeam && !$homeTeam->getTeamLogoCrawl() && !empty($detail['match']['homeLogo'])) {
            $homeTeam->setTeamLogoCrawl($detail['match']['homeLogo']);
            $homeTeam->save();
        }
        //save logo team:
        $awayTeam = ScTeam::findFirstById($matchCrawl->getMatchAwayId());
        if ($awayTeam && !$awayTeam->getTeamLogoCrawl() && !empty($detail['match']['awayLogo'])) {
            $awayTeam->setTeamLogoCrawl($detail['match']['awayLogo']);
            $awayTeam->save();
        }
        $matchCrawl->save();
        $this->db->commit();
        echo "---finish in " . (time() - $start_time_cron) . " second";
        die();
    }
}
