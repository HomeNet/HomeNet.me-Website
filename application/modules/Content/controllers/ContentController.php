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
 * @package Content
 * @subpackage Content
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class Content_ContentController extends Zend_Controller_Action
{

    public function init()
    {
        $this->view->controllerTitle = 'Content'; //for generic templates
        $this->view->id = $this->_getParam('id');
    }

    public function indexAction()
    {
        $service = new Content_Model_Content_Service();
        $this->view->assign('objects', $service->getObjectsBySection($this->_getParam('id')));
    }

    public function newAction()
    {
        $this->_helper->viewRenderer->setNoController(true); //use generic templates
        $manager = new Content_Model_Section_Manager();
        $form = $manager->getForm($this->_getParam('id'));
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

        $service = new Content_Model_Content_Service();
        $values['section'] = $this->view->id;
        $service->create($values);
        
        return $this->_redirect($this->view->url(array('controller'=>'content', 'action'=>'index', 'id'=>$this->view->id),'content-id').'?message=Successfully added');//
    }

    public function editAction()
    {
        $this->_helper->viewRenderer->setNoController(true); //use generic templates
        
        $service = new Content_Model_Content_Service();
        $object = $service->getObjectById($this->_getParam('id'));
        $form = $object->getForm();
        $form->addElement('submit', 'submit', array('label' => 'Update'));
        $form->addElement('hidden', 'section', array('value' =>$object->section));
        if (!$this->getRequest()->isPost()) {
            //load exsiting values
            
            
          //  $values = $object->toArray();

           // $form->populate($values);

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

        return $this->_redirect($this->view->url(array('controller'=>'content', 'action'=>'index', 'id'=>$values['section']),'content-id').'?message=Updated');//
    }

    public function deleteAction()
    {
        $this->_helper->viewRenderer->setNoController(true); //use generic templates
        
        $service = new Content_Model_Content_Service();
        $object = $service->getObjectById($this->_getParam('id'));
        $form = new Core_Form_Confirm();

        if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {

            $form->addDisplayGroup($form->getElements(), 'node', array ('legend' => 'Are you sure you want to delete "'.$object->title.'"?'));

            $this->view->form = $form;
            return;
        }
        $section = $object->section;

        $values = $form->getValues();

        //need to figure out why this isn't in values
        if(!empty($_POST['delete'])){
            
            $service->delete($object);
            return $this->_redirect($this->view->url(array('controller'=>'content', 'action'=>'index', 'id'=>$section),'content-id').'?message=Deleted');
        }
        return $this->_redirect($this->view->url(array('controller'=>'content', 'action'=>'index', 'id'=>$section),'content-id').'?message=Canceled');
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