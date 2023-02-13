<?php

namespace Score\Repositories;

use Score\Models\ForexcecConfig;
use Phalcon\Mvc\User\Component;
use Score\Models\ScMatch;
use Symfony\Component\DomCrawler\Crawler;

class MatchRepo extends Component
{
    public  function saveMatch($match, $home, $away, $tournament, $type_crawl)
    {

        $matchSave = ScMatch::findFirst([
            "match_home_id = :home_id: AND match_away_id = :away_id: AND match_status != 'F'",
            'bind' => [
                'home_id' => $home->getTeamId(),
                'away_id' => $away->getTeamId(),
            ]
        ]);

        if (!$matchSave) {
            $matchSave = new ScMatch();
            $matchSave->setMatchName($home->getTeamName() . "-vs-" . $away->getTeamName());
            $matchSave->setMatchHomeId($home->getTeamId());
            $matchSave->setMatchAwayId($away->getTeamId());
            $matchSave->setMatchInsertTime(time());
            if (is_numeric($match->getTime())) {
                $time = $match->getTime();
                $start_time = time() - $time * 60;
            } elseif (strpos($match->getTime(), "'")) {
                $time = str_replace("'", "", $match->getTime());
                $start_time = time() - $time * 60;
            }  elseif (strpos($match->getTime(), "+")) {
                $time = str_replace("+", "", $match->getTime());
                $start_time = time() - $time * 60;
                $matchSave->setMatchStatus("S");
            } elseif ($match->getTime() == "HT" || $match->getTime() == "Half Time" || $match->getTime() == "HalfTime") {
                $time = 45;
                $start_time = time() - $time * 60;
            } elseif ($match->getTime() == "FT" || $match->getTime() == "AET" || $match->getTime() == "Finished") {
                $time = 90;
                $start_time = time() - $time * 60;
            } else {
                $start_time = $this->my->formatDateTimeSendEmail(time()) . " " . $match->getTime();
                $start_time = strtotime($start_time);
            }
            if (!$start_time) {
                $start_time = $match->getTime();
                $day_start = date('d', time());
                $month_start = date('m', time());
                $year_start = date('Y', time());
            } else {
                $day_start = date('d', $start_time);
                $month_start = date('m', $start_time);
                $year_start = date('Y', $start_time);
            }
            $matchSave->setMatchStartDay($day_start);
            $matchSave->setMatchStartMonth($month_start);
            $matchSave->setMatchStartYear($year_start);
            $matchSave->setMatchStartTime($start_time);
        }
        if (is_numeric($match->getTime())) {
            $time_live = $match->getTime();
            $matchSave->setMatchStatus("S");
        } elseif (strpos($match->getTime(), "'")) {
            $time_live = str_replace("'", "", $match->getTime());
            $matchSave->setMatchStatus("S");
        }  elseif (strpos($match->getTime(), "+")) {
            $time_live = str_replace("+", "", $match->getTime());
            $matchSave->setMatchStatus("S");
        } elseif ($match->getTime() == "HT" || $match->getTime() == "Half Time") {
            $time_live = $match->getTime();
            $matchSave->setMatchStatus("F");
        } elseif ($match->getTime() == "FT" || $match->getTime() == "AET" || $match->getTime() == "Finished") {
            $time_live = $match->getTime();
            $matchSave->setMatchStatus("S");
        } else {
            $time_live = 0;
            $matchSave->setMatchStatus("W");
        }
        $matchSave->setMatchTime($time_live);
        $matchSave->setMatchHomeScore(is_numeric($match->getHomeScore()) ? $match->getHomeScore() : 0);
        $matchSave->setMatchAwayScore(is_numeric($match->getAwayScore()) ? $match->getAwayScore() : 0);
        $matchSave->setMatchTournamentId($tournament->getTournamentId());
        if ($type_crawl == MatchCrawl::TYPE_FLASH_SCORE) {
            $matchSave->setMatchLinkDetailFlashscore($match->getHrefDetail());
        }
        $matchSave->setMatchOrder(1);
        if ($matchSave->save()) {
            return true;
        }
        var_dump($matchSave->getMessages());
        var_dump($match);
        return false;
    }
    public function getMatch($time, $status = "", $tournament = "")
    {
        $day = date('d', $time);
        $month = date('m', $time);
        $year = date('Y', $time);

        $match = ScMatch::query()
            ->innerJoin('Score\Models\ScTournament', 'match_tournament_id = t.tournament_id', 't')
            ->columns("match_id,match_tournament_id,match_name,match_home_id,match_away_id,match_home_score,match_away_score,
            match_insert_time,match_time,match_start_time,match_order,match_status,
            t.tournament_id,t.tournament_name,t.tournament_country,t.tournament_image,t.tournament_order")
            ->andWhere(
                "match_start_day = :day: AND match_start_month = :month: AND match_start_year = :year:",
                [
                    'day' => $day,
                    'month' => $month,
                    'year' => $year
                ]
            );
        if ($status) {
            $match = $match->andWhere("match_status = :status:", ['status' => $status]);
        }
        if ($tournament) {
            $match = $match->andWhere("t.tournament_id = :tournament:", ['tournament' => $tournament]);
        }

        $match = $match->orderBy("match_order")
            ->execute();
        return $match;
    }
}
