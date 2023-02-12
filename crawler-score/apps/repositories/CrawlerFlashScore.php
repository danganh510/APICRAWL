<?php

namespace Score\Repositories;

use Exception;
use Facebook\WebDriver\WebDriver;
use Facebook\WebDriver\WebDriverBy;
use Phalcon\Mvc\User\Component;

use Symfony\Component\DomCrawler\Crawler;

class CrawlerFlashScore extends Component
{
    public $url_fb = "https://www.flashscore.com";
    public function getDivParent($seleniumDriver)
    {

        //click button LIVE cho lần đầu
        $divFilters = $seleniumDriver->findElements(".filters__text--short");
        foreach ($divFilters as $div) {
            if ($div->getText() === 'LIVE') {
                $div->click();
                break;
            }
        }
        sleep(1);
        //  $seleniumDriver->clickButton('.filters__tab > .filters');
        $parentDiv = $seleniumDriver->findElements('div[id="live-table"] > section > div > div > div');
        return $parentDiv;
    }
    public function CrawlFlashScore($parentDiv)
    {
        $list_live_match = [];
        $list_live_tournaments = [];
        $index = 0;
        $tournaments = [];

        // var_dump(count($parentDiv));
        // $seleniumDriver->quit();
        // exit;
        foreach ($parentDiv as $key => $div) {

            try {
                //check tournament
                $divTuornaments = $div->findElements(WebDriverBy::cssSelector('.event__title--type'));

                if (count($divTuornaments)) {
                    //đây là div chứa tournament
                    $country_name = $div->findElement(WebDriverBy::cssSelector('.event__title--type'))->getText();
                    $name = $div->findElement(WebDriverBy::cssSelector('.event__title--name'))->getText();

                    $country_name =  strtolower($country_name);
                    $group = "";
                    if (strpos($name, "Group")) {
                        $nameDetail = explode(" - ", $name);
                        $name = $nameDetail[0];
                        $group = $nameDetail[1];
                    }
                    $hrefTour = "/football/" . $country_name . "/" . $this->create_slug(strtolower($name));


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
                $divMatch = $div->findElements(WebDriverBy::cssSelector('.event__stage--block'));
                if (count($divMatch)) {
                    $id_insite = $div->getAttribute("id");
                    $id_insite = explode("_", $id_insite);
                    $id_insite = $id_insite[count($id_insite) - 1];
                    $href_detail = "/match/" . $id_insite;
                    $time = $div->findElement(WebDriverBy::cssSelector(".event__stage--block"))->getText();

                    $home = $div->findElement(WebDriverBy::cssSelector(".event__participant--home"))->getText();
                    $home_image = $div->findElement(WebDriverBy::cssSelector(".event__logo--home"))->getAttribute("src");
                    $home_score = $div->findElement(WebDriverBy::cssSelector(".event__score--home"))->getText();

                    $away = $div->findElement(WebDriverBy::cssSelector(".event__participant--away"))->getText();
                    $away_image = $div->findElement(WebDriverBy::cssSelector(".event__logo--away"))->getAttribute("src");
                    $away_score = $div->findElement(WebDriverBy::cssSelector(".event__score--away"))->getText();

                    $liveMatch = new MatchCrawl();
                    $liveMatch->setTime(MyRepo::replace_space($time));
                    $liveMatch->setHome($home);
                    $liveMatch->setHomeScore($home_score);
                    $liveMatch->setHomeImg($home_image);
                    $liveMatch->setAway($away);
                    $liveMatch->setAwayScore($away_score);
                    $liveMatch->setAwayImg($away_image);
                    $liveMatch->setHrefDetail($href_detail);
                    $liveMatch->setTournament($list_live_tournaments[count($list_live_tournaments) - 1]);
                    $list_live_match[] =  $liveMatch;
                }
            } catch (Exception $e) {

                continue;
            }

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
