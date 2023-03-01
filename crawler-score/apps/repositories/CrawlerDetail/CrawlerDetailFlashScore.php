<?php

namespace Score\Repositories;

use DOMDocument;
use Exception;
use Facebook\WebDriver\WebDriver;
use Facebook\WebDriver\WebDriverBy;
use Phalcon\Mvc\User\Component;

use Symfony\Component\DomCrawler\Crawler;

class CrawlerDetailFlashScore extends CrawlerList
{
    public function __construct($seleniumDriver, $url_crawl, $day_time, $isLive)
    {
        $this->seleniumDriver = $seleniumDriver;
        $this->url_crawl = $url_crawl;
        $this->isLive = $isLive;
    }
    /**
     * @ $this->seleniumDriver Selenium
     */
    public function getDivParent()
    { #/match-summary/match-statistics
        try {
            //$html = $this->seleniumDriver->getPageSource();
            //  $this->seleniumDriver->clickButton('.filters__tab > .filters');
            echo $this->getDivInfo();
            $this->getDivStart();
            $this->getDivTracker();

        } catch (Exception $e) {
            echo $e->getMessage();
        }
        echo $this->seleniumDriver->checkRam();
        $this->seleniumDriver->quit();
    }
    public function getDivInfo()
    {
        $parentDiv = $this->seleniumDriver->findElement('div[id="detail"]');

        $htmlDivInfo = $parentDiv->getAttribute("outerHTML");

        $htmlDivInfo = "<!DOCTYPE html>" . $htmlDivInfo;
        //khai bao cho the svg
        $htmlDivInfo = str_replace("<svg ", "<svg xmlns='http://www.w3.org/2000/svg'", $htmlDivInfo);
        MyRepo::saveText($htmlDivInfo, "info");
        return $htmlDivInfo;
    }
    public function getDivStart()
    {
        $this->seleniumDriver->clickButton("a[href='#/match-summary/match-statistics']");
        sleep(1);
        $parentDiv = $this->seleniumDriver->findElement('div[id="detail"]');
        $htmlDivStart = $parentDiv->getAttribute("outerHTML");

        $htmlDivStart = "<!DOCTYPE html>" . $htmlDivStart;
        //khai bao cho the svg
        $htmlDivStart = str_replace("<svg ", "<svg xmlns='http://www.w3.org/2000/svg'", $htmlDivStart);
        MyRepo::saveText($htmlDivStart, "Start");
        sleep(1);
    }
    public function getDivTracker()
    {
        $this->seleniumDriver->clickButton("a[href='#/match-summary/live-commentary']");
        sleep(1);
        $parentDiv = $this->seleniumDriver->findElement('div[id="detail"]');
        $htmlDivStart = $parentDiv->getAttribute("outerHTML");

        $htmlDivStart = "<!DOCTYPE html>" . $htmlDivStart;
        //khai bao cho the svg
        $htmlDivStart = str_replace("<svg ", "<svg xmlns='http://www.w3.org/2000/svg'", $htmlDivStart);
        MyRepo::saveText($htmlDivStart, "Start");
    }
    public function crawlDetail()
    {
        return [];
        $parentDiv = $this->getDivParent();
        $time_1 = microtime(true);

        require_once(__DIR__ . "/../../library/simple_html_dom.php");
        $list_live_match = [];
        $parentDiv =  str_get_html($parentDiv);
        if (!$parentDiv) {
            return [];
        }

        $parentDivs = $parentDiv->find("div");
        // var_dump(count($parentDiv));
        // $this->seleniumDriver->quit();
        // exit;
        foreach ($parentDivs as $key => $div) {
            //   goto test;
            try {
                //check tournament
                $divTuornaments = $div->find('.event__title--type');

                if (!empty($divTuornaments)) {
                    //đây là div chứa tournament
                    $country_name = $div->find('.event__title--type')[0]->innertext();

                    $name = $div->find(".event__title--name")[0]->innertext();

                    $country_name =  strtolower($country_name);
                    $group = "";
                    if ((strpos($name, "Group") || strpos($name, "Offs") || strpos($name, "Apertura") || strpos($name, "Clausura"))

                        && strpos($name, " - ")
                    ) {
                        echo $name;
                        $nameDetail = explode(" - ", $name);
                        $name = $nameDetail[0];
                        $group = $nameDetail[1];
                    }
                    $hrefTour = "/football/" . MyRepo::create_slug($country_name) . "/" . $this->create_slug(strtolower($name));


                    $tournamentModel = new MatchTournament();
                    $tournamentModel->setCountryName(strtolower($country_name));
                    $tournamentModel->setTournamentName(strtolower($name));
                    $tournamentModel->setTournamentGroup(strtolower($group));
                    $tournamentModel->setId(count($this->list_live_tournaments) + 1);
                    $tournamentModel->setCountryImage("");
                    $tournamentModel->setTournamentHref($hrefTour);

                    $this->list_live_tournaments[] = $tournamentModel;

                    continue;
                }

                //match
                $divMatch = $div->find(".event__participant");

                if (!empty($divMatch)) {
                    $list_live_match[] = $this->getMatch($div);

                    echo "time get match: " . (microtime(true) - $time_1) . "</br>";
                }
            } catch (Exception $e) {
                echo "1-";

                continue;
            }
            test:
            // $text = $div->getAttribute("outerHTML");
            // $this->saveText($text, $key);
        }
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
