<?php

namespace Score\Api\Controllers;

use Score\Models\ScMatch;
use Score\Models\ScTeam;
use Score\Models\ScTournament;
use Score\Repositories\Article;
use Score\Repositories\Banner;
use Score\Repositories\CacheMatch;
use Score\Repositories\CacheMatchLive;
use Score\Repositories\CacheTeam;
use Score\Repositories\CacheTour;
use Score\Repositories\Career;
use Score\Repositories\MatchRepo;
use Score\Repositories\Page;
use Score\Repositories\Team;

class MatchController extends ControllerBase
{
    public function listAction()
    {
        //get các trận cần lấy theo thời gian


        $time_request = isset($this->requestParams['time']) ? $this->requestParams['time'] : "";
        $status = isset($this->requestParams['status']) ?  $this->requestParams['status'] : "";
        //live
        $isLive = false;
        if (!$time_request || $time_request == "live") {
            $isLive = true;
            $time = time();
            $cacheMatch = new CacheMatchLive();
        } else {
            $time = strtotime($time_request);
            $cacheMatch = new CacheMatch();
        }
        $events = [];

        $cacheTeam = new CacheTeam();
        $arrTeam = $cacheTeam->getCache();

        $cacheTour = new CacheTour();
        $arrTournament = $cacheTour->getCache();
        // $matchRepo = new MatchRepo();
        // $arrMatch = $matchRepo->getMatch($time, $status);
        $arrMatch = $cacheMatch->getCache();
        var_dump($arrMatch);exit;
        if (!$arrMatch) {
            goto end;
        }
        foreach ($arrMatch as $key => $match) {
            if (!is_array($match)) {
                $match = (array) $match;
            }
            if (empty($arrTeam[$match['match_home_id']]) || empty($arrTeam[$match['match_away_id']])) {
                continue;
            }
            if (empty($arrTournament[$match['match_tournament_id']])) {
                continue;
            }
            if (!$isLive) {
                if ($this->my->getDays($time, $match['match_start_time']) != 0) {
                    continue;
                }
            }
            if ($status) {
                if ($status != $match['match_status']) {
                    continue;
                }
            }

            //con check điều kiện

            $homeModel = new ScTeam();
            $home = $homeModel->setData($arrTeam[$match['match_home_id']]);

            $awayModel = new ScTeam();
            $away = $awayModel->setData($arrTeam[$match['match_away_id']]);

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
                'roundInfo' => $match['match_round'],
            ];
            if (isset($events[$match['match_tournament_id']])) {
                $events[$match['match_tournament_id']]['match'][$match['match_id']] = $matchInfo;
            } else {
                $events[$match['match_tournament_id']] = [
                    'tournament' => [
                        'name' => $arrTournament[$match['match_tournament_id']]['tournament_name'],
                        'slug' => $this->create_slug($arrTournament[$match['match_tournament_id']]['tournament_name']),
                        'category' => [
                            'name' => $arrTournament[$match['match_tournament_id']]['tournament_country'],
                            'slug' => $arrTournament[$match['match_tournament_id']]['tournament_country'],
                            'sport' => [
                                'name' => "football",
                                'slug' => "football"
                            ],
                            'flag' => $arrTournament[$match['match_tournament_id']]['tournament_country'],
                            'countryCode' => "countryCode"
                        ]
                    ],
                    'match' => [
                        $match['match_id'] => $matchInfo
                    ]

                ];
            }
        }
        end:
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
        $home = Team::getTeamById($matchInfo['match_home_id']);
        $away = Team::getTeamById($matchInfo['match_away_id']);
        $info = [
            'id' => $matchInfo['match_id'],
            'name' => $matchInfo['match_name'],
            'tournament' => $matchInfo['tournament_name'],
            'home' => $home->getTeamName(),
            'away' => $away->getTeamName(),
            'homeSlug' => $home->getTeamSlug(),
            'awaySlug' => $away->getTeamSlug(),
            'homeScore' => $matchInfo['match_home_score'],
            'awayScore' => $matchInfo['match_away_score'],
            'summary' => $matchInfo['info_summary'],
            'timeLine' => $matchInfo['info_time'],
            'stats' => $matchInfo['info_stats'],
        ];
        return $info;
    }
}
