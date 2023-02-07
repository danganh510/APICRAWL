<?php
namespace Score\Api\Controllers;

use Score\Repositories\Article;
use Score\Repositories\Page;
use Score\Repositories\Type;
use Score\Utils\Validator;

class NewsController extends ControllerBase
{
    public function indexAction()
    {
        $type_id = $this->globalVariable->typeNewsId;
        $repoType = new Type();
        $type = $repoType->getTypeById($type_id, $this->lang_code);
        if(!$type){
            $this->my->sendErrorEmailAndRedirectToNotFoundPage($this->lang_code, $this->location_code);
            return;
        }
        $page = new Page();
        $page->AutoGenMetaPage("news",defined('txt_news') ? txt_news : '',$this->lang_code);
        $page->generateStylePage('news');
        $repoArticle = new Article();
        $recent_articles = $repoArticle->getByArrTypeAndInsertTimeIsHomeY($type_id,$this->lang_code,5);
        $arr_recent_article_id = '';
        foreach ($recent_articles as $key_recent => $recent_article){
            $arr_recent_article_id .= ($key_recent!=0 ? ',' : '').$recent_article->getArticleId();
        }
        $this->view->setVars([
            'parent_keyword'        => 'news',
            'type_id'               => $type_id,
            'recent_articles'       => $recent_articles,
            'types'                 => $repoType->getTypeByParent($type_id,$this->lang_code),
            'arr_recent_article_id' => $arr_recent_article_id,
        ]);
    }
    public function typeAction()
    {
        $page = new Page();
        $page->generateParentBread("news",defined('txt_news') ? txt_news : '', $this->lang_code);
        $page->generateStylePage('news');
        $type_id = $this->globalVariable->typeNewsId;
        $parent_keyword = 'news';
        $repoType = new Type();
        $type = $repoType->getTypeById($type_id,$this->lang_code);
        if(!$type){
            $this->my->sendErrorEmailAndRedirectToNotFoundPage($this->lang_code, $this->location_code);
            return;
        }
        $type_keyword = $this->dispatcher->getParam("type-key");
        $type_child = $repoType->getTypeByKeyword($type_keyword,$this->lang_code);
        if(!$type_child){
            $this->dispatcher->forward(array(
                "controller" => "news",
                "action" => "detail"
            ));
            return;
        }
        $type_child_id = $type_child->getTypeId();
        $arrParameter = array();
        if ($this->lang_code && $this->lang_code != $this->globalVariable->defaultLanguage) {
            $count_sql = "SELECT COUNT(*) AS count ";
            $table_sql = " FROM \Score\Models\ScArticle n  
                        INNER JOIN \Score\Models\ScArticleLang nl ON nl.article_id = n.article_id AND nl.article_lang_code = :LANG: 
                        WHERE n.article_active = 'Y' AND n.article_type_id = $type_child_id 
                        ORDER BY n.article_insert_time DESC 
	                  ";
            $select_sql = " SELECT nl.article_name,nl.article_summary,nl.article_icon,nl.article_meta_image,n.article_icon_large,n.article_icon_large_mobile,n.article_insert_time,n.article_keyword ";
            $arrParameter = array("LANG" => $this->lang_code);
        }else{
            $count_sql = "SELECT COUNT(*) AS count ";
            $table_sql = " FROM \Score\Models\ScArticle n  
                      WHERE n.article_active = 'Y' AND n.article_type_id = $type_child_id 
                      ORDER BY n.article_insert_time DESC 
	                  ";
            $select_sql = " SELECT n.article_name,n.article_summary,n.article_icon,n.article_meta_image,n.article_icon_large,n.article_icon_large_mobile,n.article_insert_time,n.article_keyword ";
        }
        $count_query = $this->modelsManager->executeQuery($count_sql.$table_sql,$arrParameter);
        $validator = new Validator();
        $current_page = $this->request->getQuery('page');
        $current_page =isset($current_page)?$current_page:1;
        $totalItems = $count_query[0]->count;
        $check = false;
        if ((isset($current_page))&&($validator->validInt($current_page) == false || $current_page < 1)) {
            $current_page = 1;
            $check = true;
        }
        $itemsPerPage = 11;
        $urlPage = '?';
        if ($urlPage != '?') $urlPage .= '&';
        $urlPattern = $urlPage.'page=(:num)';
        $paginator = new \Paginator($totalItems, $itemsPerPage, $current_page, $urlPattern);
        $offset = ($current_page-1) * $itemsPerPage;
        $limit_sql = " LIMIT ".$offset.",".$itemsPerPage;
        $articles = $this->modelsManager->executeQuery($select_sql.$table_sql.$limit_sql,$arrParameter);
        if(($paginator->getNumPages() > 0)&&($current_page > $paginator->getNumPages())){
            $current_page = $paginator->getNumPages();
            $check = true;
        }
        if($check) {
            $urlPage .= 'page=' . $current_page;
            $this->response->redirect($this->lang_url.'/'.$parent_keyword.'/'.$type_keyword.$urlPage . '');
            return;
        }
        if($this->isMobile){
            $paginator->setMaxPagesToShow(5);
        }else{
            $paginator->setMaxPagesToShow(6);
        }
        $this->tag->setTitle($type_child->getTypeTitle());
        $this->view->setVars([
            'parent_keyword'    => $parent_keyword,
            'type_keyword'      => $type_keyword,
            'type_child'        => $type_child,
            'articles'          => $articles,
            'htmlPaginator'     => $paginator->toHtmlFrontend(),
            'meta_key'          => $type_child->getTypeMetaKeyword(),
            'meta_descript'     => $type_child->getTypeMetaDescription(),
            'menu_bread'        => $type_child->getTypeName(),
        ]);
    }
    public function detailAction()
    {
        $this->view->pick('news/detail');
        $page = new Page();
        $page->generateParentBread("news",defined('txt_news') ? txt_news : '', $this->lang_code);
        $page->generateStylePage('news');
        $type_id = $this->globalVariable->typeNewsId;
        $repoType = new Type();
        $type = $repoType->getTypeById($type_id,$this->lang_code);
//        if(!$type){
//            $this->my->sendErrorEmailAndRedirectToNotFoundPage($this->lang_code, $this->location_code);
//            return;
//        }
        $repoArticle = new Article();
        $ar_keyword = $this->dispatcher->getParam("type-key");
        $article = $repoArticle->getByKey($ar_keyword,$this->lang_code);
        if (!$article) {
            $this->my->sendErrorEmailAndRedirectToNotFoundPage($this->lang_code, $this->location_code);
            return;
        }
        $type_child = $repoType->getTypeById($article->getArticleTypeId(),$this->lang_code);
        if(!$type_child){
            $this->my->sendErrorEmailAndRedirectToNotFoundPage($this->lang_code, $this->location_code);
            return;
        }
        if($type_child->getTypeParentId() != $type_id){
            $this->my->sendErrorEmailAndRedirectToNotFoundPage($this->lang_code, $this->location_code);
            return;
        }
        $this->tag->setTitle(html_entity_decode($article->getArticleTitle(),ENT_QUOTES));
        $this->view->setVars([
            'parent_keyword'    => 'news',
            'type_keyword'      => $type_child->getTypeKeyword(),
            'keyword'           => $ar_keyword,
            'type_child'        => $type_child,
            'article'           => $article,
            'meta_key'          => $article->getArticleMetaKeyword(),
            'meta_descript'     => $article->getArticleMetaDescription(),
            'meta_social_image' => $article->getArticleMetaImage(),
            'menu_bread'        => $article->getArticleName(),
            'ar_time'           => $article->getArticleInsertTime(),
            'ar_content'        => $article->getArticleContent($this->lang_url_slashed),
            'related_articles'  => $repoArticle->getRelatedByKeyAndType($ar_keyword,$type_child->getTypeId(),$this->lang_code,3),
            'related_types'     => $repoType->getRelatedTypeByKeywordAndParent($type_child->getTypeKeyword(),$type_id,$this->lang_code)
        ]);
    }
}