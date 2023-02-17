<?php

namespace Score\Backend\Controllers;

use Exception;
use Goutte\Client;
use GuzzleHttp\Psr7\Request;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;

use Score\Repositories\CrawlerScore;
use Score\Repositories\Team;

use Score\Models\ScMatch;
use Score\Repositories\CrawlerFlashScore;
use Score\Repositories\CrawlerSofa;
use Score\Repositories\MatchCrawl;
use Score\Repositories\MatchRepo;
use Score\Repositories\MyRepo;
use Score\Repositories\Selenium;
use Score\Repositories\Tournament;


class CrawlerdetailController extends ControllerBase
{

    public $type_crawl = MatchCrawl::TYPE_FLASH_SCORE;
    public function indexAction()
    {

        ini_set('max_execution_time', 20);

        $time_plus = $this->request->get("timePlus");
        $start_time_cron = time() + 0 * 24 * 60 * 60;
        echo "Start crawl data in " . $this->my->formatDateTime($start_time_cron) . "/n/r";

        $start_time = microtime(true);
        try {
            $crawler = new CrawlerFlashScore();
            $seleniumDriver = new Selenium($crawler->url_fb);
            //time plus = 1  crawl all to day
            $divParent = $crawler->getDivParent($seleniumDriver,$time_plus);
            $seleniumDriver->quit();
            echo ( microtime(true) - $start_time). "</br>";
        } catch (Exception $e) {
            echo $e->getMessage();
            $seleniumDriver->quit();
            die();
        }
        $total = 0;
        //start crawler
        try {
            statCrawler:
            
            $list_match = $crawler->CrawlFlashScore($divParent);
            echo ( microtime(true) - $start_time). "</br>";
            $matchRepo = new MatchRepo();
            foreach ($list_match as $match) {
                $home = Team::findByName($match->getHome(), MyRepo::create_slug($match->getHome()), $this->type_crawl);
                if (!$home) {
                    $home = Team::saveTeam($match->getHome(), $match->getAwayImg(), $this->type_crawl);
                }
                $away = Team::findByName($match->getAway(), MyRepo::create_slug($match->getAway()), $this->type_crawl);
                if (!$away) {
                    $away = Team::saveTeam($match->getAway(), $match->getAwayImg(), $this->type_crawl);
                }
                $tournament = Tournament::findByName($match->getTournament()->getTournamentName());
                if (!$tournament) {
                    $tournament = Tournament::saveTournament($match->getTournament(), $this->type_crawl);
                }
                if (!$home) {
                    echo "can't save home team";
                    continue;
                }
                if (!$away) {
                    echo "can't save away team";
                    continue;
                }
                if (!$tournament) {
                    echo "can't save tournament team";
                    continue;
                }
                $result =  $matchRepo->saveMatch($match, $home, $away, $tournament, $this->type_crawl);
                if ($result) {
                    $total++;
                    echo "Save match success --- ";
                } else {
                    echo "Save match false ---";
                }
            }
            $total++;
            // if ($total < 10) {
            //     sleep(5);
            //     goto statCrawler;
            // }
        } catch (Exception $e) {
            echo $total;
            echo $e->getMessage();
        }
        $seleniumDriver->quit();
        echo ( microtime(true) - $start_time). "</br>";
        end:
        echo "---total: ". $total;

        echo "---finish in " . (time() - $start_time_cron) . " second";
        die();
    }
}
