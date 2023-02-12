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
    public static function saveTournament($tournamentInfo,$type_crawl) {
        $tournament = new ScTournament();
        $tournament->setTournamentName($tournamentInfo->getTournamentName());
        $tournament->setTournamentSlug(MyRepo::create_slug($tournamentInfo->getTournamentName()));
        $tournament->setTournamentImage("");
        $tournament->setTournamentCountry($tournamentInfo->getCountryName());
        if ($type_crawl == MatchCrawl::TYPE_FLASH_SCORE) {
            $tournament->setTournamentNameFlashScore($tournamentInfo->getTournamentName());
            $tournament->setTournamentHrefFlashscore($tournamentInfo->getTournamentHref());
        }
        $tournament->setTournamentActive("Y");
        $tournament->setTournamentOrder($tournamentInfo->getId());
        $tournament->save();
      
        return $tournament;
    }

}
 