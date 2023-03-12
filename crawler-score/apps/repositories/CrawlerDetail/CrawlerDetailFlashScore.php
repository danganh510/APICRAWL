<?php

namespace Score\Repositories;

use DOMDocument;
use Exception;


class CrawlerDetailFlashScore extends CrawlerDetail
{
    public function __construct($seleniumDriver, $url_crawl, $day_time, $isLive)
    {
        $this->seleniumDriver = $seleniumDriver;
        $this->url_crawl = $url_crawl;
        $this->isLive = $isLive;
    }
    public function crawlDetail()
    {
        require_once(__DIR__ . "/../../library/simple_html_dom.php");
        $result = [];
        $time = microtime(true);
        $this->getDivParent();
        $info = $this->crawlDetailInfo();
        $start = $this->crawlDetailStarts();
        $tracker = $this->crawlDetailTracker();

        $result = [
            'match' => $info['match'],
            'info' => $info['info'],
            'start' => $start,
            'tracker' => $tracker
        ];
        return $result;
    }
    /**
     * @ $this->seleniumDriver Selenium
     */
    public function getDivParent()
    { #/match-summary/match-statistics
        try {
            //$html = $this->seleniumDriver->getPageSource();
            //  $this->seleniumDriver->clickButton('.filters__tab > .filters');
            $this->divInfo = $this->getDivInfo();
            $this->divStart = $this->getDivStart();
            $this->divTracker = $this->getDivTracker();
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        echo $this->seleniumDriver->checkRam();
        $this->seleniumDriver->quit();
    }
    public function getDivInfo()
    {
        $parentDiv = $this->seleniumDriver->findElement('div[id="detail"]');

        $htmlDivInfo = $parentDiv->getAttribute("outerHTML");

        $htmlDivInfo = "<!DOCTYPE html>" . $htmlDivInfo;
        //khai bao cho the svg
        $htmlDivInfo = str_replace("<svg ", "<svg xmlns='http://www.w3.org/2000/svg'", $htmlDivInfo);
        return $htmlDivInfo;
    }
    public function getDivStart()
    {
        $htmlDivStart = "";

        try {
            $this->seleniumDriver->clickButton("a[href='#/match-summary/match-statistics']");
        } catch (Exception $e) {
            goto end;
        }
        sleep(1);
        $parentDiv = $this->seleniumDriver->findElement('div[id="detail"]');
        $htmlDivStart = $parentDiv->getAttribute("outerHTML");

        $htmlDivStart = "<!DOCTYPE html>" . $htmlDivStart;
        //khai bao cho the svg
        $htmlDivStart = str_replace("<svg ", "<svg xmlns='http://www.w3.org/2000/svg'", $htmlDivStart);
        end:
        return $htmlDivStart;
    }
    public function getDivTracker()
    {

        $htmlTRacker = "";
        try {
            $this->seleniumDriver->clickButton("a[href='#/match-summary/live-commentary']");
        } catch (Exception $e) {
            goto end;
        }
        sleep(1);
        $parentDiv = $this->seleniumDriver->findElement('div[id="detail"]');
        $htmlTRacker = $parentDiv->getAttribute("outerHTML");

        $htmlTRacker = "<!DOCTYPE html>" . $htmlTRacker;
        //khai bao cho the svg
        $htmlTRacker = str_replace("<svg ", "<svg xmlns='http://www.w3.org/2000/svg'", $htmlTRacker);
        end:
        return $htmlTRacker;
    }

    public function crawlDetailInfo()
    {
        $info = [];
        $divCrawl =  str_get_html($this->divInfo);
        if (!$divCrawl) {
            return [
                'info' => [],
                'match' => [],

            ];
        }

        $divsInfo = $divCrawl->find("div[elementtiming='SpeedCurveFRP'] > div");
        foreach ($divsInfo as $div) {
            $temp = [];
            $timeNow = 0;
            $homeEvent = "";
            $homeDescription = "";
            $homeText = "";
            $assistHome = "";
            $homeSubIncident = "";
            $homeincidentSubOut = "";
            $homeScore = "";

            $awayEvent = "";
            $awayDescription = "";
            $awayText = "";
            $assistAway = "";
            $awaySubIncident = "";
            $awayincidentSubOut = "";
            $awayScore = "";
            $classDiv = $div->getAttribute('class');

            if (strpos($classDiv, 'smv__homeParticipant') !== false) {
                $time = $div->find(".smv__timeBox", 0);
                if ($time) {
                    $timeNow = $time->text();
                }

                $description = $div->find("div > .smv__incidentIcon", 0);
                if (!$description) {
                    $description = $div->find("div > .smv__incidentIconSub", 0);
                }
                if ($description) {

                    $arrEvent = $this->getEvent($description);

                    $homeEvent = $arrEvent['event'];
                    $homeDescription = $arrEvent['strDescription'];
                }

                $home = $div->find("a", 0);
                if ($home) {
                    $homeText = $home->text();
                }
                $assist = $div->find(".smv__assist", 0);
                if ($assist) {
                    $assistHome = $assist->text();
                }
                $supIncident = $div->find(".smv__subIncident", 0);
                if ($supIncident) {
                    $homeSubIncident = $supIncident->text();
                }
                $incidentSubOut = $div->find(".smv__incidentSubOut", 0);
                if ($incidentSubOut) {
                    $homeincidentSubOut = $incidentSubOut->text();
                }
                $homeScoreDiv = $div->find(".smv__incidentHomeScore", 0);
                if ($homeScoreDiv) {
                    $homeScore = $homeScoreDiv->text();
                }
            }
            if (strpos($classDiv, 'smv__awayParticipant') !== false) {
                $time = $div->find(".smv__timeBox", 0);
                if ($time) {
                    $timeNow = $time->text();
                }
                $description = $div->find("div > div", 1);

                if ($description) {
                    $arrEvent = $this->getEvent($description);
                    $awayEvent = $arrEvent['event'];
                    $awayDescription = $arrEvent['strDescription'];
                }

                $away = $div->find("a", 0);
                if ($away) {
                    $awayText = $away->text();
                }
                $assist = $div->find(".smv__assistAway", 0);
                if ($assist) {
                    $assistAway = $assist->text();
                }
                $supIncident = $div->find(".smv__subIncident", 0);
                if ($supIncident) {
                    $awaySubIncident = $supIncident->text();
                }

                $incidentSubOut = $div->find(".smv__incidentSubOut", 0);
                if ($incidentSubOut) {
                    $awayincidentSubOut = $incidentSubOut->text();
                }
                $awayScoreDiv = $div->find(".smv__incidentAwayScore", 0);
                if ($awayScoreDiv) {
                    $awayScore = $awayScoreDiv->text();
                }
            }

            if ($timeNow) {
                $info[] = [
                    'time' => $timeNow,
                    'homeText' => $homeText,
                    'homeAssist' => str_replace(["(", ")"], ["", ""], $assistHome),
                    'homeEvent' => $homeEvent,
                    'homeDescription' => $homeDescription,
                    'homeSubIncident' => str_replace(["(", ")"], ["", ""], $homeSubIncident),
                    'homeincidentSubOut' => str_replace(["(", ")"], ["", ""], $homeincidentSubOut),
                    'homeScore' => $homeScore,

                    'awayText' => $awayText,
                    'awayAssist' => str_replace(["(", ")"], ["", ""], $assistAway),
                    'awayEvent' => $awayEvent,
                    'awayDescription' => $awayDescription,
                    'awaySubIncident' => str_replace(["(", ")"], ["", ""], $awaySubIncident),
                    'awayincidentSubOut' => str_replace(["(", ")"], ["", ""], $awayincidentSubOut),
                    'awayScore' => $awayScore,
                ];
            }
        }

        $homeScore = "";
        $awayScore = "";
        $time = "";
        $divScore = $divCrawl->find(".detailScore__matchInfo > div > span");
        if (isset($divScore[0])) {
            $homeScore = $divScore[0]->innertext();
        }
        if (isset($divScore[2])) {
            $awayScore = $divScore[2]->innertext();
        }
        if (isset($divScore[3])) {
            $time = $divScore[3]->innertext();
        }
        $logo = $divCrawl->find(".participant__image");
        return [
            'info' => $info,
            'match' => [
                'homeScore' => $homeScore,
                'awayScore' => $awayScore,
                'homeLogo' => $logo[0]->getAttribute("src"),
                'awayLogo' => $logo[1]->getAttribute("src"),
                'timeNow' => $time
            ]
        ];
    }
    public function crawlDetailTracker()
    {
        $tracker = [];
        $divCrawl =  str_get_html($this->divTracker);
        if (!$divCrawl) {
            return $tracker;
        }

        $divsStart = $divCrawl->find(".soccer__row");
        foreach ($divsStart as $div) {
            $description = "";
            $event = "";
            $divEvent = $div->find(".soccer__icon", 0);
            if ($divEvent) {
                $event = $this->getEvent($divEvent)['event'];
            }
            $descriptionDiv = $div->find(".soccer__comment", 0);
            if ($descriptionDiv) {
                $description = $descriptionDiv->text();
            }
            $arrTemp = [
                'description' => $description,
                'event' => $event,
                'time' => $div->find(".soccer__time", 0)->text(),
            ];
            $tracker[] = $arrTemp;
        }

        return $tracker;
    }
    public function crawlDetailStarts()
    {
        $start = [];
        $divCrawl =  str_get_html($this->divStart);
        if (!$divCrawl) {
            return $start;
        }

        $divsStart = $divCrawl->find(".stat__row");
        foreach ($divsStart as $div) {
            $arrTemp = [
                'category' => $div->find(".stat__categoryName", 0)->text(),
                'homeValue' => $div->find(".stat__homeValue", 0)->text(),
                'awayValue' => $div->find(".stat__awayValue", 0)->text(),
            ];
            $start[] = $arrTemp;
        }
        return $start;
    }

    public function getEvent($description)
    {
        $event = "";
        $strDescription = "";
        $svg = $description->find("svg", 0);
        //get event
        if ($svg) {
            $hrefIcon = $description->find("use", 0)->getAttribute("xlink:href");
            if (strpos($hrefIcon, "card") !== false) {
                if (strpos($hrefIcon, "red-yellow-card") !== false) {
                    $event = "RedYellowCard";
                } else {
                    if ($svg->text() == "Red Card") {
                        $event = "RedCard";
                    } else {
                        $event = "YellowCard";
                    }
                }
            } else {
                $event = substr($hrefIcon, strpos($hrefIcon, "#") + 1);
            }
        }
        $strDescription = $description->getAttribute("title");
        if (!$strDescription) {
            $divDescription = $description->find('div', 0);
            if ($divDescription) {
                $strDescription = $divDescription->getAttribute("title");
            }
            if (!$strDescription) {
                $divDescription = $description->find("svg > title", 0);
                if ($divDescription) {
                    $strDescription = $divDescription->text();
                }
            }
        }

        return [
            'event' => $event,
            'strDescription' => $strDescription,
        ];
    }
}
