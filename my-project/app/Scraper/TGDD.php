<?php

namespace App\Scraper;

use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;
use App\Models\Product;
use App\Models\ScProductTest;

class TGDD
{

    public function scrape()
    {        
        $url = 'https://www.thegioididong.com/dtdd';

        $client = new Client();
   

        $crawler = $client->request('GET', $url);
        $count = 0;
        $crawler->filter('ul.listproduct li.item')->each(
            function (Crawler $node) {              
                $name = $node->filter('h3')->text();

                $price = $node->filter('strong.price')->text();
                $wholeStar = $node->filter('.icon-star')->count();
                $halfStar = $node->filter('.icon-star-half')->count();
                $rate = $wholeStar + 0.5 * $halfStar;
                $price = preg_replace('/\D/', '', $price);
                $product = new ScProductTest;
                $product->product_name = $name;
                $product->product_price = floatval($price);
                $product->product_rate = $rate;
                $product->save();
            }
        );
    
    }
}