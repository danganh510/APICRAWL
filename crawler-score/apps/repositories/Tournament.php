<?php

namespace Score\Repositories;

use Score\Models\ForexcecConfig;
use Phalcon\Mvc\User\Component;
use Score\Models\ScTournament;
use Symfony\Component\DomCrawler\Crawler;

class Tournament extends Component
{
    public static function findByName($name) {
        return ScTournament::findFirst([
            'tournament_name = :name:',
            'bind' => [
                'name' => $name
            ]
        ]);
    }
    public static function saveTournament($tournamentInfo) {
        $tournament = new ScTournament();
        $tournament->setTournamentName($tournamentInfo['tournament']);
        $tournament->setTournamentImage("");
        $tournament->setTournamentCountry($tournamentInfo['country']);
        $tournament->setTournamentActive("Y");
        $tournament->setTournamentOrder($tournamentInfo['index']);
        $tournament->save();
      
        return $tournament;
    }

}
 