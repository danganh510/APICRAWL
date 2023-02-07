<?php
namespace Score\Api\Controllers;

use Score\Repositories\Page;
use Score\Repositories\SendError;

class NotfoundController extends ControllerBase
{
    public function indexAction()
    {
        $repoPage = new Page();
        $repoPage->AutoGenMetaPage('notfound','404 - Not Found', $this->lang_code);
        $repoPage->generateStylePage('notfound');
        /**
         * Send Error Email
         */
        $message = "";
        if (!isset($_GET['_url']) || strpos($_GET['_url'], '/notfound') === FALSE) {
            $sent_error = new SendError();
            $sent_error->sendErrorNotfound($message);
            $this->response->redirect($this->location_code.'/'.$this->lang_code.'/notfound');
        }
    }

    public function notfoundAction()
    {
        $this->my->sendErrorEmailAndRedirectToNotFoundPage($this->lang_code, $this->location_code);
    }
}