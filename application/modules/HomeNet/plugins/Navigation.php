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
 * along with HomeNet.  If not, see <http ://www.gnu.org/licenses/>.
 */

//based on http://weierophinney.net/matthew/archives/234-Module-Bootstraps-in-Zend-Framework-Dos-and-Donts.html

class HomeNet_Plugin_Navigation extends Zend_Controller_Plugin_Abstract {

    private static $_buildNav = false;
    private $_houses;

    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        
        $module =     strtolower($request->getModuleName());
        $controller = strtolower($request->getControllerName());
        $action =     strtolower($request->getActionName());
        
        $id = $request->getParam('id', 'No ID');
        
        Zend_Registry::get('logger')->info("Route: $module > $controller > $action; $id" );
       // print_r($request->getParams());
        //exit;   
        
        if ('homenet' != strtolower($request->getModuleName())) {
            // If not in this module, return early
            return;
        }
        $skip = array('setup', 'login', 'node-model', 'device-model', 'component-model');

        if (in_array(strtolower($request->getControllerName()), $skip)) {
            // If doesn't apply to this controller, return early
            return;
        }

        //$view->addBasePath(dirname(dirname(__FILE__)) . '/views/');
        //$layout->assign('column','sdfsfsffsf');
        //
        //check to see if we should a specfic house
        $house = $request->getUserParam('house');  
        $service = new HomeNet_Model_House_Service();
        // $userHousesIds = $service->getHouseIdsByUser();
        $userHousesIds = HomeNet_Model_House_Manager::getHouseIds();

        $houses = array();

        if ($house) {
    //        if(empty($userHousesIds)){
    //            return;
    //        }

            //Make person can see house;        
            $acl = new HomeNet_Model_Acl($house);
            $acl->checkAccess('house', 'index');//throws exception if denied

            //check to see if it's one of the current users houses'
            if (in_array($house, $userHousesIds)) {
                //one of the users houses
               // $houses = $service->getObjectsByIdsWithRooms($userHousesIds);
                foreach($userHousesIds as $value){
                    $houses[] = $service->getObjectByIdWithRooms($value);
                }
                
                
                
            } else {
                //not the users house or is a guest
                $houses[0] = $service->getObjectByIdWithRooms($house);
            }
        } elseif(!empty($userHousesIds)) {
            try {
                $houses = $service->getObjectsByIdsWithRooms($userHousesIds);
            } catch (Exception $e) {
                $houses = array();
            }
        }
       // var_dump($houses);
       // exit;

        $this->_houses = $houses;

        //$_SESSION['HomeNet']['Houses'] = $houses;
        //die(debugArray($houses));

        $skip = array('add', 'add2', 'edit', 'delete', 'remove', 'code');
        if (!in_array(strtolower($request->getActionName()), $skip)) {

            self::$_buildNav = true;
        }
    }
    
    public static function hideNavigation(){
         self::$_buildNav = false;
    }

    public function postDispatch(Zend_Controller_Request_Abstract $request) {

        if (!self::$_buildNav) {
            return;
        }
        //die(debugArray($this->_buildNav));
        $layout = Zend_Layout::getMvcInstance();
        if (!$layout->isEnabled()) {
            return;
        }

        //$view = $layout->getView();
        $layout->setLayout('side-menu');
        $nav = new HomeNet_Model_Navigation($this->_houses);

        $options = array(
            'indent' => 16,
            'ulClass' => 'navMenu ui-widget ui-state-default ui-corner-all'
        );
        $view = $layout->getView();
        $view->navigation()->menu()->setPartial(array('_menu.phtml', 'Core'));

        $menu = $view->navigation()
                ->menu()
                ->render($nav, $options);

        $layout->assign('menu', $menu);
    }

}