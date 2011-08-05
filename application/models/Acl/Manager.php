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
    
    
    public function __construct(Core_Model_User_Interface $user = null) {
        if(!is_null($user)){
            $this->_user = $user;
        } else {
            $this->_user = $_SESSION['User'];
        }
    }
    
    public function setUser(Core_Model_User_Interface $user) {
         $this->_user = $user;
    }
    
    public function getUser(){
        return $this->_user;
    }
    
    private function _getCache(){
        
            if(self::$_resourceCache == null){
            
                $front = Zend_Controller_Front::getInstance();

                $manager = $front->getParam('bootstrap')
                                ->getResource('cachemanager');

                self::$_resourceCache = $manager->getCache('acl-resources');
            }
            return self::$_resourceCache;
    }
    
    private function _getModules(){
        
            if(self::$_modules == null){
                $front = Zend_Controller_Front::getInstance();
                self::$_modules = $front->getControllerDirectory();
            }
            
            return self::$_modules;
    }
    
    /**
     * @return Zend_Acl
     */
    private function _getAcl($module){
        
            if(  empty(self::$_acl[$module])){
                self::$_acl[$module] = new Zend_Acl();
            }
            
            return self::$_acl[$module];
    }
    
     private function _setAcl($module, Zend_Acl $acl){
                self::$_acl[$module] = $acl;
    }
    
    public function getResources(){
        
        $array = array();
        
        $modules = $this->_getModules();
        foreach($modules as $key => $value){
            $array[$key] = $this->getResourcesByModule($key);
        }
        return $array;
    }
    
    public function getResourcesByModule($module){
        
        $modules = $this->_getModules();
        if(empty($modules[$module])){
            throw new NotFoundException('Module '.$module.' was not found');
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
            
            
            
            $cache->save($array,$module);
        } else {
            $array = $cache->load($module);
        }

        return $array;
        
    }
    
    private function _buildAcl(Zend_Acl $acl, array $objects){
//         foreach($objects as $acl){
//             
//             
//             
//             
//                $acl->addResource()
//            }
   }
    
    
    public function getUserAcl($module){
        
        //if User acl been cached
        
            //no
            $acl = $this->getGroupAcl($module);


            //prepare user's parents
            $parents = array();
            foreach($this->_user->memberships as $group){
                $parents[] = 'g'.$group;
            }


            if(!$acl->hasRole('u'.$this->_user->id)){

                $acl->addRole('u'.$this->_user->id, $parents);

                $service = new Core_Model_Acl_User();
                $objects = $service->getObjectsByUserModule($this->_user->id, $module);

                //assumes to have the most nulls at the top
                foreach($objects as $a){

                    if(!is_null($a->controller)){
                        if(!$acl->hasResource('c'.$a->controller)){
                            $acl->addResource('c'.$a->controller);
                        }
                    }
                    
                    if($a->permission == 1){
                        $acl->allow('u'.$this->_user->id, 'c'.$a->controller, $a->action);
                    } else {
                        $acl->deny('u'.$this->_user->id, 'c'.$a->controller, $a->action);
                    }
                } 
            }
            
            //save to cache
         //else   
            //load from cache
            
            
         //endif   
            
            
        $this->_setAcl($module, $acl);
        return $acl;     
        
        //get cache 'u_'.$user.'_'.$module
    }
    
    public function getGroupAcl($module){
        
        //get group list
        $memberships = $this->_user->memberships;
        
        $hash = serialize($memberships);
        
        //get from cache
            $acl = new Zend_Acl();
            $parents = array();
            
            $service = new Core_Model_Acl_Group_Service();
            $objects = $service->getObjectsByGroupsModuleObject($memberships, $module);
            
            foreach($objects as $id->$group){
                
                $acl->addRole('g'.$id);
                $parents[] = 'g'.$id;
            
                foreach($group as $g){

                    if(!is_null($g->controller)){
                        if(!$acl->hasResource('c'.$g->controller)){
                            $acl->addResource('c'.$g->controller);
                        }
                    }

                    if($g->permission == 1){
                        $acl->allow('g'.$this->group, 'c'.$g->controller, $g->action);
                    } else {
                        $acl->deny('u'.$this->_user->id, 'c'.$g->controller, $g->action);
                    }
                } 
            }
        
        
        
        //get cache $group.'_'.$module
            
        
        $this->_setAcl($module,$acl);
        return $acl;
    }
    
    
    public function getGroupAclObjects($module,$controller,$objects){
        //get cache $user.'_'.md5(serilize($objects))
        
        //get base acl
        $acl = $this->getGroupAcl($module);
        
        $service = new Core_Model_Acl_Group_Service();
        $result = $service->getObjectsByGroupsModuleControllerObjects($memberships, $module, $controller, $objects);
 
            if(!$acl->hasResource('c'.$controller)){
                $acl->addResource('c'.$ontroller);
            }
           
            foreach($result as $id->$group){
                
 
                $acl->addResource('c'.$controler.'_'.$object, 'c'.$controler);
            
                foreach($group as $g){

                    if($g->permission == 1){
                        $acl->allow('g'.$this->group, 'c'.$g->controller, $g->action);
                    } else {
                        $acl->deny('u'.$this->_user->id, 'c'.$g->controller, $g->action);
                    }
                } 
            }

    }
    
    public function getUserAclObjects($module,$controller,$objects){
        //get cache $user.'_'.md5(serilize($objects))
        
        //get base acl
        $acl = $this->getUserAcl($module);
        
        $service = new Core_Model_Acl_User_Service();
        $result = $service->getObjectsByGroupsModuleControllerObjects($memberships, $module, $controller, $objects);
 
            if(!$acl->hasResource('c'.$controller)){
                $acl->addResource('c'.$ontroller);
            }
           
            foreach($result as $id->$group){
                
 
                $acl->addResource('c'.$controler.'_'.$object, 'c'.$controler);
            
                foreach($group as $g){

                    if($g->permission == 1){
                        $acl->allow('g'.$this->group, 'c'.$g->controller, $g->action);
                    } else {
                        $acl->deny('u'.$this->_user->id, 'c'.$g->controller, $g->action);
                    }
                } 
            }
        
    }
}