<?php

namespace Score\Models;

class ScTournament extends \Phalcon\Mvc\Model
{
    protected $tournament_id;
    protected $tournament_name;
    protected $tournament_slug;
    protected $tournament_name_flash_score;
    protected $tournament_name_livescore;
    protected $tournament_name_sofa;
    protected $tournament_country_code;
    protected $tournament_country;
    protected $tournament_image;
    protected $tournament_href_flashscore;
    protected $tournament_href_livescore;
    protected $tournament_order;
    protected $tournament_active;



    /**
     * @return mixed
     */
    public function getTournamentId()
    {
        return $this->tournament_id;
    }

    /**
     * @param mixed $tournament_id 
     */
    public function setTournamentId($tournament_id)
    {
        $this->tournament_id  = $tournament_id;
    }

    /**
     * @return mixed
     */
    public function getTournamentName()
    {
        return $this->tournament_name;
    }

    /**
     * @param mixed $tournament_name
     */
    public function setTournamentName($tournament_name)
    {
        $this->tournament_name = $tournament_name;
    }
    /**
     * @return mixed
     */
    public function getTournamentSlug()
    {
        return $this->tournament_slug;
    }

    /**
     * @param mixed $tournament_slug
     */
    public function setTournamentSlug($tournament_slug)
    {
        $this->tournament_slug = $tournament_slug;
    }
    /**
     * @return mixed
     */
    public function getTournamentNameFlashScore()
    {
        return $this->tournament_name_flash_score;
    }

    /**
     * @param mixed $tournament_name_livescore
     */
    public function setTournamentNameLivescore($tournament_name_livescore)
    {
        $this->tournament_name_livescore = $tournament_name_livescore;
    }
    /**
     * @return mixed
     */
    public function getTournamentNameLivescore()
    {
        return $this->tournament_name_livescore;
    }

    /**
     * @param mixed $tournament_name_flash_score
     */
    public function setTournamentNameFlashScore($tournament_name_flash_score)
    {
        $this->tournament_name_flash_score = $tournament_name_flash_score;
    }
    /**
     * @return mixed
     */
    public function getTournamentCountry()
    {
        return $this->tournament_country;
    }

    /**
     * @param mixed $tournament_country
     */
    public function setTournamentCountry($tournament_country)
    {
        $this->tournament_country = $tournament_country;
    }
    /**
     * @return mixed
     */
    public function getTournamentCountryCode()
    {
        return $this->tournament_country_code;
    }

    /**
     * @param mixed $tournament_country_code
     */
    public function setTournamentCountryCode($tournament_country_code)
    {
        $this->tournament_country_code = $tournament_country_code;
    }
    /**
     * @return mixed
     */
    public function getTournamentImage()
    {
        return $this->tournament_image;
    }

    /**
     * @param mixed $tournament_image
     */
    public function setTournamentImage($tournament_image)
    {
        $this->tournament_image = $tournament_image;
    }
    /**
     * @return mixed
     */
    public function getTournamentHrefFlashscore()
    {
        return $this->tournament_href_flashscore;
    }

    /**
     * @param mixed $tournament_href_livescore
     */
    public function setTournamentHrefLivescore($tournament_href_livescore)
    {
        $this->tournament_href_livescore = $tournament_href_livescore;
    }
    /**
     * @return mixed
     */
    public function getTournamentHrefLivescore()
    {
        return $this->tournament_href_livescore;
    }

    /**
     * @param mixed $tournament_href_flashscore
     */
    public function setTournamentHrefFlashscore($tournament_href_flashscore)
    {
        $this->tournament_href_flashscore = $tournament_href_flashscore;
    }
    /**
     * @return mixed
     */
    public function getTournamentOrder()
    {
        return $this->tournament_order;
    }

    /**
     * @param mixed $tournament_order
     */
    public function setTournamentOrder($tournament_order)
    {
        $this->tournament_order = $tournament_order;
    }

    /**
     * @return mixed
     */
    public function getTournamentActive()
    {
        return $this->tournament_active;
    }

    /**
     * @param mixed $tournament_active	
     */
    public function setTournamentActive($tournament_active)
    {
        $this->tournament_active     = $tournament_active;
    }



    /**
     * Allows to query a set of records that Tournament the specified conditions
     *
     * @param mixed $parameters
     * @return ScTournament[]|ScTournament
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that Tournament the specified conditions
     *
     * @param mixed $parameters
     * @return ScTournament
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }
}
