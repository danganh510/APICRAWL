<?php

namespace Score\Repositories;

use DOMDocument;
use Exception;
use Facebook\WebDriver\WebDriver;
use Facebook\WebDriver\WebDriverBy;
use Phalcon\Mvc\User\Component;

use Symfony\Component\DomCrawler\Crawler;

class CrawlerApiSofa extends Component
{
    public $url_fb = "https://www.flashscore.com";
    public $url_sf = "https://api.sofascore.com/api/v1/sport/football/scheduled-events";
    public $data;
    public function __construct($dateTime)
    {
        $this->url_sf = $this->url_sf . "/" . $dateTime;
        $this->data = json_decode(MyRepo::getHtmlByPassCloudFalre($this->url_sf));
    }

    public function CrawlMatchScore()
    {

        $arrTourExits = [];
        $list_live_tournaments = [];
        foreach ($this->data->events as $key => $div) {

            // goto test;
            try {
                if (!in_array($div->tournament->slug, $arrTourExits)) {
                    array_push($arrTourExits, $div->tournament->slug);

                    $name = $div->tournament->name;
                    $country_name = $div->tournament->category->name;
                    $country_name =  strtolower($country_name);
                    $hrefTour = "/tournament/football/$country_name/$name/" . $div->tournament->uniqueTournament->category->id;
                  
                    $group = isset($div->roundInfo->round)  ? $div->roundInfo->round : "";
                    

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
                if (isset($div->time->currentPeriodStartTimestamp)) {
                    $time = $div->time->currentPeriodStartTimestamp - $div->startTimestamp;
                    if ($time > 90 * 60) {
                        $time = "FT";
                    }
                } else {
                    $time = 0;

                }

                $start_time = $div->startTimestamp;

                $href_detail = "/{$div->slug}/{$div->customId}";
                $home =  $div->homeTeam->name;
                $home_image = "";
                $away =  $div->awayTeam->name;
                $away_image = "";

                if(isset($div->homeScore->current)) {
                    $home_score = $div->homeScore->current;
                    $away_score =   $div->awayScore->current;
                } else {
                    $home_score = 0;
                    $away_score =   0;
                }
                

                $home = str_replace(['GOAL', 'CORRECTION', '&nbsp;'], ['', '', ''], $home);
                $home = trim($home);

                $away = str_replace(['GOAL', 'CORRECTION', '&nbsp;'], ['', '', ''], $away);
                $away = trim($away);

                $liveMatch = new MatchCrawl();
                $liveMatch->setTime(MyRepo::replace_space($time));
                $liveMatch->setStartTime($start_time);
                $liveMatch->setHome($home);
                $liveMatch->setHomeScore(is_numeric($home_score) ? $home_score : 0);
                $liveMatch->setHomeImg($home_image);
                $liveMatch->setAway($away);
                $liveMatch->setAwayScore(is_numeric($away_score) ? $away_score : 0);
                $liveMatch->setAwayImg($away_image);
                $liveMatch->setHrefDetail($href_detail);
                $liveMatch->setTournament($list_live_tournaments[count($list_live_tournaments) - 1]);
                $list_live_match[] =  $liveMatch;
            } catch (Exception $e) {
                echo "1-";

                continue;
            }
        }

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
