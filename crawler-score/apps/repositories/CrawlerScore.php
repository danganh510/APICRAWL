<?php

namespace Score\Repositories;

use Score\Models\ForexcecConfig;
use Phalcon\Mvc\User\Component;
use Symfony\Component\DomCrawler\Crawler;

class CrawlerScore extends Component
{    
    public static function CrawlSofa($crawler){
        $list_live_match = [];
        $list_live_tournaments = [];
        $index = 0;  
        $tournaments = [];

         $crawler->filter('.xb > div')->each(
            function (Crawler $item) use (&$list_live_tournaments,&$list_live_match) {
                //bb là class lấy giải đấu, xf là class lấy trận đấu
                if ($item->filter(".bb > span")->count() > 0) {
                    $title = $item->filter(".bb > span")->text();
                    $country = explode("-",$title)[0];
                    $tournament = explode("-",$title)[1];
                    $list_live_tournaments[] = [
                        'country' => trim($country),
                        'tournament' => trim($tournament),
                        'index' => count($list_live_tournaments),
                    ];
                }
                if ($item->filter(".xf > a")->count() > 0) {
                    $href_detail = $item->filter(".xf > a")->attr('href');
                    $time = $item->filter(".xf > a > div > .Kg")->text();
                    $home = $item->filter(".xf > a > div > .bh > .ch > span")->text();
                    $home_score = $item->filter(".xf > a > div > .bh > .Zg > .hh")->text();
                    $away = $item->filter(".xf > a > div > .bh > .dh > span")->text();
                    $away_score = $item->filter(".xf > a > div > .bh > .Zg > .ih")->text();
                    $list_live_match[] = [
                        'time' => trim($time),
                        'home' => trim($home),
                        'home_score' => trim($home_score),
                        'away' => trim($away),
                        'away_score' => trim($away_score),
                        'href_detail' => trim($href_detail),
                        'tournament' => $list_live_tournaments[count($list_live_tournaments) - 1]
                    ];
                }
                end:
            }

        );
        return $list_live_match;
    }
    public static function CrawlLivescores($crawler){
        $list_live_match = [];
        $list_live_tournaments = [];
        $index = 0;  
        $tournaments = [];

         $crawler->filter('.xb > div')->each(
            function (Crawler $item) use (&$list_live_tournaments,&$list_live_match) {
                //bb là class lấy giải đấu, xf là class lấy trận đấu
                if ($item->filter(".bb > span")->count() > 0) {
                    $title = $item->filter(".bb > span")->text();
                    $country = explode("-",$title)[0];
                    $tournament = explode("-",$title)[1];
                    $list_live_tournaments[] = [
                        'country' => mb_ereg_replace('[^\x20-\x7E]+', '', $country),
                        'tournament' => mb_ereg_replace('[^\x20-\x7E]+', '', $tournament) ,
                        'index' => count($list_live_tournaments),
                    ];
               
                }
                if ($item->filter(".xf > a")->count() > 0) {
                    $href_detail = $item->filter(".xf > a")->attr('href');
                    $time = $item->filter(".xf > a > div > .Kg")->text();
                    $home = $item->filter(".xf > a > div > .bh > .ch > span")->text();
                    $home_score = $item->filter(".xf > a > div > .bh > .Zg > .hh")->text();
                    $away = $item->filter(".xf > a > div > .bh > .dh > span")->text();
                    $away_score = $item->filter(".xf > a > div > .bh > .Zg > .ih")->text();
                    $list_live_match[] = [
                        'time' => trim($time),
                        'home' => trim($home),
                        'home_score' => trim($home_score),
                        'away' => trim($away),
                        'away_score' => trim($away_score),
                        'href_detail' => trim($href_detail),
                        'tournament' => $list_live_tournaments[count($list_live_tournaments) - 1]
                    ];
                }
                end:
            }

        );
        return $list_live_match;
    }
    public static function CrawlDetailInfo($crawler){
        $infoLive = [];
        $index = 0;  
        $tournaments = [];
        $label =  $crawler->filter('#__livescore > .Lb')->text();
         $crawler->filter('#__livescore > .Db')->each(
            function (Crawler $item) use (&$infoLive) {
                //bb là class lấy giải đấu, xf là class lấy trận đấu
                if ($item->filter(".Eb")->count() > 0) {
                    $time = $item->filter(".Eb")->text();
                    $home_name = "";
                    $home_name_second = "";
                    $away_name_second = "";
                    $away_name = "";

                    if ($item->filter(".Fb > .Ib")->count()) {
                        $home_name = $item->filter(".Fb > .Ib")->text();
                    }
                    if ($item->filter(".Fb > .Hb")->count()) {
                        $home_name_second = $item->filter(".Fb > .Hb")->text();
                    }
                    if ($item->filter(".Gb > .Ib")->count()) {
                        $away_name = $item->filter(".Gb > .Ib")->text();
                    }
                    if ($item->filter(".Gb > .Hb")->count()) {
                        $away_name_second = $item->filter(".Gb > .Hb")->text();
                    }
                    $action = $item->filter(".Jb")->html();
                    $infoLive[] = [
                        'time' => trim($time),
                        'home_name' => trim($home_name),
                        'home_name_second' => trim($home_name_second),
                        'away_name' => trim($away_name),
                        'away_name_second' => trim($away_name_second),
                        'action' => trim($action),
                    ];
                }
              
                end:
            }

        );
        return $infoLive;
    }
    public static function CrawlDetailTracker($crawler){
        $infoTracker = [];
        $index = 0;  
        $tournaments = [];
 
         $crawler->filter('.lf')->each(
            function (Crawler $item) use (&$infoTracker) {
                //bb là class lấy giải đấu, xf là class lấy trận đấu
                if ($item->filter(".mf")->count()) {
                    $time = $item->filter(".mf")->text();
                    $content = $item->filter(".nf")->text();
                    $infoTracker[] = [
                        'time' => trim($time),
                        'content' => trim($content),
                    ];
                }
 
            }

        );
        return $infoTracker;
    }
}
 