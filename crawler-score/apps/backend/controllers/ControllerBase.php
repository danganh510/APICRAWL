<?php

namespace Score\Backend\Controllers;

use Score\Repositories\Role;

/**
 * @property \GlobalVariable globalVariable
 * @property \My my
 */
class ControllerBase extends \Phalcon\Mvc\Controller
{
	protected $auth;

	public function initialize()
    {
        //current user
        $this->auth = $this->session->get('auth');
        if (isset($this->auth['role'])) {
            $role_function  = array();
            if ($this->session->has('action')) {
                $role_function = $this->session->get('action');
            } else {
                $role = Role::getFirstByName($this->auth['role']);
                if($role) {
                    $role_function = unserialize($role->getRoleFunction());
                    $this->session->set('action', $role_function);
                }
            }
        }

        $this->view->setVars([
            'role_function' => isset($role_function) ? $role_function : []
        ]);

    }
}
