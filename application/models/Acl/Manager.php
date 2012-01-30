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
 * @todo add function to clean cache
 */

/**
 * @package Core
 * @subpackage Acl
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class Core_Model_Acl_Manager {

    private static $_resourceCache;
    private static $_modules;
    private static $_acl = array();
    private $_user;
    private $_aclIdentifier;
    private $_aclTags = array();

    /**
     * @var Core_Model_Acl_Manager
     */
    private static $_instance;

    public function __construct(Core_Model_User_Interface $user = null) {
        if ($user !== null) {
            $this->_user = $user;
        } else {
            $this->_user = Core_Model_User_Manager::getUser();
        }
    }

    /**
     * @return Core_Model_Acl_Manager 
     */
    static public function getInstance() {
        if (self::$_instance === null) {
            self::$_instance = new Core_Model_Acl_Manager();
        }
        return self::$_instance;
    }

    public function setUser(Core_Model_User_Interface $user) {
        $this->_user = $user;
    }

    public function getUser() {
        return $this->_user;
    }

    /**
     *
     * @return Zend_Cache_Core
     */
    private function _getCache() {

        if (self::$_resourceCache == null) {


            //tradtional way wasn't working to unit test      
//            $front = Zend_Controller_Front::getInstance();
//
//            $manager = $front->getParam('bootstrap')
//                    ->getResource('cachemanager');

            $manager = Zend_Registry::get('cachemanager');
            self::$_resourceCache = $manager->getCache('acl-resources');
        }
        return self::$_resourceCache;
    }

    private function _getModules() {

        if (self::$_modules == null) {
            $front = Zend_Controller_Front::getInstance();
            self::$_modules = $front->getControllerDirectory();
        }
        //fix default


        return self::$_modules;
    }

    /**
     * @return Zend_Acl
     */
    private function _getAcl($roles, $module, $collection = null, $resource = null, $objects = null) {

        $identifiers = array($module);

        if (is_array($roles)) {
            foreach ($roles as $value) {
                if ($value instanceof Zend_Acl_Role_Interface) {
                    $identifiers[] = $value->getRoleId();
                } else {
                    $identifiers[] = $value;
                }
            }
        } else {
            if ($roles instanceof Zend_Acl_Role_Interface) {
                $identifiers[] = $roles->getRoleId();
            } else {
                $identifiers[] = $roles;
            }
        }

        //format resources
        if ($resource !== null) {
            if ($resource instanceof Zend_Acl_Resource_Interface) {
                $identifiers[] = $resource->getResourceId();
            } else {
                $identifiers[] = $resource;
            }
        }

        if ($collection !== null) {
            $identifiers[] = 'x' . $collection;
        }

        $tags = $identifiers; //get tags before crazy object list is added
        //format objects
        if ($objects !== null) {
            if (is_array($objects)) {
                $identifiers[] = md5(serialize($objects));
            } else {
                $identifiers[] = $objects;
            }
        }

        $identifier = implode('_', $identifiers);



        self::$_acl[$identifier] = false;

        if (empty(self::$_acl[$identifier])) {
            $cache = $this->_getCache();
            self::$_acl[$identifier] = $cache->load($identifier);
        }

        $this->_aclIdentifier = $identifier;
        $this->_aclTags[$identifier] = $tags;


        return false;
        return self::$_acl[$identifier];
    }

    private function _getLastAclIdentifier() {
        return $this->_aclIdentifier;
    }

    private function _setAcl(CMS_Acl $acl, $identifier) {

        if ($identifier !== null) {
            //  echo "\n" . print_r($this->_aclTags[$identifier], 1) . "\n";
            $cache = $this->_getCache();
            $cache->save($acl, $identifier, $this->_aclTags[$identifier]); //
            self::$_acl[$identifier] = $acl;
        }
    }

    /**
     * @return array Resources
     */
    public function getResources() {

        $array = array();

        $modules = $this->_getModules();
        foreach ($modules as $key => $value) {
            $array[$key] = $this->getResourcesByModule($key);
        }
        return $array;
    }

    public function getResourcesByModule($module) {

//        $modules = $this->_getModules();
//        if (empty($modules[$module])) {
//            throw new NotFoundException('Module ' . $module . ' was not found');
//        }
//
//        $cache = $this->_getCache();
//
//        $array = array();
//
//
//        if (!$cache->test($module)) {
//
//            $path = $modules[$module];
//
//            foreach (scandir($path) as $file) {
//
//                if (strstr($file, "Controller.php") !== false) {
//
//                    include_once $path . DIRECTORY_SEPARATOR . $file;
//
//                    foreach (get_declared_classes() as $class) {
//
//                        if (is_subclass_of($class, 'Zend_Controller_Action')) {
//
//                            $controller = strtolower(substr($class, 0, strpos($class, "Controller")));
//                            $actions = array();
//
//                            foreach (get_class_methods($class) as $action) {
//
//                                if (strstr($action, "Action") !== false) {
//                                    $actions[] = $action;
//                                }
//                            }
//                        }
//                    }
//
//                    $array[$controller] = $actions;
//                }
//            }
//
//
//
//            $cache->save($array, $module);
//        } else {
//            $array = $cache->load($module);
//        }
//
//        return $array;
    }

    private function _buildAcl(Zend_Acl $acl, array $objects) {
//         foreach($objects as $acl){
//             
//             
//             
//             
//                $acl->addResource()
//            }
    }

    /*
     * Loads global acls like owner
     * 
     * @param string $module
     * @return CMS_Acl
     */

    public function getBaseAcl($module) {

        $module = strtolower($module);

        $acl = $this->_getAcl('base', $module);
        if ($acl === false) { //cache call failed
            //this save some duplicate processing
            $identifier = $this->_getLastAclIdentifier();

            $gService = new Core_Model_Group_Service();
            $results = $gService->getObjectsByType(0); //0 is everyone and owner, basiclly the built system users

            $groups = array();
            foreach ($results as $result) {
                $groups[] = $result->id;
            }

            $service = new Core_Model_Acl_Group_Service();
            $objects = $service->getObjectsByGroupsModule($groups, $module);

            $acl = new CMS_Acl();

            foreach ($objects as $group => $rules) {

                $gRole = new CMS_Acl_Role_Group($group);

                $acl->addRole($gRole);
                // $parents[] = $gRole;

                foreach ($rules as $rule) {

                    $cResource = null;

                    if ($rule->controller !== null) {

                        $cResource = new CMS_Acl_Resource_Controller($rule->controller);

                        if (!$acl->hasResource($cResource)) {
                            $acl->addResource($cResource);
                        }
                    }

                    if ($rule->permission == 1) {
                        $acl->allow($gRole, $cResource, $rule->action);
                    } else {
                        $acl->deny($gRole, $cResource, $rule->action);
                    }
                }
            }

            $this->_setAcl($acl, $identifier); //save to cache
        }
        return $acl;
    }

    /**
     * @param string $module
     * @return CMS_Acl
     */
    public function getGroupAcl($module) {

        $module = strtolower($module);

        //get group list
        $memberships = $this->_user->getMemberships();
        $gRoles = array();
        foreach ($memberships as $groupId) {
            $gRoles[$groupId] = new CMS_Acl_Role_Group($groupId);
        }

        $acl = $this->_getAcl($gRoles, $module);

        if ($acl === false) { //cache call failed
            //this save some duplicate processing
            $identifier = $this->_getLastAclIdentifier();

            $acl = $this->getBaseAcl($module);

            $service = new Core_Model_Acl_Group_Service();

            //add group roles
            foreach ($gRoles as $role) {
                if (!$acl->hasRole($role)) {
                    $acl->addRole($role);
                }
            }

            $objects = $service->getObjectsByGroupsModule($memberships, $module);

            foreach ($objects as $group => $rules) {

                foreach ($rules as $rule) {

                    $cResource = null;

                    if ($rule->controller !== null) {

                        $cResource = new CMS_Acl_Resource_Controller($rule->controller);

                        if (!$acl->hasResource($cResource)) {
                            $acl->addResource($cResource);
                        }
                    }

                    if ($rule->permission == 1) {
                        if(empty($gRoles[$rule->group])){
                            die('can\'t find '.$rule->group);
                        }
                        $acl->allow($gRoles[$rule->group], $cResource, $rule->action);
                    } else {
                        $acl->deny($gRoles[$rule->group], $cResource, $rule->action);
                    }
                }
            }

            $this->_setAcl($acl, $identifier); //save to cache
        }

        return $acl;
    }

    /**
     * @param string $module
     * @return CMS_Acl
     */
    public function getUserAcl($module) {

        $module = strtolower($module);

        $uRole = new CMS_Acl_Role_User($this->_user->id);

        $acl = $this->_getAcl($uRole, $module);
        if ($acl === false) { //cache call failed
            //this save some duplicate processing
            $identifier = $this->_getLastAclIdentifier();

            $acl = $this->getGroupAcl($module);

            if (!$acl->hasRole($uRole)) {

                $memberships = $this->_user->getMemberships();
                $parents = array();

                foreach ($memberships as $groupId) {
                    //$parent


                    $parents[] = new CMS_Acl_Role_Group($groupId);
                }

                $acl->addRole($uRole, $parents);
            }

            $service = new Core_Model_Acl_User_Service();

            $objects = $service->getObjectsByUserModule($this->_user->id, $module);

            //assumes to have the most nulls at the top
            foreach ($objects as $rule) {

                $cResource = null;

                if ($rule->controller !== null) {

                    $cResource = new CMS_Acl_Resource_Controller($rule->controller);

                    if (!$acl->hasResource($cResource)) {
                        $acl->addResource($cResource);
                    }
                }

                if ($rule->permission == 1) {
                    $acl->allow($uRole, $cResource, $rule->action);
                } else {
                    $acl->deny($uRole, $cResource, $rule->action);
                }
            }


            $this->_setAcl($acl, $identifier); //save to cache
        }
        return $acl;
    }

    /**
     * @param string $module
     * @param string $controller
     * @param array $objects
     * @return CMS_Acl 
     */
    public function getGroupAclObjects($module, $controller, $objects) {

        $module = strtolower($module);

        //get group list
        $memberships = $this->_user->getMemberships();

        $gRoles = array();
        foreach ($memberships as $groupId) {
            $gRoles[$groupId] = new CMS_Acl_Role_Group($groupId);
        }

        $cResource = new CMS_Acl_Resource_Controller($controller);


        $acl = $this->_getAcl($gRoles, $module, null, $cResource, $objects);
        if ($acl === false) { //cache call failed
            //this save some duplicate processing
            $identifier = $this->_getLastAclIdentifier();

            $acl = $this->getGroupAcl($module);

            //get group list
            $memberships = $this->_user->getMemberships();

            $service = new Core_Model_Acl_Group_Service();
            $objects = $service->getObjectsByGroupsModuleControllerObjects($memberships, $module, $controller, $objects);

            //add the resource if it doesn't exist.
            if (!$acl->hasResource($cResource)) {
                $acl->addResource($cResource);
            }

            foreach ($objects as $group => $rules) {

                $gRole = new CMS_Acl_Role_Group($group);

                if (!$acl->hasRole($gRole)) {
                    $acl->addRole($gRole);
                }
                // $parents[] = $gRole;

                foreach ($rules as $rule) {

                    // $cResource = null;

                    if ($rule->controller === null) {
                        throw Exception('controller can\'t be Null');
                    }

                    //    $cResource = new CMS_Acl_Resource_Controller($rule->controller);
                    if ($rule->object === null) { //if object is null (not specified)
                        throw Exception('object can\'t be Null');
                    }

                    //if it has a defined object create the resource

                    $oResource = new CMS_Acl_Resource_Object($rule->controller, $rule->object);

                    if (!$acl->hasResource($oResource)) {
                        $acl->addResource($oResource, $cResource);
                    }

                    //create permissions
                    if ($rule->permission == 1) {
                        $acl->allow($gRole, $oResource, $rule->action);
                    } else {
                        $acl->deny($gRole, $oResource, $rule->action);
                    }
                }
            }
            $this->_setAcl($acl, $identifier); //save to cache
        }
        return $acl;
    }

    /**
     * @param string $module
     * @param string $controller
     * @param array $objects
     * @return CMS_Acl 
     */
    public function getUserAclObjects($module, $controller, $objects) {

        $module = strtolower($module);

        $uRole = new CMS_Acl_Role_User($this->_user->id);
        $cResource = new CMS_Acl_Resource_Controller($controller);

        $acl = $this->_getAcl($uRole, $module, null, $cResource, $objects);
        if ($acl === false) { //cache call failed
            //this save some duplicate processing
            $identifier = $this->_getLastAclIdentifier();

            $acl = $this->getUserAcl($module);

            //add the resource if it doesn't exist.
            if (!$acl->hasResource($cResource)) {
                $acl->addResource($cResource);
            }

            $service = new Core_Model_Acl_User_Service();
            $objects = $service->getObjectsByUserModuleControllerObjects($this->_user->id, $module, $controller, $objects);

            //assumes to have the most nulls at the top
            foreach ($objects as $rule) {

                if ($rule->controller === null) {
                    throw Exception('controller can\'t be Null');
                }

                if ($rule->object === null) {
                    throw Exception('object can\'t be Null');
                }

                //if it has a defined object create the resource
                $oResource = new CMS_Acl_Resource_Object($rule->controller, $rule->object);
                if (!$acl->hasResource($oResource)) {
                    $acl->addResource($oResource, $cResource);
                }

                //create permissions
                if ($rule->permission == 1) {
                    $acl->allow($uRole, $oResource, $rule->action);
                } else {
                    $acl->deny($uRole, $oResource, $rule->action);
                }
            }
            $this->_setAcl($acl, $identifier); //save to cache
        }
        return $acl;
    }

    /**
     * @param string $module
     * @param string $controller
     * @param array $collection
     * @return CMS_Acl 
     */
    public function getGroupAclCollection($module, $collection) {

        $controllerList = array();


        $module = strtolower($module);

        //get group list
        $memberships = $this->_user->getMemberships();

        $gRoles = array();
        foreach ($memberships as $groupId) {
            $gRoles[$groupId] = new CMS_Acl_Role_Group($groupId);
        }

        $acl = $this->_getAcl($gRoles, $module, $collection);
        if ($acl === false) { //cache call failed
            //this save some duplicate processing
            $identifier = $this->_getLastAclIdentifier();

           $acl = new CMS_Acl();
           // $acl = $this->getGroupAcl($module);

            //get group list
            $service = new Core_Model_Acl_Group_Service();
            $objects = $service->getObjectsByGroupsModuleCollection($memberships, $module, $collection);

            //add collection
            // $xResource = new CMS_Acl_Resource_Collection($collection);
            //add the resource if it doesn't exist.
            // if (!$acl->hasResource($xResource)) {
            //     $acl->addResource($xResource);
            // }

            foreach ($objects as $group => $rules) {

                $gRole = new CMS_Acl_Role_Group($group);

                if (!$acl->hasRole($gRole)) {
                    $acl->addRole($gRole);
                }
                // $parents[] = $gRole;

                foreach ($rules as $rule) {

                    $resource = null;

                    if (($rule->controller) != null && !array_key_exists($rule->controller, $controllerList)) {

                        //add controllers to collection                    
                        $cResource = new CMS_Acl_Resource_Controller($rule->controller);

                        //add the resource if it doesn't exist.
                        if (!$acl->hasResource($cResource)) {
                            $acl->addResource($cResource);
                        }
                        $controllerList[$rule->controller] = $cResource;
                        $resource = $cResource;
                    }

                    if (($rule->controller !== null) && ($rule->object !== null)) {

                        $oResource = new CMS_Acl_Resource_Object($rule->controller, $rule->object);

                        if (!$acl->hasResource($oResource)) {
                            $acl->addResource($oResource, $controllerList[$rule->controller]);
                        }
                        $resource = $oResource;
                    }

                    //create permissions
                    if ($rule->permission == 1) {
                        $acl->allow($gRole, $resource, $rule->action);
                    } else {
                        $acl->deny($gRole, $resource, $rule->action);
                    }
                }
            }
            $this->_setAcl($acl, $identifier); //save to cache
        }
        return $acl;
    }

    /**
     * @param string $module
     * @param string $controller
     * @param array $collection
     * @return CMS_Acl 
     */
    public function getUserAclCollection($module, $collection) {
        $controllerList = array();

        $module = strtolower($module);

        $uRole = new CMS_Acl_Role_User($this->_user->id);

        $acl = $this->_getAcl($uRole, $module, $collection);

        if ($acl === false) { //cache call failed
            $identifier = $this->_getLastAclIdentifier(); //this save some duplicate processing

            $acl = $this->getGroupAclCollection($module, $collection);

            $service = new Core_Model_Acl_User_Service();
            $objects = $service->getObjectsByUserModuleCollection($this->_user->id, $module, $collection);

            if (!$acl->hasRole($uRole)) {
                $acl->addRole($uRole);
            }

            //assumes to have the most nulls at the top
            foreach ($objects as $rule) {

                $resource = null;

                if (($rule->controller) != null && !array_key_exists($rule->controller, $controllerList)) {

                    //add controllers to collection                    
                    $cResource = new CMS_Acl_Resource_Controller($rule->controller);

                    //add the resource if it doesn't exist.
                    if (!$acl->hasResource($cResource)) {
                        $acl->addResource($cResource);
                    }
                    $controllerList[$rule->controller] = $cResource;
                    $resource = $cResource;
                }

                if (($rule->controller !== null) && ($rule->object !== null)) {

                    $oResource = new CMS_Acl_Resource_Object($rule->controller, $rule->object);

                    if (!$acl->hasResource($oResource)) {
                        $acl->addResource($oResource, $controllerList[$rule->controller]);
                    }
                    $resource = $oResource;
                }

                //create permissions
                if ($rule->permission == 1) {
                    $acl->allow($uRole, $resource, $rule->action);
                } else {
                    $acl->deny($uRole, $resource, $rule->action);
                }
            }
            $this->_setAcl($acl, $identifier); //save to cache
        }
        return $acl;
    }

}