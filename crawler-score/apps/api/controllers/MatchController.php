<?php

namespace Score\Api\Controllers;

use Score\Repositories\Article;
use Score\Repositories\Banner;
use Score\Repositories\Career;
use Score\Repositories\MatchRepo;
use Score\Repositories\Page;
use Score\Repositories\Team;

class MatchController extends ControllerBase
{
    public function listAction()
    {
        //get các trận cần lấy theo thời gian
        /*
        return match:
        [
            tournament => [
                'name' => "name",

            ]
        ]
        */

        $time = $this->requestParams['time'];
        //live
        if (!$time || $time == "live") {
            $time = time();
        }
        $events = [];
        $matchRepo = new MatchRepo();
        $arrMatch = $matchRepo->getMatch($time);
        foreach ($arrMatch as $key=> $match) {
           
            $home = Team::getTeamById($match['match_home_id']);
            $away = Team::getTeamById($match['match_away_id']);
            if (!$home || !$away) {
                continue;
            }
            $matchInfo = [
                'status' => [
                    'description' => $match['match_status'],
                    'type' => $match['match_status']
                ],
                'matchInfo' => [
                    'id' => $match['match_id'],
                    'time_start' => $match['match_start_time'],
                    'time' => $match['match_time'],
                ],
                'homeTeam' => [
                    'id' => $home->getTeamId(),
                    'name' => $home->getTeamName(),
                    'slug' => $this->create_slug($home->getTeamName()),
                    'svg' => "svg",
                    'score' => [
                        'score' => $match['match_home_score'],
                        'time' => [$match['match_home_score']]
                    ]
                ],
                'awayTeam' => [
                    'id' => $away->getTeamId(),
                    'name' => $away->getTeamName(),
                    'slug' => $this->create_slug($away->getTeamName(),
                    ),
                    'svg' => "svg",
                    'score' => [
                        'score' => $match['match_away_score'],
                        'time' => [$match['match_home_score']]
                    ]
                ],
            ];
            if (isset($events[$match['tournament_id']])) {
                $events[$match['tournament_id']]['match'][$match['match_id']] = $matchInfo;
            } else {
                $events[$match['tournament_id']] = [
                    'tournament' => [
                        'name' => $match['tournament_name'],
                        'slug' => $this->create_slug($match['tournament_name']),
                        'roundInfo' => $match['tournament_round'],
                        'category' => [
                            'name' => $match['tournament_country'],
                            'slug' => $match['tournament_country'],
                            'sport' => [
                                'name' => "football",
                                'slug' => "football"
                            ],
                            'flag' => $match['tournament_country'],
                            'countryCode' => "countryCode"
                        ]
                    ],
                    'match' => [
                        $match['match_id'] => $matchInfo
                    ]
     
                ];
            }
         
           
        }
      
        return $events;
        //get match and tournament

    }
}
