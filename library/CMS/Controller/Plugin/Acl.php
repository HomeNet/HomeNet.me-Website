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
        
        $config = Zend_Registry::get('config');

        //die(get_include_path());
        
        
        $auth = Zend_Auth::getInstance();

        $user = Core_Model_User_Manager::getUser();
        
        

        $module =     strtolower($request->getModuleName());
        $controller = strtolower($request->getControllerName());
        $action =     strtolower($request->getActionName());

        //framework may not list the module
        //@todo verify if this is still needed
        if (empty($module) || ($module == 'default')) {
            $module = 'core';
        }      
       
        $aManager = Core_Model_Acl_Manager::getInstance();

        $acl = $aManager->getUserAcl($module);
       
        $cResource = new CMS_Acl_Resource_Controller($controller);

        //check to see if resource exists, if it doesn't add it and let it inhert the default rules
        if (!$acl->has($cResource)) {
            $acl->add($cResource);
        }


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
//       if($acl->isAllowed($user, $cResource, $action)){
//            echo 'Is Allowed';
//       } else {
//            echo 'Not Allowed';
//        }
//       echo "<br>";
//       die('Failed Acl: '.$user->name.', ' . $module . ' > ' . $controller . ' > ' . $action);

      
        if (!$acl->isAllowed($user, $cResource, $action)) {
            
            $dispatcher = Zend_Controller_Front::getInstance()->getDispatcher();

            if (!$dispatcher->isDispatchable($request)) {           
               $request->setModuleName('Core');
               $request->setControllerName('Error');
               $request->setActionName('not-found');
   
               $request->setParam('error_message', 'Could not find route ' . $module . ' > ' . $controller . ' > ' . $action);            
           
                // $request->setParam('forward', true);



            } elseif ($user->id == $config->site->user->guest) { //if guest
                
              //  die('Route to Login: ' . $module . ' > ' . $request->controller . ' > ' . $action);
                if(isset($_SERVER['REQUEST_URI'])){
                    $_SESSION['login_redirect'] = $_SERVER['REQUEST_URI'];
                }
              //  $view = Zend_Registry::get('view');
                
                
                $request->setModuleName('Core');
                $request->setControllerName('Login');
                $request->setActionName('index');
               // $request->setParam('forward', true);
            } else {
                die('Not Authorized: ' . $module . ' > ' . $request->controller . ' > ' . $action);
                $request->setModuleName('Core');
                $request->setControllerName('error');
                $request->setActionName('noauth');
            }
        }
    }

}

//        if (!$auth->hasIdentity()) {  //guest
//            //check to see if guest profile is loaded
//            if (empty($_SESSION['User'])) {
//                $uService = new Core_Model_Acl_User_Service();
//                $guest = $uService->getObjectById($config->site->user->guest);
//                $_SESSION['User'] = $guest->toArray();
//            }
//        }
//        } else {
//            $user = $_SESSION['User'];
//        }

        //$uRole = new CMS_Acl_Role_User($_SESSION['User']['id']);