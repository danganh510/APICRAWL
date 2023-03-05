<?php

namespace Score\Repositories;

use Exception;
use Score\Models\ScCountry;

class CrawlerFlashScoreLive extends CrawlerFlashScoreBase
{
    public function __construct($seleniumDriver, $url_crawl, $day_time, $isLive)
    {
        $this->seleniumDriver = $seleniumDriver;
        $this->url_crawl = $url_crawl;
        $this->day_time = $day_time;
        $this->isLive = $isLive;
    }
    public function getHtmlParent()
    {
        $this->setupSite();
        $htmlDiv = "";
        try {

            $parentDiv = $this->seleniumDriver->findElement('div[id="live-table"] > section > div > div');

            $htmlDiv = $parentDiv->getAttribute("outerHTML");
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        $this->seleniumDriver->quit();
        return $htmlDiv;
    }
    public function crawlList()
    {
        require_once(__DIR__ . "/../../library/simple_html_dom.php");
        $parentDiv = $this->getHtmlParent();
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
                    $this->list_live_tournaments[] = $this->getTournament($div);
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
