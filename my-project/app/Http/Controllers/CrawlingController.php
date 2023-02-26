<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CrawlingController extends Controller
{
    public function crawlingAction() {
        $bot = new \App\Scraper\TGDD();
        $bot->scrape();
    }
}
