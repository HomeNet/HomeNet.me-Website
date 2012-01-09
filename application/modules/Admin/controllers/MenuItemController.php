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
 * @subpackage MenuItem
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class Admin_MenuItemController extends Zend_Controller_Action
{

    public function init()
    {
        $this->view->controllerTitle = 'Menu Item'; //for generic templates
        $this->view->id = $this->_getParam('id');
    }

    public function indexAction()
    {
        $service = new Core_Model_Menu_Item_Service();
        $this->view->objects = $service->getObjectsByMenu($this->view->id);
    }

    public function newAction()
    {
        $this->_helper->viewRenderer->setNoController(true); //use generic templates
        
        $form = new Admin_Form_MenuItem();
        $form->addElement('submit', 'submit', array('label' => 'Create'));
        $this->view->assign('form',$form);
        
        
        
        //$this->_helper->viewRenderer('../generic/new');

        if (!$this->getRequest()->isPost()) {
            //first
            $this->view->form = $form;
            return;
        }

        if (!$form->isValid($_POST)) {
            //die('not valid');
            $this->view->form = $form;
            return;
        }
        
        //save
        //$nodeService = new HomeNet_Model_NodesService();
        
        $values = $form->getValues(true);
      //  die(debugArray($values));
        $values['menu'] = $this->view->id;

        $csService = new Core_Model_Menu_Item_Service();
        $csService->create($values);
        
        return $this->_redirect($this->view->url(array('controller'=>'menu-item', 'action'=>'index', 'id'=>$this->view->id),'admin-id').'?message=Successfully added new Set');//
    }

    public function editAction()
    {
        $this->_helper->viewRenderer->setNoController(true); //use generic templates
        
        $csService = new Core_Model_Menu_Item_Service();
        $form = new Admin_Form_MenuItem();
        $form->addElement('hidden', 'menu');
        $form->addElement('submit', 'submit', array('label' => 'Update'));
        
        if (!$this->getRequest()->isPost()) {
            //load exsiting values
            $object = $csService->getObjectById($this->_getParam('id'));
            
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
         $object = $csService->getObjectById($this->_getParam('id'));
         $object->fromArray($values);
        $csService->update($object);

        return $this->_redirect($this->view->url(array('controller'=>'menu-item', 'action'=>'index', 'id'=>$object->menu),'admin-id').'?message=Updated');//
    }

    public function deleteAction()
    {
        $this->_helper->viewRenderer->setNoController(true); //use generic templates
        
        $service = new Core_Model_Menu_Item_Service();
        $object = $service->getObjectById($this->view->id);
        $form = new Core_Form_Confirm();

        if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {

            $form->addDisplayGroup($form->getElements(), 'node', array ('legend' => 'Are you sure you want to delete "'.$object->title.'"?'));

            $this->view->form = $form;
            return;
        }

        $values = $form->getValues();
        $id = $object->menu;
        //need to figure out why this isn't in values
        if(!empty($_POST['confirm'])){
            
            $service->delete($object);
            return $this->_redirect($this->view->url(array('controller'=>'menu-item', 'action'=>'index', 'id'=>$id),'admin-id').'?message=Deleted');
        }
        return $this->_redirect($this->view->url(array('controller'=>'menu-item', 'action'=>'index', 'id'=>$id),'admin-id').'?message=Canceled');
    }
    
    public function typeFormAjaxAction(){
        $this->_helper->viewRenderer->setNoRender(true);
        
        $type = $this->_getParam('type');
        
        $types = array(
            Core_Model_Menu_Item::ROUTE   => 'Route',
            Core_Model_Menu_Item::DIVIDER => 'Divider',
            Core_Model_Menu_Item::TEXT => 'Text',
            Core_Model_Menu_Item::URL => 'Url'
        );
        
        if(!array_key_exists($type, $types)){
            throw new Exception('Type: '.$type.' Not Found');
        }
        
        $class = 'Admin_Form_MenuItem'.$types[$type];
        
        $form = new $class();
        
        echo $form->render();
        
        //echo 'test';
        
      // return 'test2';
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