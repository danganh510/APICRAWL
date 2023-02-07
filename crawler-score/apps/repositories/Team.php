<?php

namespace Score\Repositories;

use Score\Models\ForexcecConfig;
use Phalcon\Mvc\User\Component;
use Score\Models\ScTeam;
use Symfony\Component\DomCrawler\Crawler;

class Team extends Component
{
    public static function findByName($name) {
        return ScTeam::findFirst([
            'team_name = :name:',
            'bind' => [
                'name' => $name
            ]
        ]);
    }
    public static function saveTeam($team_name,$image) {
        $team = new ScTeam();
        $team->setTeamName($team_name);
        $team->setTeamSvg($image);
        $team->setTeamActive("Y");
        $team->save();
        return $team;
    }
    public static function getTeamById($team_id) {
        $team = ScTeam::findFirst([
            'team_id = :id:',
            'bind' => [
                'id' => $team_id
            ]
        ]);
        return $team ;
    }
}
 