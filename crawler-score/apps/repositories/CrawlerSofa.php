<?php

namespace Score\Repositories;

use Exception;
use Facebook\WebDriver\WebDriver;
use Facebook\WebDriver\WebDriverBy;
use Phalcon\Mvc\User\Component;

use Symfony\Component\DomCrawler\Crawler;

class CrawlerSofa extends Component
{
    public $url_fb = "https://www.sofascore.com";
    public function CrawlSofa()
    {
        $list_live_match = [];
        $list_live_tournaments = [];
        $index = 0;
        $tournaments = [];
        $seleniumDriver = new Selenium($this->url_fb);
        $seleniumDriver->clickButton('button[data-tabid="mobileSportListType.true"]');
        $parentDiv = $seleniumDriver->findElements('div[aria-readonly="true"] > div >div ');
        foreach ($parentDiv as $key => $div) {
            try {
                //check tournament
                $aTuornaments = $div->findElements(WebDriverBy::cssSelector('a[href^="/tournament/"]'));
                if (count($aTuornaments)) {
                    $aTuornament = $aTuornaments[0];
                    //đây là div chứa tournament
                    $imgTournament = $div->findElement(WebDriverBy::cssSelector('img'));
                    $image_country = $imgTournament->getAttribute("src");
                    $name = $aTuornament->getText();

                    $group = "";
                    if (strpos($name,"Group")) {
                        $nameDetail = explode(", ",$name);
                        $name = $nameDetail[0];
                        $group = $nameDetail[1];
                    }
                    $hrefTour = $aTuornament->getAttribute("href");
                    $arrHref = explode("/", $hrefTour);
                    $country_name = $arrHref[5];

                    $tournamentModel = new MatchTournament();
                    $tournamentModel->setCountryName($country_name);
                    $tournamentModel->setTournamentName($name);
                    $tournamentModel->setTournamentGroup($group);
                    $tournamentModel->setId(count($list_live_tournaments) + 1);
                    $tournamentModel->setCountryImage($image_country);
                    $tournamentModel->setTournamentHref($hrefTour);

                    $list_live_tournaments[] = $tournamentModel;
                }
            } catch (Exception $e) {
                continue;
            }

            //test
            // $text = $div->getAttribute("outerHTML");
            // $this->saveText($text, $key);
        }
        var_dump($list_live_tournaments);
        echo "finish";
        exit;
        return $list_live_match;
    }
    public function saveText($text, $key)
    {
        $fp = fopen(__DIR__ . "/../test/div_$key.html", 'w'); //mở file ở chế độ write-only
        fwrite($fp, $text);
        fclose($fp);
    }
}
