<?php

namespace Score\Models;

class ScTeam extends \Phalcon\Mvc\Model
{
    protected $team_id;
    protected $team_name;
    protected $team_svg;
    protected $team_active;



    /**
     * @return mixed
     */
    public function getTeamId()
    {
        return $this->team_id;
    }

    /**
     * @param mixed $team_id
     */
    public function setTeamId($team_id)
    {
        $this->team_id = $team_id;
    }

    /**
     * @return mixed
     */
    public function getTeamName()
    {
        return $this->team_name;
    }

    /**
     * @param mixed $team_name
     */
    public function setTeamName($team_name)
    {
        $this->team_name = $team_name;
    }

    /**
     * @return mixed
     */
    public function getTeamSvg()
    {
        return $this->team_svg;
    }

    /**
     * @param mixed $team_svg
     */
    public function setTeamSvg($team_svg)
    {
        $this->team_svg = $team_svg;
    }

    /**
     * @return mixed
     */
    public function getTeamActive()
    {
        return $this->team_active	;
    }

    /**
     * @param mixed $team_active	
     */
    public function setTeamActive($team_active	)
    {
        $this->team_active	 = $team_active	;
    }

       /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'sc_team';
    }

    /**
     * Allows to query a set of records that Team the specified conditions
     *
     * @param mixed $parameters
     * @return ScTeam[]|ScTeam
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that Team the specified conditions
     *
     * @param mixed $parameters
     * @return ScTeam
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

 
}