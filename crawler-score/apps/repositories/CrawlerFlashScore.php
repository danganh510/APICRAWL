<?php

namespace Score\Repositories;

use DOMDocument;
use Exception;
use Facebook\WebDriver\WebDriver;
use Facebook\WebDriver\WebDriverBy;
use Phalcon\Mvc\User\Component;

use Symfony\Component\DomCrawler\Crawler;

class CrawlerFlashScore extends Component
{
    public $url_fb = "https://www.flashscore.com";
    public function getDivParent($seleniumDriver, $time_plus = 0)
    {
        $time_delay = 1;
        $time_1 = microtime(true);

        if (date("H", time()) > $this->globalVariable->time_size_low_start && date("H", time()) < $this->globalVariable->time_size_low_end) {
            $time_delay = 2;
        }

        if ($time_plus) {
            //  $parentDiv = $seleniumDriver->findElements('div[id="live-table"] > section > div > div > div');
            //click button time cho lần đầu
            $seleniumDriver->clickButton("#calendarMenu");
            sleep(2);
            $divTimes = $seleniumDriver->findElements(".calendar__day");
            foreach ($divTimes as $div) {
                $text = $div->getText();
                if (explode(' ', $text)[0] == strftime('%d/%m', time() + $time_plus * 24 * 60 * 60)) {
                    $div->click();
                    break;
                }
            }
        } else {
            //  click button LIVE cho lần đầu
            $divFilters = $seleniumDriver->findElements(".filters__text--short");
            foreach ($divFilters as $div) {
                echo "time find div: ". (microtime(true) - $time_1). "</br>";

                if ($div->getText() === 'LIVE') {
                    $div->click();
                    break;
                }
            }
        }

        echo "time lick button: ". (microtime(true) - $time_1). "</br>";
        sleep(2);

        sleep(1);
        //click close
        while(empty($seleniumDriver->findElements(".event__expander--close"))) {
            $divClose = $seleniumDriver->findElements(".event__expander--close");
            foreach ($divClose as $key =>  $div) {
                try {
                    $div->click();
                    sleep(0.1);
                    echo "time click icon $key: ". (microtime(true) - $time_1). "</br>";
                    break;
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
            }
        }
        // foreach ($divClose as $key =>  $div) {
        //     try {
        //         $div->click();
        //         sleep(1);
        //         echo "time click icon $key: ". (microtime(true) - $time_1). "</br>";
        //     } catch (Exception $e) {
        //         echo $e->getMessage();
        //     }
        // }
        echo "time click icon: ". (microtime(true) - $time_1). "</br>";

        sleep(1);
        $htmlDiv = "";
        try {
            //  $seleniumDriver->clickButton('.filters__tab > .filters');
            echo "time before find parent div: ". (microtime(true) - $time_1). "</br>";
            $parentDiv = $seleniumDriver->findElement('div[id="live-table"] > section > div > div');
            
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
        $seleniumDriver->quit();
        echo "time get button: ". (microtime(true) - $time_1). "</br>";

        return ($htmlDiv);
    }
    public function CrawlFlashScore($parentDiv)
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

        $parentDivs = $parentDiv->find("div");
        // var_dump(count($parentDiv));
        // $seleniumDriver->quit();
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
                    if (strpos('(', $home) && strpos(')', $home)) {
                        $home = explode('(', $home);
                        $home = trim($home[0]);
                    }
                    if (strpos('(', $away) && strpos(')', $away)) {
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
    public function saveText($text, $key)
    {
        $fp = fopen(__DIR__ . "/../test/div_$key.html", 'w'); //mở file ở chế độ write-only
        fwrite($fp, $text);
        fclose($fp);
    }
    function create_slug($string)
    {
        $search = array(
            '#(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)#',
            '#(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)#',
            '#(ì|í|ị|ỉ|ĩ)#',
            '#(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)#',
            '#(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)#',
            '#(ỳ|ý|ỵ|ỷ|ỹ)#',
            '#(đ)#',
            '#(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)#',
            '#(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)#',
            '#(Ì|Í|Ị|Ỉ|Ĩ)#',
            '#(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)#',
            '#(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)#',
            '#(Ỳ|Ý|Ỵ|Ỷ|Ỹ)#',
            '#(Đ)#',
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
