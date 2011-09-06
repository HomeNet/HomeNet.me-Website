<?php

/**
 * Description of Service
 *
 * @author mdoll
 */
class Core_Model_Acl_Manager {

    private static $_resourceCache = null;
    private static $_modules = null;
    private static $_acl = array();
    private $_user = null;
    private $_aclIdentifier = null;
    private $_aclTags = array();

    public function __construct(Core_Model_User_Interface $user = null) {
        if (!is_null($user)) {
            $this->_user = $user;
        } else {
            $this->_user = $_SESSION['User'];
        }
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

        return self::$_modules;
    }

    /**
     * @return Zend_Acl
     */
    private function _getAcl($module, $roles, $resource = null, $objects = null) {

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
        if (!is_null($resource)) {
            if ($resource instanceof Zend_Acl_Resource_Interface) {
                $identifiers[] = $resource->getResourceId();
            } else {
                $identifiers[] = $resource;
            }
        }

        $tags = $identifiers; //get tags before crazy has is added
        //format objects
        if (!is_null($objects)) {
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


        //return false;
        return self::$_acl[$identifier];
    }

    private function _getLastAclIdentifier() {
        return $this->_aclIdentifier;
    }

    private function _setAcl(CMS_Acl $acl, $identifier) {

        if (!is_null($identifier)) {
          //  echo "\n" . print_r($this->_aclTags[$identifier], 1) . "\n";
            $cache = $this->_getCache();
            $cache->save($acl, $identifier, $this->_aclTags[$identifier]); //
            self::$_acl[$identifier] = $acl;
        }
    }

    public function getResources() {

        $array = array();

        $modules = $this->_getModules();
        foreach ($modules as $key => $value) {
            $array[$key] = $this->getResourcesByModule($key);
        }
        return $array;
    }

    public function getResourcesByModule($module) {

        $modules = $this->_getModules();
        if (empty($modules[$module])) {
            throw new NotFoundException('Module ' . $module . ' was not found');
        }

        $cache = $this->_getCache();

        $array = array();


        if (!$cache->test($module)) {

            $path = $modules[$module];

            foreach (scandir($path) as $file) {

                if (strstr($file, "Controller.php") !== false) {

                    include_once $path . DIRECTORY_SEPARATOR . $file;

                    foreach (get_declared_classes() as $class) {

                        if (is_subclass_of($class, 'Zend_Controller_Action')) {

                            $controller = strtolower(substr($class, 0, strpos($class, "Controller")));
                            $actions = array();

                            foreach (get_class_methods($class) as $action) {

                                if (strstr($action, "Action") !== false) {
                                    $actions[] = $action;
                                }
                            }
                        }
                    }

                    $array[$controller] = $actions;
                }
            }



            $cache->save($array, $module);
        } else {
            $array = $cache->load($module);
        }

        return $array;
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
     */

    public function getBaseAcl($module) {

        $acl = $this->_getAcl($module, 'base');
        if ($acl === false) { //cache call failed
            //this save some duplicate processing
            $identifier = $this->_getLastAclIdentifier();

            $gService = new Core_Model_Group_Service();
            $results = $gService->getObjectsByType(0);

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

                    $controller = null;

                    if (!is_null($rule->controller)) {

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

    public function getGroupAcl($module) {

        //get group list
        $memberships = $this->_user->getMemberships();

        $gRoles = array();
        foreach ($memberships as $groupId) {
            $gRoles[$groupId] = new CMS_Acl_Role_Group($groupId);
        }




        $acl = $this->_getAcl($module, $gRoles);
        if ($acl === false) { //cache call failed
            //this save some duplicate processing
            $identifier = $this->_getLastAclIdentifier();

            $acl = $this->getBaseAcl($module);

            $service = new Core_Model_Acl_Group_Service();
            $objects = $service->getObjectsByGroupsModule($memberships, $module);

            foreach ($objects as $group => $rules) {

                $gRole = $gRoles[$group];

                if (!$acl->hasRole($gRole)) {
                    $acl->addRole($gRole);
                }

                foreach ($rules as $rule) {

                    $controller = null;

                    if (!is_null($rule->controller)) {

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
        //save a copy to the cache
        //   $this->_setAcl($module, $acl);
        return $acl;
    }

    public function getUserAcl($module) {

        $uRole = new CMS_Acl_Role_User($this->_user->id);

        $acl = $this->_getAcl($module, $uRole);
        if ($acl === false) { //cache call failed
            //this save some duplicate processing
            $identifier = $this->_getLastAclIdentifier();


            $acl = $this->getGroupAcl($module);

            if (!$acl->hasRole($uRole)) {

                $parents = array();
                foreach ($this->_user->memberships as $groupId) {
                    $parents[] = new CMS_Acl_Role_Group($groupId);
                }

                $acl->addRole($uRole, $parents);
            }

            $service = new Core_Model_Acl_User_Service();

            $objects = $service->getObjectsByUserModule($this->_user->id, $module);

            //assumes to have the most nulls at the top
            foreach ($objects as $rule) {

                $cResource = null;

                if (!is_null($rule->controller)) {

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

    public function getGroupAclObjects($module, $controller, $objects) {

        //get group list
        $memberships = $this->_user->getMemberships();

        $gRoles = array();
        foreach ($memberships as $groupId) {
            $gRoles[$groupId] = new CMS_Acl_Role_Group($groupId);
        }

        $cResource = new CMS_Acl_Resource_Controller($controller);


        $acl = $this->_getAcl($module, $gRoles, $cResource, $objects);
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

                    if (is_null($rule->controller)) {
                        throw Exception('controller can\'t be Null');
                    }

                    //    $cResource = new CMS_Acl_Resource_Controller($rule->controller);



                    if (is_null($rule->object)) { //if object is null (not specified)
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

    public function getUserAclObjects($module, $controller, $objects) {

        $uRole = new CMS_Acl_Role_User($this->_user->id);
        $cResource = new CMS_Acl_Resource_Controller($controller);

        $acl = $this->_getAcl($module, $uRole, $cResource, $objects);
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

                if (is_null($rule->controller)) {
                    throw Exception('controller can\'t be Null');
                }

                if (is_null($rule->object)) {
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

}