<?php

namespace Score\Models;

class ScTeam extends \Phalcon\Mvc\Model
{
    protected $team_id;
    protected $team_name;
    protected $team_slug;
    protected $team_name_sofa;
    protected $team_name_livescore;

    protected $team_name_flashscore;
    protected $team_logo;
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
    public function getTeamSlug()
    {
        return $this->team_slug;
    }

    /**
     * @param mixed $team_slug
     */
    public function setTeamSlug($team_slug)
    {
        $this->team_slug = $team_slug;
    }


    /**
     * @return mixed
     */
    public function getTeamNameSofa()
    {
        return $this->team_name_sofa;
    }

    /**
     * @param mixed $team_name_sofa
     */
    public function setTeamNameSofa($team_name_sofa)
    {
        $this->team_name_sofa = $team_name_sofa;
    }

    /**
     * @return mixed
     */
    public function getTeamNameLivescore()
    {
        return $this->team_name_livescore;
    }

    /**
     * @param mixed $team_name_livescore
     */
    public function setTeamNameLivescore($team_name_livescore)
    {
        $this->team_name_livescore = $team_name_livescore;
    }
    /**
     * @return mixed
     */
    public function getTeamNameFlashscore()
    {
        return $this->team_name_flashscore;
    }

    /**
     * @param mixed $team_name_flashscore
     */
    public function setTeamNameFlashscore($team_name_flashscore)
    {
        $this->team_name_flashscore = $team_name_flashscore;
    }

    /**
     * @return mixed
     */
    public function getTeamLogo()
    {
        return $this->team_logo;
    }

    /**
     * @param mixed $team_logo
     */
    public function setTeamLogo($team_logo)
    {
        $this->team_logo = $team_logo;
    }

    /**
     * @return mixed
     */
    public function getTeamActive()
    {
        return $this->team_active;
    }

    /**
     * @param mixed $team_active	
     */
    public function setTeamActive($team_active)
    {
        $this->team_active     = $team_active;
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
