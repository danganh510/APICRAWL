<?php

namespace Score\Backend\Controllers;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Score\Models\ForexcecConfig;
use Score\Models\ForexcecLanguage;
use Score\Repositories\Config;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;
use Score\Repositories\Activity;
use Score\Repositories\Language;
use Facebook\WebDriver\Firefox\FirefoxProfile;
use Facebook\WebDriver\Firefox\FirefoxDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;

class IndexController extends ControllerBase
{

    public function indexAction()
    {
        
    }
}
