<?php
namespace Score\Api\Controllers;

use Score\Models\ScNewspaper;
use Score\Repositories\NewspaperArticle;
use Score\Repositories\Page;

class NewspapersController extends ControllerBase
{
    public function indexAction()
    {
        $parent_keyword = 'newspapers';
        $page = new Page();
        $page->AutoGenMetaPage($parent_keyword,'Newspapers',$this->lang_code);
        $page->generateStylePage($parent_keyword);
        $this->view->setVars([
            'parent_keyword' => $parent_keyword,
            'newspapers'     => ScNewspaper::findAllNewspaper()
        ]);
    }
    public function detailAction()
    {
        $parent_keyword = 'newspapers';
        $page = new Page();
        $page->generateParentBread($parent_keyword,'Newspapers',$this->lang_code);
        $ar_key = $this->dispatcher->getParam("ar-key");
        $newspaper = ScNewspaper::findFirstNewspaperByKey($ar_key);
        if(!$newspaper){
            $this->my->sendErrorEmailAndRedirectToNotFoundPage($this->lang_code, $this->location_code);
            return;
        }
        $repoNewspaperArticle = new NewspaperArticle();
        $newspaperArticles = $repoNewspaperArticle->getByNewspaperIdAndInsertTime($newspaper->getNewspaperId(),$this->lang_code);
        $relatedNewspapers = ScNewspaper::findRelatedByNewspaperKey($ar_key);
        $this->tag->setTitle($newspaper->getNewspaperTitle());
        $this->view->setVars([
            'parent_keyword'    => $parent_keyword,
            'newspaper'         => $newspaper,
            'keyword'           => $ar_key,
            'meta_key'          => $newspaper->getNewspaperMetaKeyword(),
            'meta_descript'     => $newspaper->getNewspaperMetaDescription(),
            'meta_social_image' => $newspaper->getNewspaperMetaImage(),
            'menu_bread'        => $newspaper->getNewspaperName(),
            'newspaperArticles' => $newspaperArticles,
            'relatedNewspapers' => $relatedNewspapers
        ]);
    }
    public function getdataAction(){
        $html = '';
        $id = $this->request->getPost('id', array('string', 'trim'));
        $limit = 6;
        $repoNewspaperArticle = new NewspaperArticle();
        $articles = $repoNewspaperArticle->getByNewspaperIdAndInsertTime($id,$this->lang_code,$limit);
        if(count($articles)>0){
            if($this->isMobile) {
                $html .= '<div class="newspaper-articles">';
            }
            $html .= '<div class="box-gray-content">';
            $html .= '<div class="row">';
            foreach ($articles as $article){
                $html .= '<div class="col-lg-4">';
                $html .= '<a href="'.$article->getArticleLink().'" target="_blank" title="'.$article->getArticleName().'" class="media">';
                $html .= '<img width="80" src="'.$article->getArticleIcon().'" alt="'.$article->getArticleName().'" title="'.$article->getArticleName().'" class="mr-10">';
                $html .= '<div class="media-body">';
                $html .= '<h3 class="text-black text-16 text-normal">';
                $html .= $article->getArticleName();
                $html .= '</h3>';
                $html .= '</div>';
                $html .= '</a>';
                $html .= '</div>';
            }
            $html .= '</div>';
            $html .= '</div>';
            if($this->isMobile) {
                $html .= '</div>';
            }
        }
        die($html);
    }
}