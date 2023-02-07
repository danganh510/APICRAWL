<?php

namespace Score\Backend\Controllers;

use Score\Models\ForexcecUser;
use Score\Repositories\Activity;
use Score\Repositories\Role;
use Score\Repositories\EmailTemplate;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;
use Score\Utils\Validator;
use Score\Utils\PasswordGenerator;
class UserController extends ControllerBase
{
    public function indexAction()
    {
        $data = $this->getParameter();
        $list_user = $this->modelsManager->executeQuery($data['sql'],$data['para']);
        $current_page = $this->request->get('page');
        $validator = new Validator();
        if($validator->validInt($current_page) == false || $current_page < 1)
            $current_page=1;
        $paginator = new PaginatorModel(
            array(
                'data'  => $list_user,
                'limit' => 20,
                'page'  => $current_page,
            )
        );
        $msg_result = array();
        if ($this->session->has('msg_result')) {
            $msg_result = $this->session->get('msg_result');
            $this->session->remove('msg_result');
        }
        $msg_delete = array();
        if ($this->session->has('msg_delete')) {
            $msg_delete = $this->session->get('msg_delete');
            $this->session->remove('msg_delete');
        }
        $this->view->setVars(array(
            'user' => $paginator->getPaginate(),
            'msg_result'  => $msg_result,
            'msg_delete'  => $msg_delete,
        ));
    }
    public function createAction()
    {
        $data = array('id' => -1,'active' => 'Y', 'role_id' => -1, 'password' => PasswordGenerator::salt(8));
        $messages = array();
        if($this->request->isPost()) {
            $messages = array();
            $data = array(
                'id' => -1,
                'name' => $this->request->getPost("txtName", array('string', 'trim')),
                'email' => $this->request->getPost("txtEmail", array('string', 'trim')),
                'password' => $this->request->getPost("txtPassword", array('string', 'trim')),
                'role_id' => $this->request->getPost("slcRole"),
                'active' => $this->request->getPost("radActive"),
            );
            if (empty($data['name'])) {
                $messages['name'] = "Name field is required.";
            }
            if (empty($data['email'])) {
                $messages['email'] = "Email field is required.";
            }else if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $messages['email'] = 'The email must be a valid email address.';
            } else if (ForexcecUser::findFirstByEmail($data['email'])) {
                $messages['email'] = 'That email is taken.';
            }
            if (empty($data['password'])) {
                $messages['password'] = "Password field is required.";
            }
            if (($data['role_id']) == -1) {
                $messages['role_id'] = "Role field is required.";
            }

            if (count($messages) == 0) {
                $passGenerator = new PasswordGenerator();
                $password = $this->security->hash($data['password']);
                $new_user = new ForexcecUser();
                $new_user->setUserName($data['name']);
                $new_user->setUserEmail($data['email']);
                $new_user->setUserPassword($password);
                $new_user->setUserRoleId($data['role_id']);
                $new_user->setUserActive($data['active']);
                $new_user->setUserInsertTime($this->globalVariable->curTime);
                $result = $new_user->save();

                $message =  "We can't store your info now: "."<br/>";
                if ($result === true){
                    $message = 'Create the user ID: '.$new_user->getUserId().' success';
                    $msg_result = array('status' => 'success', 'msg' => $message);
                    $old_data = array();
                    $new_data = $data;
                    $data_log = json_encode(array('content_article' => array($new_user->getUserId() => array($old_data, $new_data))));
                    $activity = new Activity();
                    $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log);
                    $this->sendNewUserEmail($new_user,$data['password']);
                }else{
                    foreach ($new_user->getMessages() as $msg) {
                        $message .= $msg."<br/>";
                    }
                    $msg_result = array('status' => 'error', 'msg' => $message);
                }
                $this->session->set('msg_result',$msg_result );
                $this->response->redirect("/list-user");
                return;
            }
        }
        $strRole = Role::getComboBox($data['role_id']);
        $messages["status"] = "border-red";
        $this->view->setVars(array(
            'oldinput' => $data,
            'slcRole' => $strRole,
            'messages' => $messages
        ));
    }
    public function viewAction()
    {
        $id = $this->request->get('id');
        $checkID = new Validator();
        if(!$checkID->validInt($id))
        {
            $this->response->redirect('notfound');
            return ;
        }
        $user_model = ForexcecUser::findFirstById($id);
        if(empty($user_model))
        {
            $this->response->redirect('notfound');
            return;
        }
        if ($this->session->has('msg_information')) {
            $msg_information = $this->session->get('msg_information');
            $this->session->remove('msg_information');
            $this->view->msg_information = $msg_information;
        }
        if ($this->session->has('msg_password')) {
            $msg_password = $this->session->get('msg_password');
            $this->session->remove('msg_password');
            $this->view->msg_password = $msg_password;
        }
        if ($this->session->has('msg_role')) {
            $msg_role = $this->session->get('msg_role');
            $this->session->remove('msg_role');
            $this->view->msg_role = $msg_role;
        }
        $data = array(
            'user_id' => $user_model->getUserId(),
            'user_name' => $user_model->getUserName(),
            'user_email' => $user_model->getUserEmail(),
            'user_role_id' => $user_model->getUserRoleId(),
            'user_active' => $user_model->getUserActive(),
            'user_insert_time' => $user_model->getUserInsertTime(),
        );

        $strRole = Role::getComboBox($data['user_role_id']);
        $this->view->setVars(array(
            'data' => $data,
            'slcRole' => $strRole,
        ));
    }
    public function informationAction()
    {
        $id = $this->request->get('id');
        $checkID = new Validator();
        if(!$checkID->validInt($id))
        {
            $this->response->redirect('notfound');
            return ;
        }
        $user_model = ForexcecUser::findFirstById($id);
        if($user_model === null)
        {
            $this->response->redirect('notfound');
            return;
        }
        $data = array(
            'user_id' => $user_model->getUserId(),
            'user_name' => $user_model->getUserName(),
            'user_email' => $user_model->getUserEmail(),
            'user_role_id' => $user_model->getUserRoleId(),
            'user_active' => $user_model->getUserActive(),
        );
        $old_data = $data;
        if($this->request->isPost()) {
            $input_data = array(
                'user_id' => $id,
                'user_name' => $this->request->getPost('txtName', array('string', 'trim')),
                'user_email' => $this->request->getPost('txtEmail', array('string', 'trim')),
                'user_active' => $this->request->getPost('radActive'),
            );
            $data = $input_data;
            $messages = array();
            if(empty($data['user_name'])) {
                $messages['name'] = 'Name field is required.';
            }
            if(count($messages) == 0)
            {
                $user_model->setUserName($data['user_name']);
                $result = $user_model->update();
                if ($result === false) {
                    $message = "Edit User fail !";
                    $msg_result['status'] = 'error';
                    $msg_result['msg'] = $message;
                } else {
                    $msg_result = array('status' => 'success', 'msg' => 'Edit User Success');
                    $message = '';
                    $data_log = json_encode(array('content_user' => array($user_model->getUserId() => array($old_data, $data))));
                    $activity = new Activity();
                    $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log);
                }
                $this->session->set('msg_information', $msg_result);
            }
        }
        return $this->response->redirect("/view-user?id=".$id);

    }
    public function passwordAction()
    {
        $id = $this->request->get('id');
        $checkID = new Validator();
        if(!$checkID->validInt($id))
        {
            $this->response->redirect('notfound');
            return ;
        }
        $user_model = ForexcecUser::findFirstById($id);
        if(empty($user_model))
        {
            $this->response->redirect('notfound');
            return;
        }
        $data = array(
            'user_id' => $user_model->getUserId(),
            'user_password' => $user_model->getUserPassword(),
        );
        $old_data = $data;
        if($this->request->isPost()) {
            $input_data = array(
                'user_id' => $id,
                'user_password' => $this->request->getPost('txtPassword', array('string', 'trim')),
            );
            $data = $input_data;
            $messages = array();
            if(empty($data['user_password'])) {
                $messages['password'] = 'New Password field is required.';
            }
            if(count($messages) == 0){
                $user_model->setUserPassword($this->security->hash($data['user_password']));
                $result = $user_model->update();
                if ($result === false) {
                    $message = "Change Password fail !";
                    $msg_result['status'] = 'error';
                    $msg_result['msg'] = $message;
                } else {
                    $msg_result = array('status' => 'success', 'msg' => 'Change Password Success');
                    $message = '';
                    $data_log = json_encode(array('content_user' => array($user_model->getUserId() => array($old_data, $data))));
                    $activity = new Activity();
                    $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log);
                    $this->sendRecoverEmail($user_model,$data['user_password']);
                }
                $this->session->set('msg_password', $msg_result);
            }
        }
        return $this->response->redirect("/view-user?id=".$id);
    }

    //send recover email
    private function sendRecoverEmail($user,$pass)
    {
        $fromEmail = "noreply@forexcec.com";
        $toEmail = $user->getUserEmail();
        $email_template = new EmailTemplate();
        $message_forgot_password = $email_template->getEmailResetPass($user,$pass,'en');
        $fromFullName = "ForexCEC";
        $toFullname = $user->getUserName();
        $replyToEmail = "noreply@forexcec.com";
        $replyToName = $fromFullName;
        if($message_forgot_password["success"]==1) {
            $result = $this->my->sendEmail($fromEmail, $toEmail, $message_forgot_password["subject"], $message_forgot_password["content"], $fromFullName, $toFullname, $replyToEmail, $replyToName);
            return $result;
        }
    }

    //send new user email
    private function sendNewUserEmail($user,$pass)
    {
        $fromEmail = "noreply@forexcec.com";
        $toEmail = $user->getUserEmail();
        $email_template = new EmailTemplate();
        $message_forgot_password = $email_template->getEmailNewUser($user,$pass,'en');
        $fromFullName = "ForexCEC";
        $toFullname = $user->getUserName();
        $replyToEmail = "noreply@forexcec.com";
        $replyToName = $fromFullName;
        if($message_forgot_password["success"]==1) {
            $result = $this->my->sendEmail($fromEmail, $toEmail, $message_forgot_password["subject"], $message_forgot_password["content"], $fromFullName, $toFullname, $replyToEmail, $replyToName);
            return $result;
        }
    }

    public function roleAction()
    {
        $id = $this->request->get('id');
        $checkID = new Validator();
        if(!$checkID->validInt($id))
        {
            $this->response->redirect('notfound');
            return ;
        }
        $user_model = ForexcecUser::findFirstById($id);
        if(empty($user_model))
        {
            $this->response->redirect('notfound');
            return;
        }
        $data = array(
            'user_id' => $user_model->getUserId(),
            'user_role_id' => $user_model->getUserRoleId(),
        );
        $old_data = $data;
        if($this->request->isPost()) {
            $input_data = array(
                'user_id' => $id,
                'user_role_id' => $this->request->getPost('slcRole'),
            );
            $data = $input_data;
            $messages = array();
            if($data['user_role_id']=='') {
                $messages['role'] = 'Role field is required.';
            }
            if(count($messages) == 0){
                $user_model->setUserRoleId($data['user_role_id']);
                $result = $user_model->update();
                if ($result === false) {
                    $message = "Update Role fail !";
                    $msg_result['status'] = 'error';
                    $msg_result['msg'] = $message;
                } else {
                    $msg_result = array('status' => 'success', 'msg' => 'Update Role Success');
                    $message = '';
                    $data_log = json_encode(array('content_user' => array($user_model->getUserId() => array($old_data, $data))));
                    $activity = new Activity();
                    $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log);
                }
                $this->session->set('msg_role', $msg_result);
            }
        }
        return $this->response->redirect("/view-user?id=".$id);
    }
    public function deleteAction ()
    {
        $list_user = $this->request->get('item');
        $Content_user = array();
        $msg_delete = array('error' => '', 'success' => '');
        if($list_user) {
            foreach ($list_user as $user_id) {
                $user_model = ForexcecUser::findFirstById($user_id);
                if($user_model) {
                    $table_names = array();
                    $message_temp = "Can't delete User Name = ".$user_model->getUserName().". Because It's exist in";
                    if(empty($table_names)) {
                        $old_user_data = $user_model->toArray();
                        $new_user_data = array();
                        $Content_user[$user_id] = array($old_user_data, $new_user_data);
                        $user_model->delete();
                    } else {
                        $msg_delete['error'] .= $message_temp.implode(",", $table_names)."<br>";
                    }
                }
            }
        }
        if (count($Content_user) > 0 ) {
            // delete success
            $message = 'Delete '. count($Content_user) .' user success.';
            $msg_delete['success'] = $message;
            // store activity success
            $data_log = json_encode(array('content_user' => $Content_user));
            $activity = new Activity();
            $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log);
        }
        $this->session->set('msg_delete', $msg_delete);
        $this->response->redirect('/list-user');
        return;
    }
    private function getParameter(){
        $sql = "SELECT *
                FROM Score\Models\ForexcecUser
                WHERE 1";
        $keyword = trim($this->request->get("txtSearch"));
        $from = trim($this->request->get("txtFrom")); //string
        $to = trim($this->request->get("txtTo"));  //string
        $arrParameter = array();
        $validator = new Validator();
        if(!empty($keyword)) {
            if($validator->validInt($keyword)) {
                $sql.= " AND (user_id = :number:)";
                $arrParameter['number'] = $this->my->getIdFromFormatID($keyword, true);;
            }
            else {
                $sql.=" AND (user_name like CONCAT('%',:keyword:,'%') OR user_email like CONCAT('%',:keyword:,'%'))";
                $arrParameter['keyword'] = $keyword;
            }
            $this->dispatcher->setParam("txtSearch", $keyword);
        }
        if($from){
            $intFrom = $this->my->UTCTime(strtotime($from)); //UTC_mysql_time = date_picker - time zone
            $sql .= " AND user_insert_time >= :from:";
            $arrParameter['from'] = $intFrom;
            $this->dispatcher->setParam("txtFrom", $from);
        }
        if($to){
            $intTo = $this->my->UTCTime(strtotime($to)); //UTC_mysql_time = date_picker - time zone
            $sql .= " AND user_insert_time <= :to:";
            $arrParameter['to'] = $intTo;
            $this->dispatcher->setParam("txtTo", $to);
        }
        $sql.=" ORDER BY user_insert_time DESC";
        $data['para'] = $arrParameter;
        $data['sql'] = $sql;
        return $data;
    }
}