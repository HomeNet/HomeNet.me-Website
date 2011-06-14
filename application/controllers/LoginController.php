<?php

/*
 * Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 *
 * This file is part of HomeNet.
 *
 * HomeNet is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * HomeNet is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with HomeNet.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * @package Core
 * @subpackage Login
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class LoginController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
    }

    public function indexAction() {
        $form = new Core_Form_Login();
        $form->setAction('/login/');

        if (!$this->getRequest()->isPost()) {
            $this->view->form = $form;
            return;
        }

        if (!$form->isValid($_POST)) {
            // Failed validation; redisplay form
            $this->view->form = $form;
            return;
        }

        // now try and authenticate....
        $values = $form->getValues();
        $auth = new Core_Model_AuthInternal();

        try {
            $auth->login($values['username'], $values['password']);
        } catch (CMS_Exception $e) {

            if($e->getCode() == Core_Model_User::ERROR_NOT_ACTIVATED){
                $this->_setParam('user',$_SESSION['User']['id']);
                $this->_forward('next-steps', 'User');
            }
            

            $this->view->error = $e->getMessage();
            $this->view->form = $form;
            return;
        }

        $request = $this->getRequest();
        if ($request->getParam('forward')) {
            $this->_redirect($_SERVER['REQUEST_URI']);
        }


        $this->_redirect('/');


        // $this->setRequest($request);
        //$this->run($request);
        //  unset($_POST);
        // $this->_redirector = $this->_helper->getHelper('Redirector');
        //$this->_forward($request->getActionName(), $request->getControllerName(), $request->getModuleName(), $request->getParams());
    }

    public function facebookAction() {
        // action body
    }

    public function twitterAction() {
        // action body
    }

    public function googleAction() {
        // action body
    }

    public function yahooAction() {
        // action body
    }

    public function openIDAction() {
        // action body
    }

    public function aimAction() {
        // action body
    }

    public function successAction() {
        // action body
    }

    public function loginWidgetAction() {
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $user = new Zend_Session_Namespace('User');
            $this->view->name = $user->name;
            $this->view->identity = $auth->getIdentity();
        }
    }

    public function logoutAction() {
        $user = new Core_Model_AuthInternal();
        $user->logout();
    }

}

