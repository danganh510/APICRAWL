<?php

namespace Score\Models;

class ScMatchInfo extends \Phalcon\Mvc\Model
{
    public $info_id;
    public $info_match_id;
    public $info_summary;
    public $info_time;
    public $info_stats;
    public $info_H2H;
    public $info_H2H_home;
    public $info_H2H_away;

    public function initialize()
    {
        $this->setSource('info');
    }

    public function setInfoId($id)
    {
        $this->info_id = $id;
    }

    public function getInfoId()
    {
        return $this->info_id;
    }

    public function setInfoMatchId($matchId)
    {
        $this->info_match_id = $matchId;
    }

    public function getInfoMatchId()
    {
        return $this->info_match_id;
    }

    public function setInfoSummary($summary)
    {
        $this->info_summary = $summary;
    }

    public function getInfoSummary()
    {
        return $this->info_summary;
    }

    public function setInfoTime($time)
    {
        $this->info_time = $time;
    }

    public function getInfoTime()
    {
        return $this->info_time;
    }

    public function setInfoStats($stats)
    {
        $this->info_stats = $stats;
    }

    public function getInfoStats()
    {
        return $this->info_stats;
    }

    public function setInfoH2h($h2h)
    {
        $this->info_H2H = $h2h;
    }

    public function getInfoH2h()
    {
        return $this->info_H2H;
    }

    public function setInfoH2hHome($h2hHome)
    {
        $this->info_H2H_home = $h2hHome;
    }

    public function getInfoH2hHome()
    {
        return $this->info_H2H_home;
    }

    public function setInfoH2hAway($h2hAway)
    {
        $this->info_H2H_away = $h2hAway;
    }

    public function getInfoH2hAway()
    {
        return $this->info_H2H_away;
    }
    public function getSource()
    {
        return 'sc_match_info';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return ScMatchInfo[]|ScMatchInfo
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return ScMatchInfo
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }
}
