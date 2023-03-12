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
        var_dump(microtime(true) - $time);
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
}
