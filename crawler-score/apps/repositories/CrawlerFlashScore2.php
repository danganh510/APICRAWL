<?php

namespace Score\Repositories;

use Exception;
use Score\Models\ScCountry;

class CrawlerFlashScore2 extends CrawlerList
{
    public function __construct($seleniumDriver, $url_crawl, $day_time, $isLive)
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
            sleep(4);
        } else {
            //  click button LIVE cho lần đầu
            $divFilters = $this->seleniumDriver->findElements(".filters__text--short");
            foreach ($divFilters as $div) {
                // echo "time find div: " . (microtime(true) - $time_1) . "</br>";

                if ($div->getText() === 'LIVE') {
                    $div->click();
                    break;
                }
            }
            sleep(1);
        }

        $divClose = $this->seleniumDriver->findElements(".event__expander--close");
        $divClose = array_reverse($divClose);

        $click = 0;
        foreach ($divClose as $key =>  $div) {
            try {
                //  $this->seleniumDriver->waitItemHide("onetrust-accept-btn-handler");
                $div->click();
                echo "good-79--";
                sleep(0.1);
                $click++;
            } catch (Exception $e) {
                echo "error85:";
            }
        }

        sleep($click * 0.05);
        $htmlDiv = "";
        try {
            //  $this->seleniumDriver->clickButton('.filters__tab > .filters');
            // echo "time before find parent div: " . (microtime(true) - $time_1) . "</br>";
            $parentDiv = $this->seleniumDriver->findElement('div[id="live-table"] > section > div > div');

            // echo "time after find parent div: " . (microtime(true) - $time_1) . "</br>";

            $htmlDiv = $parentDiv->getAttribute("outerHTML");
            // echo "time get html parent div: " . (microtime(true) - $time_1) . "</br>";

            $htmlDiv = "<!DOCTYPE html>" . $htmlDiv;
            //khai bao cho the svg
            $htmlDiv = str_replace("<svg ", "<svg xmlns='http://www.w3.org/2000/svg'", $htmlDiv);
            // echo "time replace: " . (microtime(true) - $time_1) . "</br>";
        } catch (Exception $e) {
            echo "error118:";
            echo $e->getMessage();
        }
       // $this->seleniumDriver->checkRam();
        $this->seleniumDriver->quit();
        // echo "time get button: " . (microtime(true) - $time_1) . "</br>";

        return $htmlDiv;
    }
    public function crawlList()
    {
        $parentDiv = $this->getDivParent();
        require_once(__DIR__ . "/../../library/simple_html_dom.php");
        $list_live_match = [];
        $parentDiv =  str_get_html($parentDiv);
        if (!$parentDiv) {
            return [];
        }

        $parentDivs = $parentDiv->find("div");

        foreach ($parentDivs as $key => $div) {
            //   goto test;
            try {
                //check tournament
                $divTuornaments = $div->find('.event__title--type');

                if (!empty($divTuornaments)) {
                    //đây là div chứa tournament
                    $country_name = $div->find('.event__title--type', 0)->innertext();

                    $name = $div->find(".event__title--name", 0)->innertext();

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

                    $country_code = ScCountry::findFirstCodeByName($country_name);
                    $tournamentModel = new MatchTournament();
                    $tournamentModel->setCountryName(strtolower($country_name));
                    $tournamentModel->setCountryCode(strtolower($country_code));
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

                    // echo "time get match: " . (microtime(true) - $time_1) . "</br>";
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
    public function getMatch($divMatch)
    {
        $dataMatch = [];
        $id_insite = $divMatch->getAttribute("id");
        $id_insite = explode("_", $id_insite);
        $id_insite = $id_insite[count($id_insite) - 1];
        $dataMatch['href_detail'] = "/match/" . $id_insite;
        try {
            if (count($divMatch->find(".event__stage--block"))) {
                $time = $divMatch->find(".event__stage--block")[0]->text();
            } else {
                $time = $divMatch->find(".event__time")[0]->text();
            }
        } catch (Exception $e) {
        }
        $time = str_replace('&nbsp;', "", $time);
        $dataMatch['time'] = trim($time);

        $dataMatch['home'] = $divMatch->find(".event__participant--home")[0]->text();


        $home_image = $divMatch->find(".event__logo--home");
        $dataMatch['home_image'] = isset($home_image[0]) ? $home_image[0]->getAttribute("src") : '';

        $home_score = $divMatch->find(".event__score--home");
        $dataMatch['home_score'] = isset($home_score[0]) ? $home_score[0]->innertext() : 0;

        $dataMatch['away'] = $divMatch->find(".event__participant--away")[0]->text();
        $away_image = $divMatch->find(".event__logo--away");
        $dataMatch['away_image'] = isset($away_image[0]) ? $away_image[0]->getAttribute("src") : '';

        $away_score = $divMatch->find(".event__score--away");
        $dataMatch['away_score'] = isset($away_score[0]) ? $away_score[0]->innertext() : 0;

        $liveMatch = $this->saveMatch($dataMatch);
        return $liveMatch;
    }
}
