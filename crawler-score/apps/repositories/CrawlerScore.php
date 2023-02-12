<?php

namespace Score\Repositories;

use Goutte\Client;
use Score\Models\ForexcecConfig;
use Phalcon\Mvc\User\Component;
use Symfony\Component\DomCrawler\Crawler;

class CrawlerScore extends Component
{
    public $url_fb = "https://www.livescores.com";
    public function CrawlSofa($start_time_cron,$type)
    {
        if ($type == "live") {
            $param = "/football/live/?tz=7";
        } else {
            $param = "/football/{$this->my->formatDateYMD($start_time_cron)}/?tz=7";

        }
        $url = $this->url_fb . $param;
        $client = new Client();
        $crawler = $client->request('GET', $url);
 
        $list_live_match = [];
        $list_live_tournaments = [];
        $index = 0;
        $tournaments = [];

        $crawler->filter('div[data-virtuoso-scroller] > div')->each(
            function (Crawler $item) use (&$list_live_tournaments, &$list_live_match) {
                //bb là class lấy giải đấu, xf là class lấy trận đấu
                if ($item->filter(".bb > span")->count() > 0) {
                    $title = $item->filter(".bb > span")->text();
                    $country = explode("-", $title)[0];
                    $tournament = explode("-", $title)[1];
                    $list_live_tournaments[] = [
                        'country' => trim($country),
                        'tournament' => trim($tournament),
                        'index' => count($list_live_tournaments),
                    ];
                }
                if ($item->filter(".xf > a")->count() > 0) {
                    $href_detail = $item->filter(".xf > a")->attr('href');
                    $time = $item->filter(".qb > a > div > .kg")->text();
                    $home = $item->filter(".qb > a > div > .bh > .ch > span")->text();
                    $home_score = $item->filter(".qb > a > div > .bh > .Zg > .hh")->text();
                    $away = $item->filter(".qb > a > div > .bh > .dh > span")->text();
                    $away_score = $item->filter(".qb > a > div > .bh > .Zg > .ih")->text();
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
    public function CrawlLivescores($start_time_cron,$type)
    {
        if ($type == "live") {
            $param = "/football/live/?tz=7";
        } else {
            $param = "/football/{$this->my->formatDateYMD($start_time_cron)}/?tz=7";

        }
        $url = $this->url_fb . $param;
        $client = new Client();
        $crawler = $client->request('GET', $url);
 
        $list_live_match = [];
        $list_live_tournaments = [];
        $index = 0;
        $tournaments = [];
        $crawler->filter('div[data-testid*="match_rows-root"] > div')->each(

            function (Crawler $item) use (&$list_live_tournaments, &$list_live_match) {


                //bb là class lấy giải đấu, qb là class lấy trận đấu
                if ($item->filter("div[data-testid^='category_header-wrapper'] > span")->count() > 0) {

                    //   $title = $item->filter(".Be > span")->text();
                    $country = $item->filter("div[data-testid^='category_header-wrapper'] > span")->eq(0)->filter("a")->eq(0)->text();
                    $tournament = $item->filter("div[data-testid^='category_header-wrapper'] > span")->eq(0)->filter("a")->eq(1)->text();

                    $list_live_tournaments[] = [
                        'country' => mb_ereg_replace('[^\x20-\x7E]+', '', $country),
                        'tournament' => mb_ereg_replace('[^\x20-\x7E]+', '', $tournament),
                        'index' => count($list_live_tournaments)
                    ];
                }

                if ($item->filter("div[data-testid^='football_match_row']")->count() > 0) {
                    $href_detail = $item->filter("div[data-testid^='football_match_row'] > a")->attr('href');

                    $time = $item->filter("div[data-testid^='football_match_row'] > a > div > span")->eq(0)->filter("span")->text();

                    $home = $item->filter("div[data-testid^='football_match_row'] > a > div > span")->eq(1)->filter("span")->eq(1)->filter("span")->text();
                    $home_score = $item->filter("div[data-testid^='football_match_row'] > a > div > span")->eq(1)->filter("span")->eq(4)->filter("span")->text();
                    $away = $item->filter("div[data-testid^='football_match_row'] > a > div > span")->eq(1)->filter("span")->eq(8)->filter("span")->text();
                    $away_score = $item->filter("div[data-testid^='football_match_row'] > a > div > span")->eq(1)->filter("span")->eq(6)->filter("span")->text();

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
    public static function CrawlDetailInfo($crawler)
    {
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
    public static function CrawlDetailTracker($crawler)
    {
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
