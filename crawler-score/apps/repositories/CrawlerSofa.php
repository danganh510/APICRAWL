<?php

namespace Score\Repositories;

use DOMDocument;
use Exception;
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
        try {
            $seleniumDriver->clickButton('button[data-tabid="mobileSportListType.true"]');
            $parentDiv = $seleniumDriver->findElement('div[aria-readonly="true"] > div  ');

            sleep(0.5);
            //open button show more or scroll bot
            $htmlDiv = $parentDiv->getAttribute("outerHTML");

            $htmlDiv = "<!DOCTYPE html>" . $htmlDiv;
            //khai bao cho the svg
            $htmlDiv = str_replace("<svg ", "<svg xmlns='http://www.w3.org/2000/svg'", $htmlDiv);
            $this->saveText($htmlDiv, time());
            echo "time click icon: " . (microtime(true) - $time_1) . "</br>";
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        $seleniumDriver->quit();
        echo "time get button: " . (microtime(true) - $time_1) . "</br>";
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

        foreach ($parentDivs as $key => $div) {
            //   goto test; 
            try {
                //check tournament
                $divTuornaments = $div->find('a');
                foreach ($divTuornaments as $link) {
                    if (strpos($link->href, 'tournament') !== false) {
                      $hrefTour = $link->href;
                      $aTuornaments = $link;
                    }
                  }

                if (isset($hrefTour)) {
             
                    //đây là div chứa tournament
               //     $country_name = $div->find('.event__title--type')[0]->innertext();

                    $name = $aTuornaments->text();

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
continue;
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
            // $text = $div->getAttribute("outerHTML");
            // $this->saveText($text, $key);
        }
        var_dump($list_live_tournaments);exit;
        return $list_live_match;
    }
    public function saveText($text, $key)
    {
        $dir_test = __DIR__ . "/../test";
        if (!is_dir($dir_test)) {
            mkdir($dir_test);
        }
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
