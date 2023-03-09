<?php

namespace Score\Backend\Controllers;

use Score\Models\ScTeam;
use Score\Repositories\CrawlImage;
use Score\Repositories\Team;

class CrawlimageController extends ControllerBase
{

    public function indexAction()
    {
        $start_time_cron = time() + 0 * 24 * 60 * 60;
        echo "Start crawl logo team small in " . $this->my->formatDateTime($start_time_cron) . "/n/r";
        $arrTeam = ScTeam::findTeamLogoSmallNull(10);
        $crawlImage = new CrawlImage();
        $total = 0;
        foreach ($arrTeam as $team) {
            $url_logo = "https://www.flashscore.com" . $team->getTeamLogo();
            $crawl = $crawlImage->getImage($url_logo, Team::FOLDER_IMAGE_SMALL . "/" . $team->getTeamId());
            $team->setTeamLogoSmall($crawl['uploadFiles']);
            $result = $team->save();
            if ($crawl['status'] == "success") {
                $total++;
            }
        }
        echo "---total: " . $total;

        echo "---finish in " . (time() - $start_time_cron) . " second";
        die();
    }
    public function logomediumAction()
    {
        $start_time_cron = time() + 0 * 24 * 60 * 60;
        echo "Start crawl logo team small in " . $this->my->formatDateTime($start_time_cron) . "/n/r";
        $arrTeam = ScTeam::findTeamLogoSmallNull(10);
        $crawlImage = new CrawlImage();
        $total = 0;
        foreach ($arrTeam as $team) {
            $url_logo = "https://www.flashscore.com" . $team->getTeamLogo();
            $crawl = $crawlImage->getImage($url_logo, Team::FOLDER_IMAGE_SMALL . "/" . $team->getTeamId());
            $team->setTeamLogoSmall($crawl['uploadFiles']);
            $result = $team->save();
            if ($crawl['status'] == "success") {
                $total++;
            }
        }
        echo "---total: " . $total;

        echo "---finish in " . (time() - $start_time_cron) . " second";
        die();
    }
}
