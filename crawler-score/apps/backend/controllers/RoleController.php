<?php

namespace Score\Backend\Controllers;

use Score\Models\ForexcecRole;
use Score\Models\ForexcecUser;
use Score\Utils\Validator;
use Score\Repositories\Role;
use Score\Repositories\Activity;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;

class RoleController extends ControllerBase
{

    public function indexAction()
    {
        // Statement select list Role by $sql
        $data = $this->getParameter();
        //Pagination
        $current_page = $this->request->get('page');
        $validator = new Validator();
        if ($validator->validInt($current_page) == false || $current_page < 1)
            $current_page = 1;

        $list_role = $this->modelsManager->executeQuery($data['sql'], $data['para']);
        $paginator = new PaginatorModel(
            [
                'data' => $list_role,
                'limit' => 20,
                'page' => $current_page,
            ]
        );
        $msg_info = array();
        if ($this->session->has('msg_info')) {
            $msg_info = $this->session->get('msg_info');
            $this->session->remove('msg_info');
        }
        $msg_result = array();
        if ($this->session->has('msg_del')) {
            $msg_result = $this->session->get('msg_del');
            $this->session->remove('msg_del');;
        }
        $this->view->setVars(array(
            'list_role' => $paginator->getPaginate(),
            'msg_info' => $msg_info,
            'msg_del' => $msg_result
        ));
    }

    public function createAction()
    {

        $input_data = array('id' => 0, 'active' => 'Y', 'order' => '1', 'actions' => array());
        $msg_info = array();
        if ($this->request->isPost()) {
            $name = $this->request->getPost('txtName', array('string', 'trim'));
            $order = $this->request->getPost('txtOrder', array('string', 'trim'));
            $active = $this->request->getPost('rdActive');
            $actions = $this->getActions();
            $input_data = array(
                'name' => $name,
                'order' => $order,
                'active' => $active,
                'actions' => $actions
            );
            $validator = new Validator();
            if ($name == '') {
                $msg_info['name'] = 'Name field is required.';
            } else {
                $name_exist = Role::getByName($name, 0);
                if ($name_exist || in_array($name, ForexcecRole::getGuestUser())) $msg_info['name'] = 'Name "' . $name . '" is exists.';
            }
            if ($order == '') {
                $msg_info['order'] = 'Order field is required.';
            } else if (!$validator->validInt($order)) {
                $msg_info['order'] = 'Enter a valid order.';
            }
            if (count($msg_info) == 0) {
                $new_role = new ForexcecRole();
                $new_role->setRoleName($name);
                $new_role->setRoleOrder($order);
                $new_role->setRoleActive($active);
                $new_role->setRoleFunction(serialize($actions));
                /*====== Insert Data to Role =======*/
                $result = $new_role->save();
                // result danger
                $msg_info = array('status' => 'error', 'message' => 'Create a role fail.');
                // store activity error
                $message = 'Can\'t create a role';
                $data_log = json_encode(array());
                if ($result) {
                    // result success
                    $msg_info = array('status' => 'success', 'message' => 'Create a role with ID = ' . $new_role->getRoleId() . ' success.');
                    // store activity success
                    $message = '';
                    $old_data = array();
                    $new_data = $input_data;
                    $data_log = json_encode(array('content_role' => array($new_role->getRoleId() => array($old_data, $new_data))));
                }
                $activity = new Activity();
                $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log, $new_role->getRoleId());

                $this->session->set('msg_info', $msg_info);
                $this->response->redirect('/list-role');
                return;
            }
        }
        $role = new Role();
        $input_data["str_action"] = $role->getFunctionRole(4, $this->getArrDirectory(), $input_data['actions'], 'backend');
        $arr_role = array();
        $arr_role['input_data'] = $input_data;
        $arr_role['msg_info'] = $msg_info;
        $this->view->arr_role = $arr_role;
    }

    public function editAction()
    {
        $id = $this->request->get('id');
        $validator = new Validator();
        if (!$validator->validInt($id)) {
            $this->view->disable();
            $this->response->redirect('notfound');
            return;
        }
        $arr_role = array();
        $msg_info = array();
        $arr_role['arr_dir'] = $this->getArrDirectory();
        $role_edit = Role::getByID($id);
        if ($role_edit) {
            $model_data = array(
                'id' => $id,
                'name' => $role_edit->getRoleName(),
                'order' => $role_edit->getRoleOrder(),
                'active' => $role_edit->getRoleActive(),
                'actions' => unserialize($role_edit->getRoleFunction())
            );
            $input_data = $model_data;
            if ($this->request->isPost()) {
                $name = $this->request->getPost('txtName', array('string', 'trim'));
                $order = $this->request->getPost('txtOrder', array('string', 'trim'));
                $active = $this->request->getPost('rdActive');
                $actions = $this->getActions();

                $input_data = array(
                    'id' => $id,
                    'name' => $name,
                    'order' => $order,
                    'active' => $active,
                    'actions' => $actions
                );
                if ($name == '') {
                    $msg_info['msg_name'] = 'Name field is required.';
                } else {
                    $name_exist = Role::getByName($name, $id);
                    if ($name_exist) $msg_info['msg_name'] = 'Name "' . $name . '" is exists.';
                }
                if ($order == '') {
                    $msg_info['msg_order'] = 'Order field is required.';
                } else if (!$validator->validInt($order)) {
                    $msg_info['msg_order'] = 'Enter a valid order.';
                }
                if (count($msg_info) == 0) {
                    $role_edit->setRoleName($name);
                    $role_edit->setRoleOrder($order);
                    $role_edit->setRoleActive($active);
                    $role_edit->setRoleFunction(serialize($actions));
                    /*====== Edit Data ID = $id to Role =======*/
                    $result = $role_edit->save();
                    // result danger
                    $msg_info = array('status' => 'danger', 'message' => 'Edit a role with ID = ' . $id . ' fail.');
                    // store activity error
                    $message = 'Can\'t edit a role';
                    $data_log = json_encode(array());
                    if ($result) {
                        // result success
                        $msg_info = array('status' => 'success', 'message' => 'Edit a role with ID = ' . $id . ' success.');
                        if ($name == $this->auth['role']) {
                            $msg_info['session_destroy'] = true;
                        }
                        // store activity success
                        $message = '';
                        $old_data = $model_data;
                        $new_data = $input_data;
                        $data_log = json_encode(array('content_role' => array($id => array($old_data, $new_data))));
                    }
                    $activity = new Activity();
                    $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log, $id);
                    $this->session->set('msg_info', $msg_info);
                    $this->response->redirect('/list-role');
                }
            }
            $role = new Role();
            $input_data["str_action"] = $role->getFunctionRole(4, $this->getArrDirectory(), $input_data['actions'], 'backend');
            $arr_role = array();
            $arr_role['input_data'] = $input_data;
            $arr_role['msg_info'] = $msg_info;
            $this->view->arr_role = $arr_role;
        } else {
            $this->response->redirect('notfound');
            return;
        }
    }

    public function deleteAction()
    {
        $role_checks = $this->request->getPost("item");
        if ($role_checks) {
            $messages = array('error' => '',
                'success' => '');
            $data_log = array();
            $count = 0;
            foreach ($role_checks as $role_id) {
                $role_item = ForexcecRole::getFirstById($role_id);
                if ($role_item) {
                    $user = ForexcecUser::findFirstByRole($role_id);
                    if ($user) {
                        $message = 'Can\'t delete the Role Name = ' . $role_item->getRoleName() . '. Because It\'s exist in User.<br>';
                        $messages['error'] .= $message;
                    } else {
                        $old_data = array(
                            'name' => $role_item->getRoleName(),
                            'order' => $role_item->getRoleOrder(),
                            'active' => $role_item->getRoleActive(),
                            'actions' => unserialize($role_item->getRoleFunction())
                        );
                        $new_data = array();
                        $dat_log[$role_id] = array($old_data, $new_data);
                        $role_item->delete();
                        $count++;
                    }
                }
            }
            if ($count > 0) {
                $messages['success'] = 'Delete ' . $count . ' role successfully.';
                $message = '';
                $data_log = json_encode(array('content_role' => $data_log));
                $activity = new Activity();
                $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log);
            }
            $this->session->set('msg_del', $messages);
            $this->response->redirect('/list-role');
            return;
        }

    }

    private function getParameter()
    {
        $sql = "SELECT * FROM Score\Models\ForexcecRole WHERE 1";
        $keyword = trim($this->request->get("txtSearch"));
        $arrParameter = array();
        $validator = new Validator();
        if (!empty($keyword)) {
            if ($validator->validInt($keyword)) {
                $number = $this->my->getIdFromFormatID($keyword);
                $sql .= " AND (role_id = :number:)";
                $arrParameter['number'] = $number;
            } else {
                $sql .= " AND (role_name like CONCAT('%',:keyword:,'%') )";
                $arrParameter['keyword'] = $keyword;
            }
            $this->dispatcher->setParam("txtSearch", $keyword);
        }
        $data['para'] = $arrParameter;
        $data['sql'] = $sql;
        return $data;
    }

    // get Array Directory
    private function getArrDirectory()
    {
        $arr_dir = array();
        $directory_backend = __DIR__ . "/../../backend/controllers/*.php";
        foreach (glob($directory_backend) as $controller) {
            $className = 'Score\Backend\Controllers\\' . basename($controller, '.php');
            $className2 = basename($controller, 'Controller.php');
            if (!strpos($className2, '.php')) {
                $parent_name = lcfirst($className2);
                $key = 'backend' . $parent_name;
                if (empty($arr_dir[$key])) $arr_dir[$key] = array();
                $methods = (new \ReflectionClass($className))->getMethods(\ReflectionMethod::IS_PUBLIC);
                foreach ($methods as $method) {
                    if (\Phalcon\Text::endsWith($method->name, 'Action')) {
                        $action = basename($method->name, 'Action');
                        $arr_dir[$key][] = $action;
                    }
                }
            }
        }
        return $arr_dir;
    }

    // get Actions
    private function getActions()
    {
        $resources = $this->getArrDirectory();
        $result = ForexcecRole::getActions();
        foreach ($resources as $key => $values) {
            if (empty($result[$key])) $result[$key] = array();
            if (!empty($_POST[$key])) {
                for ($i = 0; $i < count($_POST[$key]); $i++) {
                    $result[$key][] = $_POST[$key][$i];
                }
            }
            if (count($result[$key]) == 0) {
                $result[$key][] = "temp";
            }
        }
        return $result;
    }
}