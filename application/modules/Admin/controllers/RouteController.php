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
 * @package Admin
 * @subpackage Route
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class Admin_RouteController extends Zend_Controller_Action
{

    public function init()
    {
        $this->view->controllerTitle = 'Routes'; //for generic templates
    }

    public function indexAction()
    {
        $service = new Core_Model_Route_Service();
        $this->view->objects = $service->getObjects();
    }

    public function newAction()
    {
        $this->_helper->viewRenderer->setNoController(true); //use generic templates
        
        $form = new Core_Form_Route();
        $form->addElement('submit', 'submit', array('label' => 'Create'));
        $this->view->assign('form',$form);

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
        $values = $form->getValues();

        $service = new Core_Model_Route_Service();
        $service->create($values);
        
        return $this->_redirect($this->view->url(array('controller'=>'route', 'action'=>'index'),'admin-route').'?message=Successfully added new Set');//
    }

    public function editAction()
    {
        $this->_helper->viewRenderer->setNoController(true); //use generic templates
        
        $service = new Core_Model_Route_Service();
        $form = new Core_Form_Route();
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

        return $this->_redirect($this->view->url(array('controller'=>'route'),'admin-route').'?message=Updated');//
    }

    public function deleteAction()
    {
        $this->_helper->viewRenderer->setNoController(true); //use generic templates
        
        $csService = new Core_Model_Route_Service();
        $object = $csService->getObjectById($this->_getParam('id'));
        $form = new Core_Form_Confirm();

        if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {

            $form->addDisplayGroup($form->getElements(), 'node', array ('legend' => 'Are you sure you want to delete "'.$object->name.'"?'));

            $this->view->form = $form;
            return;
        }

        $values = $form->getValues();

        //need to figure out why this isn't in values
        if(!empty($_POST['confirm'])){
            
            $csService->delete($object);
            return $this->_redirect($this->view->url(array('controller'=>'route'),'admin-route').'?message=Deleted');
        }
        return $this->_redirect($this->view->url(array('controller'=>'route'),'admin-route').'?message=Canceled');
    }

    public function hideAction()
    {
        // action body
    }

    public function showAction()
    {
        // action body
    }
}