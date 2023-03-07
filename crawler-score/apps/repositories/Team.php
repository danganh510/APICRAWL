<?php

namespace Score\Repositories;

use Score\Models\ForexcecConfig;
use Phalcon\Mvc\User\Component;
use Score\Models\ScTeam;
use Symfony\Component\DomCrawler\Crawler;
use Travelnercom\Repositories\CacheTeam;

class Team extends Component
{
    const FOLDER_IMAGE_SMALL = "/images/team/small";
    public static function findByName($name, $name_slug, $country_code = "")
    {
        if ($country_code) {
            return ScTeam::findFirst([
                'team_name_flashscore = :name: OR team_name = :name: OR team_slug= :slug: OR team_name_livescore = :name: AND team_country_code = :countryCode:',
                'bind' => [
                    'countryCode' => $country_code,
                    'name' => $name,
                    'slug' => $name_slug
                ]
            ]);
        } else {
            return ScTeam::findFirst([
                'team_name_flashscore = :name: OR team_name = :name: OR team_slug= :slug: OR team_name_livescore = :name:',
                'bind' => [
                    'name' => $name,
                    'slug' => $name_slug
                ]
            ]);
        }
       
    }
    public static function findByNameArray($name, $name_slug,$arrTeam, $country_code = "")
    {
        $team = array_search($name_slug, array_column($arrTeam, 'team_slug'));
        if (!$team) {
            $team = array_search($name, array_column($arrTeam, 'team_name'));
        }
        if (empty($arrTeam[$team])) {
            return false;
        }
        if ($country_code && $arrTeam[$team]['team_country_code'] &&  $country_code != $arrTeam[$team]['team_country_code']) {
            return false;
        }
        $teamModel = new ScTeam();
        $teamModel->setData($arrTeam[$team]);
        return $teamModel;
    }
    public static function saveTeam($team_name, $image,$country_code,$arrTeam, $type)
    {
        $team = Team::findByNameArray($team_name, MyRepo::create_slug($team_name),$arrTeam,$country_code);
        if (!$team) {
            $team = new ScTeam();
            $team->setTeamName($team_name);
            $team->setTeamLogo($image);
            $team->setTeamSlug(MyRepo::create_slug($team_name));
            switch ($type) {
                case MatchCrawl::TYPE_FLASH_SCORE:
                    $team->setTeamNameFlashscore($team_name);
                    break;
                case MatchCrawl::TYPE_SOFA:
                case MatchCrawl::TYPE_API_SOFA:
                    $team->setTeamNameSofa($team_name);
                    break;
                case MatchCrawl::TYPE_LIVE_SCORES:
                    $team->setTeamNameLivescore($team_name);
                    break;
            }
            $team->setTeamActive("Y");
        }
        if (!$team->getTeamCountryCode()) {
            $team->setTeamCountryCode($country_code);
        }
        $team->save();

        return $team;
    }
    public static function getTeamById($team_id)
    {
        $team = ScTeam::findFirst([
            'team_id = :id:',
            'bind' => [
                'id' => $team_id
            ]
        ]);
        return $team;
    }
}
