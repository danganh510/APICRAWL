<?php

namespace Score\Repositories;

use DOMDocument;
use Exception;
use Facebook\WebDriver\WebDriver;
use Facebook\WebDriver\WebDriverBy;
use Phalcon\Mvc\User\Component;

use Symfony\Component\DomCrawler\Crawler;

class CrawlerFlashScore extends CrawlerList
{
    public function __construct($seleniumDriver,$url_crawl, $day_time, $isLive)
    {
        $this->seleniumDriver = $seleniumDriver;
        $this->url_crawl = $url_crawl;
        $this->day_time = $day_time;
        $this->isLive = $isLive;
    }
    public function getDivParent()
    {
        $time_delay = 1;
        $time_1 = microtime(true);

        // if (date("H", time()) > $this->globalVariable->time_size_low_start && date("H", time()) < $this->globalVariable->time_size_low_end) {
        //     $time_delay = 2;
        // }

        if (!$this->isLive) {
            //  $parentDiv = $seleniumDriver->findElements('div[id="live-table"] > section > div > div > div');
            //click button time cho lần đầu
            $this->seleniumDriver->clickButton("#calendarMenu");
            sleep(1);
            $divTimes = $this->seleniumDriver->findElements(".calendar__day");
            foreach ($divTimes as $div) {
                $text = $div->getText();
                if (explode(' ', $text)[0] == strftime('%d/%m', strtotime($this->day_time))) {
                    $div->click();
                    break;
                }
            }
            sleep(2.5);
        } else {
            //  click button LIVE cho lần đầu
            $divFilters = $this->seleniumDriver->findElements(".filters__text--short");
            foreach ($divFilters as $div) {
                echo "time find div: ". (microtime(true) - $time_1). "</br>";

                if ($div->getText() === 'LIVE') {
                    $div->click();
                    break;
                }
            }
            sleep(1);
        }

        echo "time lick button: ". (microtime(true) - $time_1). "</br>";
      

        // sleep(1);
        //click close
        $total = 0;
        // while(!empty($this->seleniumDriver->findElements(".event__expander--close"))) {
        //     $divClose = $seleniumDriver->findElements(".event__expander--close");
        //     foreach ($divClose as $key =>  $div) {
        //         try {
        //             $div->click();
        //             sleep(0.1);
        //             echo "time click icon $key: ". (microtime(true) - $time_1). "</br>";
        //             break;
        //         } catch (Exception $e) {
        //             echo $e->getMessage();
        //         }
        //     }
        // }
        $divClose = $this->seleniumDriver->findElements(".event__expander--close");
        $divClose = array_reverse($divClose);
      
        foreach ($divClose as $key =>  $div) {
            try {
                $div->click();
                echo "time click icon $key: ". (microtime(true) - $time_1). "</br>";
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }
        $divClose = $this->seleniumDriver->findElements(".event__expander--close");
        $divClose = array_reverse($divClose);
        foreach ($divClose as $key =>  $div) {
            try {
                $div->click();
                echo "time click icon $key: ". (microtime(true) - $time_1). "</br>";
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }

        echo "time click icon: ". (microtime(true) - $time_1). "</br>";
        sleep(0.1);
        $htmlDiv = "";
        try {
            //  $this->seleniumDriver->clickButton('.filters__tab > .filters');
            echo "time before find parent div: ". (microtime(true) - $time_1). "</br>";
            $parentDiv = $this->seleniumDriver->findElement('div[id="live-table"] > section > div > div');
            
            echo "time after find parent div: ". (microtime(true) - $time_1). "</br>";

            $htmlDiv = $parentDiv->getAttribute("outerHTML");
            echo "time get html parent div: ". (microtime(true) - $time_1). "</br>";

            $htmlDiv = "<!DOCTYPE html>" . $htmlDiv;
            //khai bao cho the svg
            $htmlDiv = str_replace("<svg ", "<svg xmlns='http://www.w3.org/2000/svg'", $htmlDiv);
            echo "time replace: ". (microtime(true) - $time_1). "</br>";

             $this->saveText($htmlDiv, time());
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        $this->seleniumDriver->quit();
        echo "time get button: ". (microtime(true) - $time_1). "</br>";

        return ($htmlDiv);
    }
    public function crawlList()
    {
        $parentDiv = $this->getDivParent();
        $time_1 = microtime(true);

        require_once(__DIR__ . "/../../library/simple_html_dom.php");
        $list_live_match = [];
        $list_live_tournaments = [];
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
                    if (strpos($name, "Group") && strpos($name, " - ")) {
                        echo $name;
                        $nameDetail = explode(" - ", $name);
                        $name = $nameDetail[0];
                        $group = $nameDetail[1];
                    }
                    $hrefTour = "/football/" . MyRepo::create_slug($country_name) . "/" . $this->create_slug(strtolower($name));


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

                //match
                $divMatch = $div->find(".event__participant");

                if (!empty($divMatch)) {
                    $id_insite = $div->getAttribute("id");
                    $id_insite = explode("_", $id_insite);
                    $id_insite = $id_insite[count($id_insite) - 1];
                    $href_detail = "/match/" . $id_insite;
                    try {
                        if (count($div->find(".event__stage--block"))) {
                            $time = $div->find(".event__stage--block")[0]->text();
                        } else {
                            $time = $div->find(".event__time")[0]->text();
                        }
                    } catch (Exception $e) {
                    }
                    $time = str_replace('&nbsp;', "", $time);
                    $time = trim($time);

                    $home = $div->find(".event__participant--home")[0]->text();
                    $home_image = $div->find(".event__logo--home");
                    $home_image = isset($home_image[0]) ? $home_image[0]->getAttribute("src") : '';
                    $home_score = $div->find(".event__score--home");
                    $home_score = isset($home_score[0]) ? $home_score[0]->innertext() : 0;

                    $away = $div->find(".event__participant--away")[0]->text();
                    $away_image = $div->find(".event__logo--away");
                    $away_image = isset($away_image[0]) ? $away_image[0]->getAttribute("src") : '';

                    $away_score = $div->find(".event__score--away");
                    $away_score = isset($away_score[0]) ? $away_score[0]->innertext() : 0;

                    $home = str_replace(['GOAL', 'CORRECTION'], ['', ''], $home);
                    $home = trim($home);

                    $away = str_replace(['GOAL', 'CORRECTION', '&nbsp;'], ['', '', ''], $away);
                    $away = trim($away);

                    //loai bo ten nuoc ra khoi ten:
                    if (strpos($home,'(')) {
                        $home = explode( '(',$home);
                        $home = trim($home[0]);          
                    }
                    if (strpos($away,'(')) {
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
                    echo "time get match: ". (microtime(true) - $time_1). "</br>";

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

}