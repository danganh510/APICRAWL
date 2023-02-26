<?php

namespace Score\Repositories;

use Exception;
use Goutte\Client;
use Score\Models\ForexcecConfig;
use Phalcon\Mvc\User\Component;
use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Promise;

class CrawlerScore extends Component
{
    public $url_fb = "https://www.livescores.com";

    public function CrawlLivescores($start_time_cron, $type)
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
    public static function crawlDetailInfo($url)
    {
        $client = new Client();
        $crawler = $client->request('GET', $url);

        $info = [];
        $crawler->filter('#__livescore > div')->each(

            function (Crawler $item) use (&$info) {

                //bb là class lấy giải đấu, qb là class lấy trận đấu
                if ($item->filter("div[data-testid^='match_detail-event'] > span")->count() > 0) {
                    $temp = [
                        'time' => $item->filter("div[data-testid^='match_detail-event'] > span")->eq(0)->text(),
                        'homeText' => $item->filter("div[data-testid^='match_detail-event'] > span")->eq(1)->text(),
                        // 'homeEvent' => $item->filter("div[data-testid^='match_detail-event'] > span")->eq(2)->filter("svg")->attr("name"),
                        // 'awayText' => $item->filter("div[data-testid^='match_detail-event'] > span")->eq(5)->text(),
                        // 'awayEvent' => $item->filter("div[data-testid^='match_detail-event'] > span")->eq(4)->filter("svg")->attr("name"),
                    ];
                    if ($item->filter("div[data-testid^='match_detail-event'] > span")->eq(2)->filter("svg")->eq(0)) {
                        $temp['homeEvent'] = $item->filter("div[data-testid^='match_detail-event'] > span")->eq(2)->filter("svg")->eq(0)->attr("name");
                    }
                    if (count($item->filter("div[data-testid^='match_detail-event'] > span")->eq(2)->filter("svg")) > 1) {
                        $temp['awayEvent'] = $item->filter("div[data-testid^='match_detail-event'] > span")->eq(2)->filter("svg")->eq(1)->attr("name");
                    }
                    $away_event = count($item->filter("div[data-testid^='match_detail-event'] > span"));
                    if (count($item->filter("div[data-testid^='match_detail-event'] > span")->eq($away_event - 1))) {
                        $temp['awayText'] = $item->filter("div[data-testid^='match_detail-event'] > span")->eq($away_event - 1)->text();
                    }
                }

                //HT or FT
                if (
                    $item->filter("div[data-testid^='half-time-scores'] > span")->count() > 0 ||
                    $item->filter("div[data-testid^='full-time-scores'] > span")->count() > 0
                ) {
                    $temp = [
                        'time' => $item->filter("div > span")->eq(0)->text(),
                        'homeScore' => $item->filter("div > div > span[data-testid^='match_detail-home_score']")->text(),
                        'awayScore' => $item->filter("div > div > span[data-testid^='match_detail-away_score']")->text(),
                    ];
                }
                if (!empty($temp)) {
                    $temp['time'] =  explode("'", $temp['time'])[0];
                    $info[] = $temp;
                }
            }

        );
        return  Promise\all($info);
    }
    public static function crawlDetailTracker($url)
    {
        $client = new Client();
        $crawler = $client->request('GET', $url);

        $tracker = [];
        $crawler->filter('#__livescore > div[data-testid^="commentary_match_detail"]')->each(

            function (Crawler $item) use (&$tracker) {

                //bb là class lấy giải đấu, qb là class lấy trận đấu
                if ($item->filter("span")->count() > 1) {
                    $temp = [
                        'time' => $item->filter("span")->eq(0)->text(),
                        'content' => $item->filter("span")->eq(1)->text(),
                    ];
                    $temp['time'] =  explode("'", $temp['time'])[0];
                    $tracker[] = $temp;
                }
            }

        );
        return  Promise\all($tracker);
    }
    public function crawlDetailStarts($url)
    {
        $client = new Client();
        $crawler = $client->request('GET', $url);

        $starts = [];
        $crawler->filter('#__livescore > div[data-testid^="match_detail-statistics"]')->each(
            function (Crawler $item) use (&$starts) {
                if ($item->filter("div")->count() > 1) {
                    try {
                        $temp = [
                            'home' => $item->filter("span > span > span")->eq(0)->text(),
                            'away' => $item->filter("span > span > span")->eq(2)->text(),
                            'name' => $item->filter("span > span > div")->eq(0)->text(),
                        ];
                    } catch (Exception $e) {
                        echo $e->getMessage();
                    }
                }
                $starts[] = $temp;
            }

        );
        return  Promise\all($starts);
    }
}
