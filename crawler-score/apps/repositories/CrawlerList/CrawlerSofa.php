<?php

namespace Score\Repositories;

use DOMDocument;
use Exception;
use Facebook\WebDriver\Interactions\WebDriverActions;
use Facebook\WebDriver\WebDriver;
use Facebook\WebDriver\WebDriverBy;
use Phalcon\Mvc\User\Component;

use Symfony\Component\DomCrawler\Crawler;

class CrawlerSofa extends Component
{
    public $url_fb = "https://www.flashscore.com";
    public $url_sf = "https://www.sofascore.com/football";
    public function getDivParent($seleniumDriver, $time_plus = 0)
    {
        $time_delay = 1;
        $time_1 = microtime(true);
        $htmlDiv = "";
        echo $seleniumDriver->getPageSource();
        $seleniumDriver->quit();
        exit;
        try {
            //click button live: react-calendar__month-view__days__day
            if (!$time_plus) {
                $seleniumDriver->clickButton('button[data-tabid="mobileSportListType.true"]');
                sleep(0.5);
            }
            sleep(1);

            //scroll to bottom
            //  $page_height = $seleniumDriver->executeScript("return document.body.scrollHeight;");
            try {
                $seleniumDriver->executeScript("window.scrollTo(0, 1200);");
                sleep(1);
                $seleniumDriver->executeScript("window.scrollTo(0, 1200);");
            } catch (Exception $e) {
                echo $e->getMessage();
            }
            sleep(1);
            //showmore:
            $buttons = $seleniumDriver->findElements("button");
            foreach ($buttons as $button) {
                if ($button->getText() == "SHOW ALL MATCHES") {
                    $button->click();
                    echo "clicked2</br>";
                    break;
                }
            }
            sleep(1);
            $parentDiv = $seleniumDriver->findElement('div[aria-readonly="true"] > div  ');

            //open button show more or scroll bot
            $htmlDiv = $parentDiv->getAttribute("outerHTML");

            $htmlDiv = "<!DOCTYPE html>" . $htmlDiv;
            //khai bao cho the svg
            $htmlDiv = str_replace("<svg ", "<svg xmlns='http://www.w3.org/2000/svg'", $htmlDiv);
            echo "time click icon: " . (microtime(true) - $time_1) . "</br>";
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        $seleniumDriver->quit();
        echo "time get button: " . (microtime(true) - $time_1) . "</br>";
        return ($htmlDiv);
    }
    public function CrawlMatchScore($parentDiv)
    {

        $time_1 = microtime(true);

        require_once(__DIR__ . "/../library/simple_html_dom.php");
        $list_live_match = [];
        $list_live_tournaments = [];
        $index = 0;
        $tournaments = [];
        $parentDiv =  str_get_html($parentDiv);
        if (!$parentDiv) {
            return [];
        }

        $parentDivs = $parentDiv->find("div > ");


        foreach ($parentDivs as $key => $div) {
            // goto test;
            try {
                //check tournament
                $aTuornaments = $div->find('div > div > div a[href*="tournament"]', 0);
                // foreach ($divTuornaments as $link) {
                //     if (strpos($link->href, 'tournament') !== false) {
                //         $hrefTour = $link->href;
                //         $aTuornaments = $link;
                //     }
                // }

                if ($aTuornaments) {
                    //????y l?? div ch???a tournament
                    //     $country_name = $div->find('.event__title--type')[0]->innertext();

                    $name = $aTuornaments->text();
                    $hrefTour = $aTuornaments->href;

                    $arrHref = explode("/", $hrefTour);
                    $country_name = $arrHref[count($arrHref) - 3];

                    $country_name =  strtolower($country_name);
                    $group = "";
                    if (strpos($name, "Group") && strpos($name, " - ")) {
                        echo $name;
                        $nameDetail = explode(" - ", $name);
                        $name = $nameDetail[0];
                        $group = $nameDetail[1];
                    }


                    $tournamentModel = new MatchTournament();
                    $tournamentModel->setCountryName($country_name);
                    $tournamentModel->setTournamentName($name);
                    $tournamentModel->setTournamentGroup($group);
                    $tournamentModel->setId(count($list_live_tournaments) + 1);
                    $tournamentModel->setCountryImage("");
                    $tournamentModel->setTournamentHref($hrefTour);

                    $list_live_tournaments[] = $tournamentModel;

                    continue;
                }
                // echo "123";exit;

                //match

                $aMatch = $div->find('a[data-id]', 0);

                if (($aMatch)) {

                    $href_detail = $aMatch->href;
                    $spanTime = $div->find("a[data-id] > div > div > div > div >span > span", 0);
                    if (!$spanTime) {
                        continue;
                    }

                    $time = $spanTime->text();
                    $time = str_replace('&nbsp;', "", $time);
                    $time = trim($time);


                    $divTeams =  $div->find("a[data-id] > div > div > div > div >div");
                    if (count($divTeams) < 1) {
                        continue;
                    }
                    $home =  $divTeams[0]->text();
                    $home_image = "";
                    $away = $divTeams[1]->text();
                    $away_image = "";

                    $spanScore =  $div->find("a[data-id] > div > div > div > div >div > div > .currentScore");
                    if (count($spanScore) < 2) {
                        $home_score = 0;
                        $away_score =  0;
                    } else {
                        $home_score = $spanScore[0]->text();
                        $away_score =   $spanScore[1]->text();
                    }


                    score0:
                    $home = str_replace(['GOAL', 'CORRECTION', '&nbsp;'], ['', '', ''], $home);
                    $home = trim($home);

                    $away = str_replace(['GOAL', 'CORRECTION', '&nbsp;'], ['', '', ''], $away);
                    $away = trim($away);

                    //loai bo ten nuoc ra khoi ten:
                    if (strpos($home, '(')) {
                        $home = explode('(', $home);
                        $home = trim($home[0]);
                    }
                    if (strpos($away, '(')) {
                        $away = explode('(', $away);
                        $away = trim($away[0]);
                    }

                    $liveMatch = new MatchCrawl();
                    $liveMatch->setTime(MyRepo::replace_space($time));
                    $liveMatch->setHome($home);
                    $liveMatch->setHomeScore(is_numeric($home_score) ? $home_score : 0);
                    $liveMatch->setHomeImg($home_image);
                    $liveMatch->setAway($away);
                    $liveMatch->setAwayScore(is_numeric($away_score) ? $away_score : 0);
                    $liveMatch->setAwayImg($away_image);
                    $liveMatch->setHrefDetail($href_detail);
                    $liveMatch->setTournament($list_live_tournaments[count($list_live_tournaments) - 1]);
                    $list_live_match[] =  $liveMatch;
                    echo "time get match: " . (microtime(true) - $time_1) . "</br>";
                }
            } catch (Exception $e) {
                echo "1-";

                continue;
            }
            test:
            $text = $div->innertext();
        }

        return $list_live_match;
    }
    public function saveText($text, $key)
    {
        $dir_test = __DIR__ . "/../test";
        if (!is_dir($dir_test)) {
            mkdir($dir_test);
        }
        $fp = fopen(__DIR__ . "/../test/div_$key.html", 'w'); //m??? file ??? ch??? ????? write-only
        fwrite($fp, $text);
        fclose($fp);
    }
    function create_slug($string)
    {
        $search = array(
            '#(??|??|???|???|??|??|???|???|???|???|???|??|???|???|???|???|???)#',
            '#(??|??|???|???|???|??|???|???|???|???|???)#',
            '#(??|??|???|???|??)#',
            '#(??|??|???|???|??|??|???|???|???|???|???|??|???|???|???|???|???)#',
            '#(??|??|???|???|??|??|???|???|???|???|???)#',
            '#(???|??|???|???|???)#',
            '#(??)#',
            '#(??|??|???|???|??|??|???|???|???|???|???|??|???|???|???|???|???)#',
            '#(??|??|???|???|???|??|???|???|???|???|???)#',
            '#(??|??|???|???|??)#',
            '#(??|??|???|???|??|??|???|???|???|???|???|??|???|???|???|???|???)#',
            '#(??|??|???|???|??|??|???|???|???|???|???)#',
            '#(???|??|???|???|???)#',
            '#(??)#',
            "/[^a-zA-Z0-9\-\_]/",
        );
        $replace = array(
            'a',
            'e',
            'i',
            'o',
            'u',
            'y',
            'd',
            'A',
            'E',
            'I',
            'O',
            'U',
            'Y',
            'D',
            '-',
        );
        $string = preg_replace($search, $replace, $string);
        $string = preg_replace('/(-)+/', '-', $string);
        $string = strtolower($string);
        return $string;
    }
}
