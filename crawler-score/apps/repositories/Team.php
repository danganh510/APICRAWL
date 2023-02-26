<?php

namespace Score\Repositories;

use Score\Models\ForexcecConfig;
use Phalcon\Mvc\User\Component;
use Score\Models\ScTeam;
use Symfony\Component\DomCrawler\Crawler;

class Team extends Component
{
    public static function findByName($name, $name_slug, $type_crawl)
    {
        switch ($type_crawl) {
            case "flashScore":
                return ScTeam::findFirst([
                    'team_name_flashscore = :name: OR team_name = :name: OR team_slug= :slug: OR team_name_livescore = :name:',
                    'bind' => [
                        'name' => $name,
                        'slug' => $name_slug
                    ]
                ]);
            default:
                return ScTeam::findFirst([
                    'team_name_flashscore = :name: OR team_name = :name: OR team_slug= :slug: OR team_name_livescore = :name:',
                    'bind' => [
                        'name' => $name,
                        'slug' => $name_slug
                    ]
                ]);
        }
    }
    public static function saveTeam($team_name, $image, $type)
    {
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
