<?php

namespace Score\Repositories;

use Goutte\Client;
use GuzzleHttp\Promise\Promise;
use Score\Models\ForexcecConfig;
use Phalcon\Mvc\User\Component;
use Symfony\Component\DomCrawler\Crawler;

class CrawlerDetailAsyn extends Component
{
    public function crawlDetailInfoAsync($url)
    {
        return new Promise(function ($resolve, $reject) use ($url) {
            $crawler = new CrawlerScore();
            $result = $crawler->crawlDetailInfo($url . "&tab=info");
            $resolve($result);
        });
    }

    public function crawlDetailTrackerAsync($url)
    {
        return new Promise(function ($resolve, $reject) use ($url) {
            $crawler = new CrawlerScore();
            $result = $crawler->crawlDetailTracker($url . "&tab=tracker");
            $resolve($result);
        });
    }

    public function crawlDetailStartsAsync($url)
    {
        return new Promise(function ($resolve, $reject) use ($url) {
            $crawler = new CrawlerScore();
            $result = $crawler->crawlDetailStarts($url . "&tab=statistics");
            $resolve($result);
        });
    }
}
