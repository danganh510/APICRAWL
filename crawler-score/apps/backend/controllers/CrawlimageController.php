<?php

namespace Score\Backend\Controllers;

use Score\Models\ScTeam;
use Score\Repositories\CrawlImage;
use Score\Repositories\Team;

class CrawlimageController extends ControllerBase
{

    public function indexAction()
    {
        $arrTeam = ScTeam::findTeamLogoSmallNull(10);
        $crawlImage = new CrawlImage();
        foreach ($arrTeam as $team) {
            $url_logo = "https://www.flashscore.com" . $team->getTeamLogo();
            $result = $crawlImage->getImage($url_logo, Team::FOLDER_IMAGE_SMALL . "/" . $team->getTeamId());
            var_dump($result);exit;
            $team->setTeamLogoSmall($result['uploadFiles']);
            $team->save();
        }
        echo "succes";
        die();
    }
}
