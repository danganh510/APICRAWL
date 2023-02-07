<?php

use Phalcon\Mvc\User\Component;

class GlobalVariable extends Component
{
	public $acceptUploadTypes;
    public $timeZone;
    public $curTime;
    public $localTime;
    public $defaultLocation;
    public $defaultLanguage;
    public $global;
    public $defaultCountry;
    public $contentTypeImages;
    public $typeInformationId;
    public $typeAboutUsId;
    public $typeTradingId;
    public $typeEducationId;
    public $typeTradingStrategiesId;
    public $typePromotions;
    public $typeMarketsId;
    public $typePlatformId;
    public $typePartnershipId;
    public $typeFaqsId;
    public $urlFlag;
    public $website;
    public $programmableSearchEngineCxKey;
    public $cronPassword;
    public $mainCurrency;
    public $tableSEO;


    public function __construct()
	{
        date_default_timezone_set('UTC');//default for Application - NOT ONLY for current script
        $this->timeZone = -4*3600;
        $this->curTime = time();
        $this->localTime = time() + $this->timeZone;
        $this->timeZoneStr = 'UTC -04:00';
        $this->defaultLocation ='gx';
        $this->defaultLanguage ='en';
        $this->defaultCountry = "gb";
        $this->mainCurrency = 'VND';
	    //accept upload file types
		$this->acceptUploadTypes = array(
			"image/jpeg" => array("type" => "image", "ext" => ".jpg"),
			"image/pjpeg" => array("type" => "image", "ext" => ".jpg"),
			"image/png" => array("type" => "image", "ext" => ".png"),
			"image/bmp" => array("type" => "image", "ext" => ".bmp"),
			"image/x-windows-bmp" => array("type" => "image", "ext" => ".bmp"),
			"image/x-icon" => array("type" => "image", "ext" => ".ico"),
			"image/ico" => array("type" => "image", "ext" => ".ico"),
			"image/gif" => array("type" => "image", "ext" => ".gif"),
			"text/plain" => array("type" => "file", "ext" => ".txt"),
			"application/msword" => array("type" => "file", "ext" => ".doc"),
			"application/vnd.openxmlformats-officedocument.wordprocessingml.document" => array("type" => "file", "ext" => ".docx"),
			"application/vnd.ms-excel" => array("type" => "file", "ext" => ".xls"),
			"application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" => array("type" => "file", "ext" => ".xlsx"),
			"application/pdf" => array("type" => "file", "ext" => ".pdf"),
		);

        //accept upload file types
        $this->contentTypeImages = array(
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',
            'txt' => 'text/plain',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'pdf'  => 'application/pdf',
        );
        $this->global = array(
            "name" => "Global",
            "code" => "gx",
        );

        $this->typeInformationId = 1;
        $this->typeAboutUsId = 2;
        $this->typeTradingId = 3;
        $this->typeEducationId = 4;
        $this->typeTradingStrategiesId = 5;
        $this->typePromotions = 6;
        $this->typeMarketsId = 7;
        $this->typePlatformId = 8;
        $this->typePartnershipId = 20;
        $this->typeFaqsId = 21;
        $this->urlFlag = 'https://d3nqrmb1lqq5py.cloudfront.net/images/flag/';
        $this->website = 'forexcec.com';
        $this->programmableSearchEngineCxKey = '4f429654962ca4a4a';
        $this->cronPassword = 'k3FRQ1U0bYHUVSu6';
        $this->tableSEO = array(
            'ForexcecArticle' => 'Score\Models\ForexcecArticle',
            'ForexcecArticleLang' => 'Score\Models\ForexcecArticleLang',

            'ForexcecType' => 'Score\Models\ForexcecType',
            'ForexcecTypeLang' => 'Score\Models\ForexcecTypeLang',

            'ForexcecPage' => 'Score\Models\ForexcecPage',
            'ForexcecPageLang' => 'Score\Models\ForexcecPageLang',
        );
    }
}