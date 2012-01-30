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
 * @subpackage Apikey
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class HomeNet_ApikeyController extends Zend_Controller_Action
{

    private $_house;
    
    public function init()
    {
        $this->view->house= $this->_house = $this->_getParam('house');
        $acl = new HomeNet_Model_Acl($this->_house);
        $acl->checkAccess('house', $this->getRequest()->getActionName());
    }

    public function indexAction()
    {
        $service = new HomeNet_Model_Apikey_Service();
        $objects = $service->getObjectsByHouseUser($this->_house);

        if (count($objects) == 0) {
            $row = $service->createApikeyForHouse($this->_house);
            $this->view->apikey = $row->id;
        } else { 
            $this->view->apikey = $objects[0]->id;
        }
    }

    public function newAction()
    {
        // action body
    }

    public function editAction()
    {
        // action body
    }

    public function deleteAction()
    {
        // action body
    }




}









