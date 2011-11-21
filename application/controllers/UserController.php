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
 * @subpackage User
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class UserController extends Zend_Controller_Action
{

    public function init()
    {
        $this->view->user = $this->_getParam('user');
    }

    public function indexAction()
    {

    }

    public function newAction()
    {
        $form   = new Core_Form_User();
        //$form->setAction('/user/new');
        if (!$this->getRequest()->isPost()) {
            $this->view->form = $form;
            return;
        }

        if (!$form->isValid($_POST)) {
            // Failed validation; redisplay form
            $this->view->form = $form;
            return;
        }

        $values = $form->getValues();
        
        $uManager = new Core_Model_User_Manager();
        $user = $uManager->register($values);
        //die('user added');

        //decide where to redirect based on whether it is new registration or a admin created one
        $this->_redirect($this->view->url(array('user'=>$user->id,'action'=>'next-steps'),'core-user'));
    }
    
     public function profileAction()
    {
        $form   = new Core_Form_User();
        //$form->setAction('/user/new');
        if (!$this->getRequest()->isPost()) {
            $this->view->form = $form;
            return;
        }

        if (!$form->isValid($_POST)) {
            // Failed validation; redisplay form
            $this->view->form = $form;
            return;
        }

        $values = $form->getValues();
        $uService = new Core_Model_Auth_Internal_Service();
        $uService->add($values);
        //die('user added');

        //decide where to redirect based on whether it is new registration or a admin created one
        $this->_redirect($this->view->url(array('user'=>$user->id,'action'=>'next-steps'),'core-user'));
    }

    public function nextStepsAction(){
        //displays static page
    }

    public function sendActivationAction(){
        
        $uService = new Core_Model_User_Service();
        $user = $uService->getObjectById($this->view->user);
        
        $uManager = new Core_Model_User_Manager($user);
        $uManager->sendActivationEmail();
        
        $this->_forward('next-steps');
    }

    public function activateAction(){
        $uService = new Core_Model_User_Service();
        $user = $uService->getObjectById($this->view->user);
        
        $uManager = new Core_Model_User_Manager($user);

        try{
            $uManager->activate($this->_getParam('key'));
        } catch(CMS_Exception $e){
            return;
        }

        $this->_redirect($this->view->url(array('user'=>$this->view->user,'action'=>'activated'),'core-user'));
    }

    public function activatedAction(){
        //$this->_forward('index','login');
    }

    public function editAction()
    {
        $form = new Core_Form_User();
        $form->setAction('/user/edit');
        $form->removeElement('password');
        //$id = $this->_request->getParam('id');

        //First Displayed
        if (!$this->getRequest()->isPost()) {
            $userModel = new Model_User();
            $user = $userModel->get($id);
            $form->populate($user);
            $this->view->form = $form;
            return;
        }

        if (!$form->isValid($_POST)) {
            // Failed validation; redisplay form
            $this->view->form = $form;
            return;
        }


        
    }
    /*
    public function deleteAction()
    {
        $id = $this->_request->getParam('id');
        $userModel = new Model_User();
        $userModel->deleteUser($id);
        return $this->_forward('list');}
    }*/

    public function settingsAction()
    {

    }
}





