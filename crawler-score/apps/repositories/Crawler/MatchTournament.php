<?php

namespace Score\Repositories;
use Phalcon\Mvc\User\Component;

class MatchTournament extends Component
{
    private $id;
    private $tournament_name;
    private $tournament_group;
    private $country_name;
    private $country_code;
    private $country_image;
    private $tournament_href;
    public function getId() {
        return $this->id;
    }
    public function getTournamentName() {
        return $this->tournament_name;
    }
    public function getTournamentGroup() {
        return $this->tournament_group;
    }
    public function getTournamentHref() {
        return $this->tournament_href;
    }
    public function getCountryName() {
        return $this->country_name;
    }
    public function getCountryCode() {
        return $this->country_code;
    }
    public function getCountryImage() {
        return $this->country_image;
    }
    public function setId($id) {
        $this->id = $id;
        return $this->id;
    }
    public function setTournamentName($tournament_name) {
        $this->tournament_name = $tournament_name;
        return $this->tournament_name;
    }
    public function setTournamentGroup($tournament_group) {
        $this->tournament_group = $tournament_group;
        return $this->tournament_name;
    }
    public function setTournamentHref($tournament_href) {
        $this->tournament_href = $tournament_href;
        return $this->tournament_href;
    }
    public function setCountryName($country_name) {
        $this->country_name = $country_name;
        return $this->country_name;
    }
    public function setCountryCode($country_code) {
        $this->country_code = $country_code;
        return $this->country_code;
    }
    public function setCountryImage($country_image) {
        $this->country_image = $country_image;
        return $this->country_image;
    }
}