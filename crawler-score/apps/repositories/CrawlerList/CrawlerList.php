<?php

namespace Score\Repositories;

use DOMDocument;
use Exception;
use Facebook\WebDriver\WebDriver;
use Facebook\WebDriver\WebDriverBy;
use Phalcon\Mvc\User\Component;

use Symfony\Component\DomCrawler\Crawler;

class CrawlerList extends Component
{
    public $url_fl = "https://www.flashscore.com";
    public $url_sf = "https://www.sofascore.com/football";
    public $url_lc = "https://www.livescores.com";
    public $time_plus;
    public $parentDiv;
    public $type_crawl;
    public $url_crawl;
    public $isLive;
    public $day_time;

    public $seleniumDriver;
    public $list_live_tournaments = [];
    public $round;

    public function __construct($type_crawl, $time_plus = 0, $isLive = false)
    {
        $this->type_crawl = $type_crawl;
        $this->time_plus = $time_plus;
        $this->isLive = $isLive;
    }
    public function runSelenium()
    {
        $this->seleniumDriver = new Selenium($this->url_crawl);
    }
    public function getInstance()
    {
        $day_time =  $this->my->formatDateYMD(time() + $this->time_plus * 24 * 60 * 60);
        switch ($this->type_crawl) {
            case MatchCrawl::TYPE_FLASH_SCORE:
                $this->url_crawl = $this->url_fl;
                if ($this->isLive) {
                    $crawler = new CrawlerFlashScoreLive($this->seleniumDriver, $this->url_crawl, $day_time, $this->isLive);
                    break;
                }
                $crawler = new CrawlerFlashScore($this->seleniumDriver, $this->url_crawl, $day_time, $this->isLive);
                break;
            case MatchCrawl::TYPE_SOFA:
                $this->url_crawl = $this->url_sf;
                $this->runSelenium();
                break;
            case MatchCrawl::TYPE_API_SOFA:
                $this->url_crawl = $this->url_sf;
                $this->runSelenium();
                break;
            case MatchCrawl::TYPE_LIVE_SCORES:
                $this->url_crawl = $this->url_lc;
                $crawler = new CrawlerScore($this->url_crawl, $day_time, $this->isLive);
                break;
        }
        return $crawler->crawlList();
    }
    public function saveDivToFile($div, $key)
    {
        $dir_div = __DIR__ . "/match";
        if (!is_dir($dir_div)) {
            mkdir($dir_div);
        }
        $fp = fopen($dir_div . "/div_$key.html", 'w'); //mở file ở chế độ write-only
        fwrite($fp, $div);
        fclose($fp);
    }
    public function deleteFolder()
    {
        $dir_div = __DIR__ . "/match";
        if (is_dir($dir_div)) {
            $objects = scandir($dir_div);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    unlink($dir_div . "/" . $object);
                }
            }
            rmdir($dir_div);
        }
    }
    public function openDivfromFile($div, $key)
    {
        $dir_div = __DIR__ . "/match";
        if (!is_dir($dir_div)) {
            return false;
        }
        $fp = fopen($dir_div . "/div_$key.html", 'w'); //mở file ở chế độ write-only
        return $fp;
    }
    public function checkFileCache()
    {
        $dir_div = __DIR__ . "/match";

        return is_dir($dir_div);
    }
    public function getDivHtml($key)
    {
        $dir_div = __DIR__ . "/match";
        $div_html = file_get_contents($dir_div . "/div_$key.html");
        return $div_html;
    }
    public function getTournament($div)
    {
    }
    public function getMatch($div)
    {
    }
    public function saveMatch($data)
    {

        $data['home'] = str_replace(['GOAL', 'CORRECTION', '&nbsp;'], ['', '', ''], $data['home']);
        $data['home'] = trim($data['home']);
        //loai bo ten nuoc ra khoi ten:
        if (strpos($data['home'], '(')) {
            $data['home'] = explode('(', $data['home']);
            $data['home'] = trim($data['home'][0]);
        }
        $data['away'] = str_replace(['GOAL', 'CORRECTION', '&nbsp;'], ['', '', ''], $data['away']);
        $data['away'] = trim($data['away']);
        //loai bo ten nuoc ra khoi ten:
        if (strpos($data['away'], '(')) {
            $data['away'] = explode('(', $data['away']);
            $data['away'] = trim($data['away'][0]);
        }

        $liveMatch = new MatchCrawl();
        $liveMatch->setTime(MyRepo::replace_space($data['time']));
        $liveMatch->setHome($data['home']);
        $liveMatch->setHomeScore(is_numeric($data['home_score']) ? $data['home_score'] : 0);

        $liveMatch->setAway($data['away']);
        $liveMatch->setAwayScore(is_numeric($data['away_score']) ? $data['away_score'] : 0);
        $liveMatch->setHrefDetail($data['href_detail']);
        $liveMatch->setRound($this->round);
        $liveMatch->setTournament($this->list_live_tournaments[count($this->list_live_tournaments) - 1]);
        $liveMatch->setCountryCode($this->list_live_tournaments[count($this->list_live_tournaments) - 1]->getCountryCode());
        if (isset($data['home_image'])) {
            $liveMatch->setHomeImg($data['home_image']);
        }
        if (isset($data['away_image'])) {
            $liveMatch->setAwayImg($data['away_image']);
        }
        if (isset($data['home_card_red'])) {
            $liveMatch->setHomeCardRed($data['home_card_red']);
        }
        if (isset($data['away_card_red'])) {
            $liveMatch->setAwayCardRed($data['away_card_red']);
        }
        return $liveMatch;
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
