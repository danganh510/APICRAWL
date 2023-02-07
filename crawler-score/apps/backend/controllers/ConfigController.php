<?php

namespace Score\Backend\Controllers;

use Score\Models\ForexcecConfig;
use Score\Models\ForexcecLanguage;
use Score\Repositories\Config;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;
use Score\Repositories\Activity;
use Score\Repositories\Language;

class ConfigController extends ControllerBase
{

    public function indexAction()
    {
        $current_page = $this->request->getQuery('page', 'int');
        $sql = "SELECT * FROM Score\Models\ForexcecConfig WHERE  1 ";
        $arrParameter = array();
        $keyword = trim($this->request->get("txtSearch"));
        $lang = $this->request->get("slcLang", array('string', 'trim'));
        $langCode = $this->globalVariable->defaultLanguage;
        if (!empty($lang)) {
            $langCode = $lang;
            $this->dispatcher->setParam("slcLang", $langCode);
        }
        if ($keyword) {
            $sql .= " AND  ((config_key like CONCAT('%',:keyword:,'%')) OR  (config_content like CONCAT('%',:keyword:,'%'))) ";
            $arrParameter["keyword"] = $keyword;
            $this->dispatcher->setParam("txtSearch", $keyword);
        }
        $sql .= " AND config_language = :CODE: ORDER BY config_key ASC";
        $arrParameter['CODE'] = $langCode;
        $list_config = $this->modelsManager->executeQuery($sql, $arrParameter);
        $paginator = new PaginatorModel(array(
            'data' => $list_config,
            'limit' => 20,
            'page' => $current_page,
        ));
        $this->view->select_lang = Language::getCombo($langCode);
        $this->view->list_config = $paginator->getPaginate();
        if ($this->session->has('msg_result')) {
            $msg_result = $this->session->get('msg_result');
            $this->session->remove('msg_result');
            $this->view->msg_result = $msg_result;
        }
        if ($this->session->has('msg_del')) {
            $msg_result = $this->session->get('msg_del');
            $this->session->remove('msg_del');
            $this->view->msg_del = $msg_result;
        }
    }

    public function createAction()
    {
        $lang_default = $this->globalVariable->defaultLanguage;
        $arr_language = Language::arrLanguages();
        $arr_translate = array();
        $config_key = '';
        $messages = array();
        if ($this->request->isPost()) {
            foreach ($arr_language as $code => $lang) {
                $arr_translate[$code] = trim($this->request->getPost('content_' . $code));
            }
            $config_key = $this->request->getPost('txtKey', array('string', 'trim'));
            if (empty($config_key)) {
                $messages['key'] = 'Key field is required.';
            } else {
                $pos = strpos($config_key, ' ');
                if ($pos !== false) {
                    $messages['key'] = 'Key have white space.';
                } else {
                    if (Config::checkKeyword($config_key)) {
                        $messages['key'] = 'Key is exists.';
                    }
                }
            }
            if (count($messages) == 0) {
                $url_delete_cache = defined('URL_DELETE_CACHE') ? URL_DELETE_CACHE : '';
                if($url_delete_cache) {
                    $result = Config::curl_get_contents($url_delete_cache);
                }
                $isCheck = true;
                $new_data = array();
                foreach ($arr_language as $code => $lang) {
                    $config_model = Config::findByLanguage($config_key, $code);
                    if (!$config_model) {
                        $config_model = new ForexcecConfig();
                        $config_model->setConfigKey($config_key);
                        $config_model->setConfigLanguage($code);
                    } else {
                        $old_data[] = $config_model->toArray();
                    }
                    $config_model->setConfigContent($arr_translate[$code]);
                    if (!$config_model->save()) $isCheck = false;
                    if ($isCheck) {
                        $item = array(
                            'config_key' => $config_key,
                            'config_language' => $code,
                            'config_content' => $arr_translate[$code],
                        );
                        $new_data[] = $item;
                    }
                }
                if ($isCheck) {
                    $msg_result = array('status' => 'success', 'msg' => 'Create Config Success !');
                    $old_data = array();
                    $message = '';
                    $data_log = json_encode(array('content_config' => array($config_key => array($old_data, $new_data))));
                    $activity = new Activity();
                    $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log);
                } else {
                    $msg_result = array('status' => 'success', 'msg' => 'Create Config Fail !');
                }
                $this->session->set('msg_result', $msg_result);
                return $this->response->redirect('/list-config');
            }
        }
        $messages['status'] = 'border-red';
        $formData = array(
            'arr_translate' => $arr_translate,
            'arr_language' => $arr_language,
            'lang_default' => $lang_default,
            'messages' => $messages,
            'config_key' => $config_key,
            'mode' => 'create',
        );
        $this->view->formData = $formData;
    }

    public function editAction()
    {
        $config_key = trim($this->request->get("id"));
        $lang_default = $this->globalVariable->defaultLanguage;
        $check_find = Config::findByLanguage($config_key, $lang_default);
        if (!$check_find) {
            return $this->response->redirect("/notfound");
        }
        if ($this->session->has('msg_result')) {
            $msg_result = $this->session->get('msg_result');
            $this->session->remove('msg_result');
            $this->view->msg_result = $msg_result;
        }
        $old_data = array();
        $new_data = array();
        $messages = array();
        $arr_language = Language::arrLanguages();
        $arr_translate = array();
        $list_config = Config::getByID($config_key);
        foreach ($list_config as $config) {
            $arr_translate[$config->getConfigLanguage()] = $config->getConfigContent();
            $item = array(
                'config_key' => $config->getConfigKey(),
                'config_language' => $config->getConfigLanguage(),
                'config_content' => $config->getConfigContent(),
            );
            $old_data[] = $item;
        }
        if ($this->request->isPost()) {
            $config_key = $this->request->getPost('txtKey', array('string', 'trim'));
            $arr_translate = array();
            foreach ($arr_language as $code => $lang) {
                $arr_translate[$code] = trim($this->request->get('content_' . $code));
            }
            if (empty($config_key)) {
                $messages['key'] = 'Keyword field is required.';
            }
            if (count($messages) == 0) {
                $url_delete_cache = defined('URL_DELETE_CACHE') ? URL_DELETE_CACHE : '';
                if($url_delete_cache) {
                    $result = Config::curl_get_contents($url_delete_cache);
                }
                $isCheck = true;
                foreach ($arr_language as $code => $lang) {
                    $config_model = Config::findByLanguage($config_key, $code);
                    if (!$config_model) {
                        $config_model = new ForexcecConfig();
                        $config_model->setConfigKey($config_key);
                        $config_model->setConfigLanguage($code);
                    } else {
                        $old_data[] = $config_model->toArray();
                    }
                    $config_model->setConfigContent($arr_translate[$code]);
                    if (!$config_model->save()) $isCheck = false;
                    if ($isCheck) {
                        $item = array(
                            'config_key' => $config_key,
                            'config_language' => $code,
                            'config_content' => $arr_translate[$code],
                        );
                        $new_data[] = $item;
                    }
                }
                if ($isCheck) {
                    $msg_result = array('status' => 'success', 'msg' => 'Update Config Success !');
                    $message = '';
                    $data_log = json_encode(array('content_config' => array($config_key => array($old_data, $new_data))));
                    $activity = new Activity();
                    $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log);
                } else {
                    $msg_result = array('status' => 'success', 'msg' => 'Update Config Fail !');
                }
                $this->session->set('msg_result', $msg_result);
                return $this->response->redirect('/edit-config?id=' . $config_key);
            }

        }
        $messages['status'] = 'border-red';
        $formData = array(
            'arr_translate' => $arr_translate,
            'arr_language' => $arr_language,
            'lang_default' => $lang_default,
            'messages' => $messages,
            'config_key' => $config_key,
            'mode' => 'edit',
        );
        $this->view->formData = $formData;
    }

    public function deleteAction()
    {
        $config_key = trim($this->request->get("id"));
        $check_find = Config::findByID($config_key);
        if (!$check_find) {
            return $this->response->redirect("/notfound");
        }
        $url_delete_cache = defined('URL_DELETE_CACHE') ? URL_DELETE_CACHE : '';
        if($url_delete_cache) {
            $result = Config::curl_get_contents($url_delete_cache);
        }
        $check_delete = Config::deletedByKey($config_key);
        $list_config = Config::getByID($config_key);
        $data = array();
        foreach ($list_config as $config) {
            $item = array(
                'config_key' => $config->getConfigKey(),
                'config_language' => $config->getConfigLanguage(),
                'config_content' => $config->getConfigContent(),
            );
            $data[] = $item;
            if (!$config->delete()) $check_delete = false;
        }
        if ($check_delete) {
            $msg_result = array('status' => 'success', 'msg' => 'Delete Config Key = ' . $config_key . '  Success !');
            $message = '';
            $data_log = json_encode(array('content_config' => array($config_key => array($data))));
            $activity = new Activity();
            $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log);
        } else {
            $msg_result = array('status' => 'success', 'msg' => 'Delete Config Key = ' . $config_key . '  Fail !');
        }
        $this->session->set('msg_result', $msg_result);
        return $this->response->redirect('/list-config');
    }
}