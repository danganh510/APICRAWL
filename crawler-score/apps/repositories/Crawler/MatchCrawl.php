<?php

namespace Score\Repositories;
use Phalcon\Mvc\User\Component;

class MatchCrawl extends Component
{
    const TYPE_FLASH_SCORE = "flashScore";
    const TYPE_SOFA = "sofa";
    const TYPE_API_SOFA = "apisofa";
    const TYPE_LIVE_SCORES = "liveScores";

    const TYPE_default = "default";

    
    private $time;
    private $start_time;

    private $home;
    private $home_score;
    private $home_img;
    private $away;
    private $away_score;
    private $away_img;
    private $href_detail;
    private $tournament;
    public function getTime() {
        return $this->time;
    }
    public function getStartTime() {
        return $this->start_time;
    }
    public function getHome() {
        return $this->home;
    }
    public function getHomeScore() {
        return $this->home_score;
    }
    public function getHomeImg() {
        return $this->home_img;
    }
    public function getAway() {
        return $this->away;
    }
    public function getAwayScore() {
        return $this->away_score;
    }
    public function getAwayImg() {
        return $this->away_img;
    }
    public function getHrefDetail() {
        return $this->href_detail;
    }
    public function getTournament() {
        return $this->tournament;
    }
    public function setTime($time) {
        $this->time = $time;
        return $this->time;
    }
    public function setStartTime($start_time) {
        $this->start_time = $start_time;
        return $this->start_time;
    }
    public function setHome($home) {
        $this->home = $home;
        return $this->home;
    }
    public function setHomeScore($home_score) {
        $this->home_score = $home_score;
        return $this->home_score;
    }
    public function setHomeImg($away_img) {
        $this->away_img = $away_img;
        return $this->away_img;
    }
    public function setAway($away) {
        $this->away = $away;
        return $this->away;
    }
    public function setAwayScore($away_score) {
        $this->away_score = $away_score;
        return $this->away_score;
    }
    public function setAwayImg($away_img) {
        $this->away_img = $away_img;
        return $this->away_img;
    }
    public function setHrefDetail($href_detail) {
        $this->href_detail = $href_detail;
        return $this->href_detail;
    }
    public function setTournament($tournament) {
        $this->tournament = $tournament;
        return $this->tournament;
    }
    
}