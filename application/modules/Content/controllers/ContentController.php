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
class Content_ContentController extends Zend_Controller_Action {

    private $_id;

    public function init() {
        $this->view->controllerTitle = 'Content'; //for generic templates      
        $this->_id = $this->view->id = $this->_getParam('id');
    }

    private function _loadSection($id) {
        $this->view->breadcrumbs()->addPage(array(
            'label' => 'Admin',
            'route' => 'admin'
        ));

        $this->view->breadcrumbs()->addPage(array(
            'label' => 'Content',
            'route' => 'content-admin',
            'module' => 'Content',
            'controller' => 'section',
        ));

        $sService = new Content_Model_Section_Service();
        $section = $sService->getObjectById($id);
        // 

        $this->view->breadcrumbs()->addPage(array(
            'label' => $section->title,
            'route' => 'content-admin-id',
            'module' => 'Content',
            'controller' => 'content',
            'params' => array('id' => $id)
        ));

//        $this->view->breadcrumbs()->addPage(array(
//            'label' => 'Content',
//            'route' => 'content-admin-id',
//            'module' => 'Content',
//            'controller' => 'content',
//            'params' => array('id' => $id)
//        ));

        $this->view->heading = $section->title . ' Content';

        return $section;
    }

    public function indexAction() {
        $this->_loadSection($this->_id);
        $service = new Content_Model_Content_Service();
        $this->view->assign('objects', $service->getObjectsBySection($this->_id));
    }

    public function newAction() {
        $this->_loadSection($this->_id);

        $this->_helper->viewRenderer->setNoController(true); //use generic templates
        $manager = new Content_Model_Section_Manager();
        $form = $manager->getForm($this->_getParam('id'));
        $form->addElement('submit', 'submit', array('label' => 'Create'));
        $this->view->assign('form', $form);

        if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {
            // Failed validation; redisplay form
            $this->view->form = $form;
            return;
        }

        //save
        $values = $form->getValues();

        $service = new Content_Model_Content_Service();
        $values['owner'] = Core_Model_User_Manager::getUser()->id;
        $values['section'] = $this->_id;
        $object = $service->create($values);

        $this->view->messages()->add('Successfully Added Content &quot;' . $object->title . '&quot;');
        return $this->_redirect($this->view->url(array('controller' => 'content', 'action' => 'index', 'id' => $this->_id), 'content-admin-id')); //
    }

    public function editAction() {
        $this->_helper->viewRenderer->setNoController(true); //use generic templates

        $service = new Content_Model_Content_Service();
        $object = $service->getObjectById($this->_id);
        $this->_loadSection($object->section);
        //  die(debugArray($object));
        $form = $object->getForm();
        $form->addElement('submit', 'submit', array('label' => 'Update'));
        $form->addElement('hidden', 'section', array('value' => $object->section));
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
        $values['owner'] = Core_Model_User_Manager::getUser()->id;
        $object->fromArray($values);
        
        $service->update($object);
        //exit;
        $this->view->messages()->add('Successfully Updated Content &quot;' . $object->title . '&quot;');
        return $this->_redirect($this->view->url(array('controller' => 'content', 'action' => 'index', 'id' => $object->section), 'content-admin-id')); //
    }

    public function deleteAction() {
        $this->_helper->viewRenderer->setNoController(true); //use generic templates

        $service = new Content_Model_Content_Service();
        $object = $service->getObjectById($this->_id);

        $this->_loadSection($object->section);

        $form = new Core_Form_Confirm();

        if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {

            $form->addDisplayGroup($form->getElements(), 'node', array('legend' => 'Are you sure you want to delete "' . $object->title . '"?'));

            $this->view->form = $form;
            return;
        }
        
        $section = $object->section;
        
        if (!empty($_POST['confirm'])) {
            
            $title = $object->title;
            $service->delete($object);
            $this->view->messages()->add('Successfully Deleted Content &quot;'.$title.'&quot;'); 
        }
        return $this->_redirect($this->view->url(array('controller' => 'content', 'action' => 'index', 'id' => $section), 'content-admin-id'));
    }

    public function restAjaxAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $element = $this->_getParam('element');
        $method = $this->_getParam('method');

        $class = 'Content_Plugin_Element_' . ucfirst($element) . '_Rest';

        if (!class_exists($class, true)) {
            throw new InvalidArgumentException('Invaild Class: ' . $class, 500);
        }

//            $server = new Zend_Rest_Server();
//            $server->setClass($class);
//            $server->handle();
//             $server = new Zend_Json_Server();
//             $server->setClass($class);
//             $server->handle();
        //Target our class
        $reflector = new ReflectionClass($class);

//Get the parameters of a method
        $parameters = $reflector->getMethod($method)->getParameters();
        $p = array();
//Loop through each parameter and get the type
        foreach ($parameters as $param) {
            $p[] = $this->_getParam($param->getname());
        }

        $server = new $class();
        echo call_user_func_array(array($server, $method), $p);
    }
}