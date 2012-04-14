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
 * @subpackage Field
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class Content_FieldController extends Zend_Controller_Action {

    private $_id;

    public function init() {
        $this->view->heading = 'Field'; //for generic templates
        $this->view->id = $this->_id = $this->_getParam('id');
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
            'controller' => 'section',
            'params' => array('id' => $id)
        ));

        $this->view->breadcrumbs()->addPage(array(
            'label' => 'Fields',
            'route' => 'content-admin-id',
            'module' => 'Content',
            'controller' => 'field',
            'params' => array('id' => $id)
        ));

        $this->view->heading = $section->title . ' Field';

        return $section;
    }

    public function indexAction() {
        $section = $this->_loadSection($this->_id);
        $this->view->heading = $section->title . ' Fields';

        $service = new Content_Model_FieldSet_Service();
        $this->view->objects = $service->getObjectsBySectionWithFields($this->_id);
    }

    public function newAction() {
        $this->_helper->viewRenderer->setNoController(true); //use generic templates
        $this->_loadSection($this->_id);
        $form = new Content_Form_Field($this->_id);
        $form->addElement('submit', 'submit', array('label' => 'Create'));

        $set = $this->_getParam('set');
        if (!empty($set)) {
            $e = $form->getElement('location');
            $e->setValue($set . '.0');
        }

        $this->view->assign('form', $form);

        if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {
            $this->view->form = $form;
            return;
        }

        $values = $form->getValues();
        $values['section'] = $this->_getParam('id');
        //temp fix @todo select cascade
        $location = explode('.', $values['location']);
        $values['set'] = $location[0];
        $values['order'] = $location[1];

        $service = new Content_Model_Field_Service();
        $object = $service->create($values);

        $this->view->messages()->add('Successfully Added Field &quot;' . $object->label . '&quot;');
        return $this->_redirect($this->view->url(array('controller' => 'field', 'action' => 'index', 'id' => $object->section), 'content-admin-id')); //
    }

    public function editAction() {
        $this->_helper->viewRenderer->setNoController(true); //use generic templates

        $service = new Content_Model_Field_Service();
        $object = $service->getObjectById($this->_getParam('id'));
        $this->_loadSection($object->section);
        $form = new Content_Form_Field($object->section);
        $form->addElement('submit', 'submit', array('label' => 'Update'));
     //   $form->addElement('hidden', 'section');

        if (!$this->getRequest()->isPost()) {
            //load exsiting values
            $values = $object->toArray();

            $e = $form->getElement('location');
            $e->setValue($values['set'] . '.' . $values['order']);

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
        //temp fix @todo select cascade
        $location = explode('.', $values['location']);
        $values['set'] = $location[0];
        $values['order'] = $location[1];

        $object = $service->getObjectById($this->_getParam('id'));
        $object->fromArray($values);
        $service->update($object);

        $this->view->messages()->add('Successfully Updated Field &quot;' . $object->label . '&quot;');
        return $this->_redirect($this->view->url(array('controller' => 'field', 'action' => 'index', 'id' => $object->section), 'content-admin-id'));
    }

    public function deleteAction() {
        $this->_helper->viewRenderer->setNoController(true); //use generic templates

        $service = new Content_Model_Field_Service();
        $object = $service->getObjectById($this->_id);
        $this->_loadSection($object->section);
        $form = new Core_Form_Confirm();

        if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {

            $form->addDisplayGroup($form->getElements(), 'node', array('legend' => 'Are you sure you want to delete "' . $object->label . '"?'));

            $this->view->form = $form;
            return;
        }
        
        $section = $object->section;

        if (!empty($_POST['confirm'])) {
            $label = $object->label;
            $service->delete($object);
            $this->view->messages()->add('Successfully Deleted Field &quot;' . $label . '&quot;');
        }
        return $this->_redirect($this->view->url(array('controller' => 'field', 'action' => 'index', 'id' => $section), 'content-admin-id'));
    }

    public function changeOrderAjaxAction() {

        $id = $this->_id;
        $set = $this->_getParam('set');
        $order = $this->_getParam('order');


        if (empty($id) || !is_numeric($id)) {
            throw new InvalidArgumentException('Missing Field Id');
        }

        if (empty($set) || !is_numeric($set)) {
            throw new InvalidArgumentException('Missing FieldSet Id');
        }

        if (!is_numeric($order)) {
            throw new InvalidArgumentException('Invalid Order');
        }

        $service = new Content_Model_Field_Service();
        try {
            $service->setObjectOrder($id, $set, $order);
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        $this->_helper->viewRenderer->setNoRender(true);
        // print_r($this->_getAllParams());
        echo 'success';
    }

    public function elementFormAjaxAction() {

        // die('works');
        $this->_helper->viewRenderer->setNoRender(true);
        $element = $this->_getParam('element');
        //$element = $_GET['element'];
        if ($element === null) {
            throw new InvalidArgumentException('Element Required' . $element);
        }
        
        $class = 'Content_Plugin_Element_' . ucfirst($element) . '_Element';

        if (!class_exists($class, true)) {
            throw new InvalidArgumentException('Invaild Class: ' . $class);
        }

        $object = new $class();
        $form = $object->getSetupForm();
        echo $form->render();
    }

}