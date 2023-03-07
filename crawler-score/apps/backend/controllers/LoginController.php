<?php

namespace Score\Backend\Controllers;

use Score\Models\TnRole;
use Score\Models\TnUser;
use Score\Repositories\Role;
use Score\Repositories\User;
use Score\Utils\PasswordGenerator;
use Score\Utils\Validator;
use Phalcon\Mvc\View;
class LoginController extends ControllerBase
{
    public function indexAction()
    {
        if ($this->session->has('auth')) {
            $this->response->redirect('/');
            return;
        }
        if ($this->session->has('msg_login')) {
            $this->view->msg_login = $this->session->get('msg_login');
            $this->session->remove('msg_login');
        }
        if ($this->request->isPost()) {

            $validate = new Validator();
            $libpassgenerator = new PasswordGenerator();
            $email = trim($this->request->getPost('email'));
            $password = trim($this->request->getPost('password'));
            $this->view->email = $email;
            $this->view->password = $password;

            $validLogin = true;
            if (strlen($email) < 1) {
                $this->view->msgErrorEmail = "This field cannot be empty.";
                $validLogin = false;
            } else if (strlen($email) > 255 || !$validate->validEmail($email)) {
                $this->view->msgErrorEmail = "Enter a valid email";
                $validLogin = false;
            } else {
                $this->view->msgErrorEmail = "";
            }
            if (strlen($password) < 1 || strlen($password) > 255) {
                $this->view->msgErrorPass = "This field cannot be empty.";
                $validLogin = false;
            } else {
                $this->view->msgErrorPass = "";
            }

            if ($validLogin) {
                $user = User::findFirstByEmail($email);
                if ($user) {
                    $role = Role::getFirstLoginById($user->getUserRoleId());
                    $controllerClass = $this->dispatcher->getControllerClass();
                    if (($role) || (strpos($controllerClass, 'Frontend') !== false)) {
                        if ($this->security->checkHash($password, $user->getUserPassword())) {
                            $user_repo = new User();
                            $user_repo->initSession($user, $role);
                            $user_repo->redirectLogged("/");
                            return;
                        } else {
                            $this->view->msgErrorLogin = "Email or password not correct";
                        }
                    } else {
                        $this->view->msgErrorLogin = "User not granted permissions";
                    }
                } else {
                    $this->view->msgErrorLogin = "Email or password not correct";
                }
            }
        }
        $this->view->disableLevel(array(
            View::LEVEL_LAYOUT => false,
            View::LEVEL_MAIN_LAYOUT => false
        ));
        $this->tag->setTitle('Login');
        $this->view->pick('login/index');
    }

    public function logoutAction()
    {
        $this->session->destroy();
        $this->response->redirect('login');
        return;
    }
}