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
 * @subpackage Category
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class Content_CategoryController extends Zend_Controller_Action
{

    private $_id;
    
    public function init()
    {
        $this->view->heading = 'Category'; //for generic templates
        $this->view->id = $this->_id = $this->_getParam('id');
    }
    
     private function _setupCrumbs($set){
        $this->view->breadcrumbs()->addPage(array(
            'label'  => 'Admin',
            'route'  => 'admin',   
        ));
        
    
        $this->view->breadcrumbs()->addPage(array(
            'label'  => 'Content',
            'route'  => 'content-admin',  
            'module' => 'Content',
            'controller' => 'section'
        ));
        
        $this->view->breadcrumbs()->addPage(array(
            'label'  => 'Category Sets',
            'route'  => 'content-admin',  
            'module' => 'Content',
            'controller' => 'category-set'
        ));
        
        $service = new Content_Model_CategorySet_Service();
        $object = $service->getObjectById($set);
        
        $this->view->breadcrumbs()->addPage(array(
            'label'  => $object->title,
            'route'  => 'content-admin-id',  
            'module' => 'Content',
            'controller' => 'category',
            'params' => array('id' => $object->id)
        ));

        $this->view->heading = $object->title.' Category';
        
       //return $section;
    }

    public function indexAction()
    {
        
        if(empty($this->_id)){
            throw new InvalidArgumentException('Missing Set Id', 404);
        }
        
        $this->_setupCrumbs($this->_id);
        
        $service = new Content_Model_Category_Service();
        $this->view->objects = $service->getObjectsBySet($this->view->id);
    }

    public function newAction()
    {
        $this->_helper->viewRenderer->setNoController(true); //use generic templates
        $this->_setupCrumbs($this->_id);
        $form = new Content_Form_Category();
        $form->addElement('submit', 'submit', array('label' => 'Create'));

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
        $values['set'] = $this->_id;

        $service = new Content_Model_Category_Service();
        $object = $service->create($values);
        
        $this->view->messages()->add('Successfully added Category &quot;'.$object->title.'&quot;');
        return $this->_redirect($this->view->url(array('controller'=>'category', 'action'=>'index', 'id' => $this->_id),'content-admin-id'));//
    }

    public function editAction()
    {
        $this->_helper->viewRenderer->setNoController(true); //use generic templates
        
        $service = new Content_Model_Category_Service();
        $form = new Content_Form_Category();
        $form->addElement('submit', 'submit', array('label' => 'Update'));
        $object = $service->getObjectById($this->_id);
        $this->_setupCrumbs($object->set);
        
        if (!$this->getRequest()->isPost()) {
            //load exsiting values
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
        $object->fromArray($values);
        $service->update($object);

        $this->view->messages()->add('Successfully Updated Category &quot;'.$object->title.'&quot;');
        return $this->_redirect($this->view->url(array('controller'=>'category', 'action'=>'index', 'id' => $object->set),'content-admin-id').'?message=Updated');//
    }

    public function deleteAction()
    {
        $this->_helper->viewRenderer->setNoController(true); //use generic templates
        
        $service = new Content_Model_Category_Service();
        $object = $service->getObjectById($this->_id);
        $this->_setupCrumbs($object->set);
        $form = new Core_Form_Confirm();

        if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {

            $form->addDisplayGroup($form->getElements(), 'node', array ('legend' => 'Are you sure you want to delete "'.$object->title.'"?'));

            $this->view->form = $form;
            return;
        }
        
        //@todo check for sections using this set //block if any still do
        
        //@todo also delete amy categories in this set 

        $set = $object->set;
        
        if(!empty($_POST['confirm'])){
            
            $title = $object->title;
            $service->delete($object);
            $this->view->messages()->add('Successfully Deleted Category &quot;'.$title.'&quot;');
        }
        return $this->_redirect($this->view->url(array('controller'=>'category', 'action'=>'index', 'id' => $set),'content-admin-id'));
    }

    public function hideAction()
    {
        //ajax toggle
    }

    public function showAction()
    {
        //ajax toggle
    }
}