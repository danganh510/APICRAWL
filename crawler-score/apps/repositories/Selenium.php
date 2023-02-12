<?php

namespace Score\Repositories;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Phalcon\Mvc\User\Component;


class Selenium extends Component
{
    public $driver;

    public function __construct($url)
    {
        $ip = 'selenium-hub';

         //$ip = "13.250.21.188";
        $port = 4444;

        $connection = @fsockopen($ip, $port);

        if (is_resource($connection)) {
            echo 'Server is up and running.';
            fclose($connection);
        } else {
            echo 'Server is down.';
            die();
        }
        // exit;
        $host = "http://$ip:4444/wd/hub"; // URL của máy chủ Selenium
        $this->driver = RemoteWebDriver::create($host, DesiredCapabilities::chrome());
        $this->setURL($url);
    }
    public function setURL($url = 'https://www.sofascore.com/football')
    {
        $this->driver->get($url);
        //wait javascript load
        sleep(3);
    }
    public function clickButton($domButton)
    {
        //$doomButton = 'button[data-tabid="mobileSportListType.true"]';
        $button = $this->driver->findElement(WebDriverBy::cssSelector($domButton));
        $button->click();
        sleep(2);
    }
    public function findElements($domElement)
    {
        //$domElement = 'div[aria-readonly="true"] > div >div ';
        $elements = $this->driver->findElements(WebDriverBy::cssSelector($domElement));
        return $elements;
    }
    public function quit()
    {
        return $this->driver->quit();
    }
}
