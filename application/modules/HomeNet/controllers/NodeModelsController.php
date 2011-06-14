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
 * @package HomeNet
 * @subpackage Room
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class HomeNet_NodeModelsController extends Zend_Controller_Action {

    public function init() {
        $this->view->id = $this->_getParam('model');
    }

    public function indexAction() {

        $nService = new HomeNet_Model_NodeModel_Service();
        $this->view->models = $nService->getObjects();
    }

    public function addAction() {
        $form = new HomeNet_Form_NodeModels();

        $form->addElement('submit', 'submit', array('label' => 'Add'));

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

        $nmService = new HomeNet_Model_NodeModel_Service();
      
        $nodeModel = new HomeNet_Model_NodeModel();

        $values['settings'] = unflattenArray($values['settings']);

        $nodeModel->fromArray($values);

        $nmService->create($nodeModel);

        //redirect to the next step
        return $this->_redirect($this->view->url(array('action'=>'index'),'homenet-nodemodels') . '?message=Added Device Model'); //
    }

    public function editAction() {
        $form = new HomeNet_Form_NodeModels();
        $form->addElement('submit', 'submit', array('label' => 'Update'));

        if (!$this->getRequest()->isPost()) {

            //load exsiting values
            $nmService = new HomeNet_Model_NodeModel_Service();
            $nodeModel = $nmService->getObjectById($this->view->id);
            $values = $nodeModel->toArray();
            $values['settings'] = flattenArray($values['settings']);

            $form->populate($values);

            $this->view->form = $form;
            return;
        }

        if (!$form->isValid($_POST)) {
            // Failed validation; redisplay form
            $this->view->form = $form;
            return;
        }

        $values = $form->getValues();

        $nmService = new HomeNet_Model_NodeModel_Service();
        $nodeModel = $nmService->getObjectById($this->view->id);

        $values['settings'] = unflattenArray($values['settings']);

        $nodeModel->fromArray($values);


        $nmService->update($nodeModel);

        return $this->_redirect($this->view->url(array('action'=>'index'),'homenet-nodemodels') . '?message=Updated Device Model');
    }

    public function removeAction() {

        $form = new Core_Form_Confirm();

        if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {
            $nmService = new HomeNet_Model_NodeModel_Service();
            $nodeModel = $nmService->getObjectById($this->view->id);

            $form->addDisplayGroup($form->getElements(), 'node', array('legend' => 'Are you sure you want to remove "' . $nodeModel->name . '"?'));

            $this->view->form = $form;
            return;
        }

        $values = $form->getValues();

        //need to figure out why this isn't in values
        if (!empty($_POST['delete'])) {
            $nmService = new HomeNet_Model_NodeModel_Service();
            $nodeModel = $nmService->getObjectById($this->view->id);
            $nmService->delete($nodeModel);
            return $this->_redirect($this->view->url(array('action'=>'index'),'homenet-nodemodels') . '?message=Deleted');
        }
        return $this->_redirect($this->view->url(array('action'=>'index'),'homenet-nodemodels') . '?message=Canceled');
    }

}