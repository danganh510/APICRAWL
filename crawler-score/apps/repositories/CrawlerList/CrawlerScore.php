<?php

namespace Score\Repositories;

use Exception;
use Goutte\Client;
use Score\Models\ForexcecConfig;
use Phalcon\Mvc\User\Component;
use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Promise;

class CrawlerScore extends CrawlerList
{
    public $url_fb = "https://www.livescores.com";

    public function __construct($url_crawl, $day_time, $isLive)
    {
        $this->url_crawl = $url_crawl;
        $this->day_time = $day_time;
        $this->isLive = $isLive;
    }
    public function crawlList()
    {
        if ($this->isLive) {
            $param = "/football/live/?tz=7";
        } else {
            $param = "/football/{$this->day_time}/?tz=7";
        }
        $url = $this->url_crawl . $param;
        $client = new Client();
        $crawler = $client->request('GET', $url);

        $list_live_match = [];
        $crawler->filter('div[data-testid*="match_rows-root"] > div')->each(

            function (Crawler $item) use (&$list_live_tournaments, &$list_live_match) {


                //bb là class lấy giải đấu, qb là class lấy trận đấu
                if ($item->filter("div[data-testid^='category_header-wrapper'] > span")->count() > 0) {
                    $div = $item->filter("div[data-testid^='category_header-wrapper'] > span");
                    $this->list_live_tournaments[] = $this->getTournament($div);
                }
                if ($item->filter("div[data-testid^='football_match_row']")->count() > 0) {
                    $divMatch = $item->filter("div[data-testid^='football_match_row']");
                    $list_live_match[] = $this->getMatch($divMatch);
                }
                end:
            }

        );
        return $list_live_match;
    }
    public function getTournament($div)
    {
        $country = $div->eq(0)->filter("a")->eq(0)->text();
        $tournament = $div->eq(0)->filter("a")->eq(1)->text();
        $country_name = mb_ereg_replace('[^\x20-\x7E]+', '', $country);
        $tournament = $this->explodeNameTour($tournament);
        $this->round = $tournament['group'];

        $tournamentModel = new MatchTournament();
        $tournamentModel->setCountryName(strtolower($country_name));
        $tournamentModel->setTournamentName(strtolower($tournament['name']));
        $tournamentModel->setTournamentGroup(strtolower($tournament['group']));
        $tournamentModel->setId(count($this->list_live_tournaments) + 1);
        $tournamentModel->setCountryImage("");
        return $tournamentModel;
    }
    public function explodeNameTour($tournament)
    {
        $tournament = mb_ereg_replace('[^\x20-\x7E]+', '', $tournament);
        $name = explode(":", $tournament);
        return [
            'name' => trim($name[0]),
            'group' => isset($name[count($name) - 1]) ? trim($name[count($name) - 1]) : ""
        ];
    }
    public function getMatch($divMatch)
    {
        $dataMatch = [];
        $dataMatch['href_detail'] = $divMatch->filter("a")->attr('href');

        $divMatchInfo = $divMatch->filter("a > div > span");
        $dataMatch['time'] = $divMatchInfo->eq(0)->filter("span > span")->eq(0)->text();

        $dataMatch['home'] = $divMatchInfo->eq(1)->filter("span")->eq(1)->filter("span")->text();
        $dataMatch['home_score'] = $divMatchInfo->eq(1)->filter("span")->eq(4)->filter("span")->text();
        $dataMatch['away'] = $divMatchInfo->eq(1)->filter("span")->eq(8)->filter("span")->text();
        $dataMatch['away_score'] = $divMatchInfo->eq(1)->filter("span")->eq(6)->filter("span")->text();
        $liveMatch = $this->saveMatch($dataMatch);
        return $liveMatch;
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
                    ];
                    if ($item->filter("div[data-testid^='match_detail-event'] > span")->eq(2)->filter("svg")->eq(0)) {
                        if (empty($temp['homeText'])) {
                            $temp['homeEvent'] = $item->filter("div[data-testid^='match_detail-event'] > span")->eq(2)->filter("svg")->eq(0)->attr("name");
                        } else {
                            $temp['awayEvent'] = $item->filter("div[data-testid^='match_detail-event'] > span")->eq(2)->filter("svg")->eq(0)->attr("name");
                        }
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
                            'away' => $item->filter("span > span > span")->eq($item->filter("span > span > span")->count() - 1)->text(),
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
