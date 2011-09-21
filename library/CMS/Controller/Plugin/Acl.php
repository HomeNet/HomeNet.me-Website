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
 * @package CMS
 * @subpackage Controller
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class CMS_Controller_Plugin_Acl extends Zend_Controller_Plugin_Abstract {

    public function preDispatch(Zend_Controller_Request_Abstract $request) {


//        // set up acl
//        $acl = new Zend_Acl();
//
//        // add the roles
//        $acl->addRole(new Zend_Acl_Role('guest'));
//        $acl->addRole(new Zend_Acl_Role('user'), 'guest');
//        $acl->addRole(new Zend_Acl_Role('administrator'), 'user');
//        // add the resources
//        $acl->add(new Zend_Acl_Resource('m_core'));
//        $acl->add(new Zend_Acl_Resource('m_core_c_index'),'m_core');
//        $acl->add(new Zend_Acl_Resource('m_core_c_register'),'m_core');
//        $acl->add(new Zend_Acl_Resource('m_core_c_error'),'m_core');
//        $acl->add(new Zend_Acl_Resource('m_core_c_login'),'m_core');
//        $acl->add(new Zend_Acl_Resource('m_core_c_user'),'m_core');
//        $acl->add(new Zend_Acl_Resource('m_core_c_contact'),'m_core');
//
//     
//     
//        $acl->allow(null, array('m_core_c_index', 'm_core_c_error'));
//        // a guest can only read content and login
//        $acl->allow('guest',  array('m_core_c_login','m_core_c_user', 'm_core_c_register'), null);
//        $acl->allow('guest',  array('m_core_c_user'), array('next-steps'));
//        // cms users can also work with content
//       // $acl->allow('user', 'page', array('list', 'create', 'edit', 'delete'));
//        // administrators can do anything
//       // $acl->allow('administrator', null);
//        $acl->allow('user');//, null);
///*
//        $acl->allow('guest', 'homenet_index', null);
//        $acl->allow('guest', 'homenet_device', null);
//        $acl->allow('guest', 'homenet_house', null);
//        $acl->allow('guest', 'homenet_room', null);*/
//
//        // fetch the current user

        $config = Zend_Registry::get('config');

        $auth = Zend_Auth::getInstance();




        if (!$auth->hasIdentity()) {  //guest
            //check to see if guest profile is loaded
            if (empty($_SESSION['User'])) {
                $uService = new Core_Model_Acl_User_Service();
                $guest = $uService->getObjectById($config->site->user->guest);
                $_SESSION['User'] = $guest->toArray();
            }
        }
//        } else {
//            $user = $_SESSION['User'];
//        }

        $uRole = new CMS_Acl_Role_User($_SESSION['User']['id']);

        $module = strtolower($request->module);

        //framework may not list the module
        //@todo verify if this is still needed
        if (empty($module) || ($module == 'default')) {
            $module = 'core';
        }

        $aManager = new Core_Model_Acl_Manager(new Core_Model_User(array('data' => $_SESSION['User'])));

        $acl = $aManager->getUserAcl($module);




        $cResource = new CMS_Acl_Resource_Controller($request->controller);

        //check to see if resource exists, if it doesn't add it and let it inhert the default rules
        if (!$acl->has($cResource)) {
            $acl->add($cResource);
        }

        $action = strtolower($request->action);
        //die('Failed Acl: ' . $module . ' > ' . $request->controller . ' > ' . $action);
       // die(debugArray($_SESSION));
       // die('User: ' . $uRole . '; Resource: ' . $cResource . '; Action: ' . $action);
//        if($acl->hasRole($uRole)){
//            echo 'Passed role: '.$uRole;
//        } else {
//            echo 'Failed role: '.$uRole;
//        }
//        echo "<br>";
//         if($acl->hasResource($cResource)){
//            echo 'Passed Resource: '.$cResource;
//        } else {
//            echo 'Failed Resource: '.$cResource;
//        }
//       echo "<br>";
//       flush();
//       
//       if($acl->isAllowed($uRole, $cResource, $action)){
//            echo 'Is Allowed';
//        } else {
//            echo 'Not Allowed';
//        }
//       echo "<br>";
//        die('Failed Acl: ' . $module . ' > ' . $request->controller . ' > ' . $action);
        if (!$acl->isAllowed($uRole, $cResource, $action)) {
            
            $dispatcher = Zend_Controller_Front::getInstance()->getDispatcher();

            if (!$dispatcher->isDispatchable($request)) {           

               $request->setControllerName('error');
               $request->setActionName('not-found');
   
               $request->setParam('error_message', 'Could not find route ' . $module . ' > ' . $request->controller . ' > ' . $action);            
           
                 $request->setParam('forward', true);



            } elseif ($_SESSION['User']['id'] == $config->site->user->guest) { //if guest
                
                die('Route to Login: ' . $module . ' > ' . $request->controller . ' > ' . $action);
                $request->setModuleName('core');
                $request->setControllerName('login');
                $request->setActionName('index');
                $request->setParam('forward', true);
            } else {
                die('Not Authorized: ' . $module . ' > ' . $request->controller . ' > ' . $action);
                $request->setModuleName('core');
                $request->setControllerName('error');
                $request->setActionName('noauth');
            }
        }
    }

}