<?php
namespace Score\Api\Controllers;

use Score\Repositories\Article;
use Score\Repositories\Banner;
use Score\Repositories\Career;
use Score\Repositories\Page;

class IndexController extends ControllerBase
{
    public function indexAction()
    {
        $page = new Page();
        $page->AutoGenMetaPage("index",defined('txt_brand_name') ? txt_brand_name : '',$this->lang_code);
        $page->generateStylePage('index');
        $repoBanner = new Banner();
        $banners = $repoBanner->getBannerByController($this->router->getControllerName(), $this->lang_code);
        $repoArticle = new Article();
        $service_articles = $repoArticle->getByTypeAndOrder($this->globalVariable->typeServicesId,$this->lang_code);
        $new_articles = $repoArticle->getByArrTypeAndInsertTimeIsHomeY($this->globalVariable->typeNewsId,$this->lang_code,4);
        $this->view->setVars([
            'banners'           => $banners,
            'service_articles'  => $service_articles,
            'new_articles'      => $new_articles,
            'checkCareer'       => false,
        ]);
    }
}