<?php

namespace Score\Models;

class ScTournament extends \Phalcon\Mvc\Model
{
    protected $tournament_id ;
    protected $tournament_name;
    protected $tournament_country;
    protected $tournament_image;
    protected $tournament_order;
    protected $tournament_active;



    /**
     * @return mixed
     */
    public function getTournamentId()
    {
        return $this->tournament_id ;
    }

    /**
     * @param mixed $tournament_id 
     */
    public function setTournamentId($tournament_id )
    {
        $this->tournament_id  = $tournament_id ;
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
        return $this->tournament_active	;
    }

    /**
     * @param mixed $tournament_active	
     */
    public function setTournamentActive($tournament_active	)
    {
        $this->tournament_active	 = $tournament_active	;
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