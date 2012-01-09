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
 * @subpackage Menu
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class Admin_MenuController extends Zend_Controller_Action {

    public function init() {
        $this->view->controllerTitle = 'Menu'; //for generic templates
        $this->view->id = $this->_getParam('id');
    }

    public function indexAction() {
        $service = new Core_Model_Menu_Service();
        $this->view->objects = $service->getObjects();
    }

    public function newAction() {
        $this->_helper->viewRenderer->setNoController(true); //use generic templates

        $form = new Admin_Form_Menu();
        $form->addElement('submit', 'submit', array('label' => 'Create'));
        $this->view->assign('form', $form);

        if (!$this->getRequest()->isPost()) {
            //first
            $this->view->form = $form;
            return;
        }

        if (!$form->isValid($_POST)) {
            // Failed validation; redisplay form
            $this->view->form = $form;
            return;
        }

        //save
        //$nodeService = new HomeNet_Model_NodesService();

        $values = $form->getValues();
        $values['set'] = $this->_getParam('id');

        $service = new Core_Model_Menu_Service();
        $service->create($values);

        return $this->_redirect($this->view->url(array('controller' => 'menu', 'action' => 'index'), 'admin') . '?message=Successfully added new Set'); //
    }

    public function editAction() {
        $this->_helper->viewRenderer->setNoController(true); //use generic templates

        $service = new Core_Model_Menu_Service();
        $form = new Admin_Form_Menu();
        $form->addElement('submit', 'submit', array('label' => 'Update'));

        if (!$this->getRequest()->isPost()) {
            //load exsiting values
            $object = $service->getObjectById($this->_getParam('id'));

            $values = $object->toArray();

            $form->populate($values);

            $this->view->form = $form;
            return;
        }

        if (!$form->isValid($_POST)) {
            // Failed validation; redisplay form
            $this->view->form = $form;
            return;
        }

        //save
        $values = $form->getValues();
        $object = $service->getObjectById($this->_getParam('id'));
        $object->fromArray($values);
        $service->update($object);

        return $this->_redirect($this->view->url(array('controller' => 'menu', 'action' => 'index'), 'admin') . '?message=Updated'); //
    }

    public function deleteAction() {
        $this->_helper->viewRenderer->setNoController(true); //use generic templates

        $cService = new Core_Model_Menu_Service();
        $object = $cService->getObjectById($this->_getParam('id'));
        $form = new Core_Form_Confirm();

        if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {

            $form->addDisplayGroup($form->getElements(), 'node', array('legend' => 'Are you sure you want to delete "' . $object->title . '"?'));

            $this->view->form = $form;
            return;
        }

        //@todo check for sections using this set //block if any still do
        //@todo also delete amy categories in this set 

        $values = $form->getValues();
       // $id = $object->set;
        //need to figure out why this isn't in values
        if (!empty($_POST['confirm'])) {

            $cService->delete($object);
            return $this->_redirect($this->view->url(array('controller' => 'menu', 'action' => 'index'), 'admin') . '?message=Deleted');
        }
        return $this->_redirect($this->view->url(array('controller' => 'menu', 'action' => 'index'), 'admin') . '?message=Canceled');
    }

    public function hideAction() {
        //ajax toggle
    }

    public function showAction() {
        //ajax toggle
    }

}