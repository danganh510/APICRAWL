<?php

namespace Score\Models;

class ScMatch extends \Phalcon\Mvc\Model
{
    protected $match_id;
    protected $match_tournament_id;
    protected $match_name;
    protected $match_status;
    protected $match_home_id;
    protected $match_away_id;
    protected $match_home_score;
    protected $match_away_score;
    protected $match_insert_time;
    protected $match_time;
    protected $match_start_time;
    protected $match_start_day;
    protected $match_start_month;
    protected $match_start_year;
    protected $match_order;
    protected $match_link_detail;


    /**
     * @return mixed
     */
    public function getMatchId()
    {
        return $this->match_id;
    }

    /**
     * @param mixed $match_id
     */
    public function setMatchId($match_id)
    {
        $this->match_id = $match_id;
    }
   /**
     * @return mixed
     */
    public function getMatchTournamentId()
    {
        return $this->match_tournament_id;
    }

    /**
     * @param mixed $match_tournament_id
     */
    public function setMatchTournamentId($match_tournament_id)
    {
        $this->match_tournament_id = $match_tournament_id;
    }
    /**
     * @return mixed
     */
    public function getMatchName()
    {
        return $this->match_name;
    }

    /**
     * @param mixed $match_name
     */
    public function setMatchName($match_name)
    {
        $this->match_name = $match_name;
    }

    /**
     * @return mixed
     */
    public function getMatchStatus()
    {
        return $this->match_status;
    }

    /**
     * @param mixed $match_status
     */
    public function setMatchStatus($match_status)
    {
        $this->match_status = $match_status;
    }

    /**
     * @return mixed
     */
    public function getMatchHomeId()
    {
        return $this->match_home_id;
    }

    /**
     * @param mixed $match_home_id
     */
    public function setMatchHomeId($match_home_id)
    {
        $this->match_home_id = $match_home_id;
    }

    /**
     * @return mixed
     */
    public function getMatchAwayId()
    {
        return $this->match_away_id;
    }

    /**
     * @param mixed $match_away_id
     */
    public function setMatchAwayId($match_away_id)
    {
        $this->match_away_id = $match_away_id;
    }

    /**
     * @return mixed
     */
    public function getMatchHomeScore()
    {
        return $this->match_home_score;
    }

    /**
     * @param mixed $match_home_score
     */
    public function setMatchHomeScore($match_home_score)
    {
        $this->match_home_score = $match_home_score;
    }

    /**
     * @return mixed
     */
    public function getMatchAwayScore()
    {
        return $this->match_away_score;
    }

    /**
     * @param mixed $match_away_score
     */
    public function setMatchAwayScore($match_away_score)
    {
        $this->match_away_score = $match_away_score;
    }

    /**
     * @return mixed
     */
    public function getMatchInsertTime()
    {
        return $this->match_insert_time;
    }

    /**
     * @param mixed $match_insert_time
     */
    public function setMatchInsertTime($match_insert_time)
    {
        $this->match_insert_time = $match_insert_time;
    }

    /**
     * @return mixed
     */
    public function getMatchTime()
    {
        return $this->match_time;
    }

    /**
     * @param mixed $match_time
     */
    public function setMatchTime($match_time)
    {
        $this->match_time = $match_time;
    }

    /**
     * @return mixed
     */
    public function getMatchStartTime()
    {
        return $this->match_start_time;
    }

    /**
     * @param mixed $match_start_time
     */
    public function setMatchStartTime($match_start_time)
    {
        $this->match_start_time = $match_start_time;
    }
        /**
     * @return mixed
     */
    public function getMatchStartDay()
    {
        return $this->match_start_day;
    }

    /**
     * @param mixed $match_start_day
     */
    public function setMatchStartDay($match_start_day)
    {
        $this->match_start_day = $match_start_day;
    }
        /**
     * @return mixed
     */
    public function getMatchStartMonth()
    {
        return $this->match_start_month;
    }

    /**
     * @param mixed $match_start_month
     */
    public function setMatchStartMonth($match_start_month)
    {
        $this->match_start_month = $match_start_month;
    }
        /**
     * @return mixed
     */
    public function getMatchStartYear()
    {
        return $this->match_start_year;
    }

    /**
     * @param mixed $match_start_year
     */
    public function setMatchStartYear($match_start_year)
    {
        $this->match_start_year = $match_start_year;
    }

    /**
     * @return mixed
     */
    public function getMatchOrder()
    {
        return $this->match_order;
    }


    /**
     * @param mixed $match_order
     */
    public function setMatchOrder($match_order)
    {
        $this->match_order = $match_order;
    }
   /**
     * @return mixed
     */
    public function getMatchLinkDetail()
    {
        return $this->match_link_detail;
    }


    /**
     * @param mixed $match_link_detail
     */
    public function setMatchLinkDetail($match_link_detail)
    {
        $this->match_link_detail = $match_link_detail;
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return ScMatch[]|ScMatch
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return ScMatch
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

 
}