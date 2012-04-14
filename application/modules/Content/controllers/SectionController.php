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
 * @subpackage Section
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class Content_SectionController extends Zend_Controller_Action
{

    private $_id;
    
    public function init()
    {
        $this->view->heading = 'Section'; //for generic templates

        $this->view->breadcrumbs()->addPage(array(
            'label'  => 'Admin',
            'route'  => 'admin'          
        ));
//        $this->view->breadcrumbs()->addPage(array(
//            'label'  => 'Content',
//          //  'route'  => 'content-admin',  
//          //  'module' => 'Content'
//            'uri' => '#'
//        ));
        $this->view->breadcrumbs()->addPage(array(
            'label'  => 'Content',
            'route'  => 'content-admin',  
            'module' => 'Content',
            'controller' => 'section'
        ));
       // echo $this->view->breadcrumbs()->render();
    }

    public function indexAction()
    {
        $service = new Content_Model_Section_Service();
        $this->view->objects = $service->getObjects();
    }

    public function newAction()
    {
        $this->_helper->viewRenderer->setNoController(true); //use generic templates
        
        $form = new Content_Form_Section();
        $form->addElement('submit', 'submit', array('label' => 'Create'));
        $this->view->assign('form',$form);

        if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {
            // Failed validation; redisplay form
            $this->view->form = $form;
            return;
        }
        
        //save
        $values = $form->getValues();

        $service = new Content_Model_Section_Manager();
        $object = $service->createByTemplate($values);
        
        $this->view->messages()->add('Successfully Added Section &quot;' . $object->title . '&quot;');
        return $this->_redirect($this->view->url(array('controller'=>'section', 'action'=>'index'),'content-admin'));//
    }

    public function editAction()
    {
        $this->_helper->viewRenderer->setNoController(true); //use generic templates
        
        $service = new Content_Model_Section_Service();
        $form = new Content_Form_Section();
        $form->removeElement('template');
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

        $this->view->messages()->add('Successfully Updated Section &quot;' . $object->title . '&quot;');
        return $this->_redirect($this->view->url(array('controller'=>'section'),'content-admin'));//
    }

    public function deleteAction()
    {
        $this->_helper->viewRenderer->setNoController(true); //use generic templates
        
        $csService = new Content_Model_Section_Service();
        $object = $csService->getObjectById($this->_getParam('id'));
        $form = new Core_Form_Confirm();

        if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {

            $form->addDisplayGroup($form->getElements(), 'node', array ('legend' => 'Are you sure you want to delete "'.$object->title.'"?'));

            $this->view->form = $form;
            return;
        }

        if(!empty($_POST['confirm'])){
            $title = $object->title;
            $csService->delete($object);
            $this->view->messages()->add('Successfully Deleted Section &quot;' . $title . '&quot;');

        }
        return $this->_redirect($this->view->url(array('controller'=>'section'),'content-admin'));
    }
}