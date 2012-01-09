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
 * @subpackage CategorySet
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class Content_CategorySetController extends Zend_Controller_Action
{

    public function init()
    {
        $this->_setupCrumbs();
    }
    
      private function _setupCrumbs(){
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

        $this->view->heading = 'Category Set';
        
       //return $section;
    }
    

    public function indexAction()
    {
        $csService = new Content_Model_CategorySet_Service();
        $this->view->assign('objects', $csService->getObjects());
    }

    public function newAction()
    {
        $this->_helper->viewRenderer->setNoController(true); //use generic templates
        
        $form = new Content_Form_CategorySet();
        $form->addElement('submit', 'submit', array('label' => 'Create'));
        $this->view->assign('form',$form);
        
        
        
        //$this->_helper->viewRenderer('../generic/new');

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

        $csService = new Content_Model_CategorySet_Service();
        $object = $csService->create($values);
        $this->view->messages()->add('Successfully added new Category Set &quot;'.$object->title.'&quot;');
        return $this->_redirect($this->view->url(array('controller'=>'category-set', 'action'=>'index'),'content-admin'));//
    }

    public function editAction()
    {
        $this->_helper->viewRenderer->setNoController(true); //use generic templates
        
        $csService = new Content_Model_CategorySet_Service();
        $form = new Content_Form_CategorySet();
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
        $object = $csService->update($object);
        
        $this->view->messages()->add('Successfully Updated Category Set &quot;'.$object->title.'&quot;');
        return $this->_redirect($this->view->url(array('controller'=>'category-set'),'content-admin'));//
    }

    public function deleteAction()
    {
        $this->_helper->viewRenderer->setNoController(true); //use generic templates
        
        $csService = new Content_Model_CategorySet_Service();
        $object = $csService->getObjectById($this->_getParam('id'));
        $form = new Core_Form_Confirm();

        if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {

            $form->addDisplayGroup($form->getElements(), 'node', array ('legend' => 'Are you sure you want to delete "'.$object->title.'"?'));

            $this->view->form = $form;
            return;
        }

        $values = $form->getValues();

        if(!empty($_POST['confirm'])){
            $title = $object->title;
            $csService->delete($object);
            $this->view->messages()->add('Successfully Deleted Category Set &quot;'.$title.'&quot;');
        }
        return $this->_redirect($this->view->url(array('controller'=>'category-set'),'content-admin'));
    }
}