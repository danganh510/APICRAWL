<?php

namespace Score\Api\Controllers;

use Score\Models\ScMatch;
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
        $arrMatch = $matchRepo->getMatch($time, "S");

        foreach ($arrMatch as $key => $match) {

            $home = Team::getTeamById($match['match_home_id']);
            $away = Team::getTeamById($match['match_away_id']);

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
                    'svg' => $home->getTeamLogo(),
                    'score' => [
                        'score' => $match['match_home_score'],
                        'time' => [$match['match_home_score']]
                    ]
                ],
                'awayTeam' => [
                    'id' => $away->getTeamId(),
                    'name' => $away->getTeamName(),
                    'slug' => $this->create_slug(
                        $away->getTeamName(),
                    ),
                    'svg' => $away->getTeamLogo(),
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
    public function detailAction()
    {

        $id = $this->request->get('id');
        $matchInfo = ScMatch::query()
            ->innerJoin('Score\Models\ScTournament', 'match_tournament_id = t.tournament_id', 't')
            ->innerJoin('Score\Models\ScMatchInfo', 'match_id  = i.info_match_id', 'i')
            ->columns("match_tournament_id,match_name,match_home_id,match_away_id,match_home_score,match_away_score,match_id,
        i.info_summary,i.info_time,i.info_stats,t.tournament_name")
            ->where("match_id = :id:",  [
                'id' => $id
            ])->execute();
        $matchInfo = $matchInfo->toArray()[0];
        $info = [
            'id' => $matchInfo['match_id'],
            'name' => $matchInfo['match_name'],
            'tournament' => $matchInfo['tournament_name'],
            'home' => $matchInfo['match_home_id'],
            'away' => $matchInfo['match_away_id'],
            'homeScore' => $matchInfo['match_home_score'],
            'awayScore' => $matchInfo['match_away_score'],
            'summary' => $matchInfo['info_summary'],
            'timeLine' => $matchInfo['info_time'],
            'stats' => $matchInfo['info_stats'],
        ];
        return $info;
    }
}
