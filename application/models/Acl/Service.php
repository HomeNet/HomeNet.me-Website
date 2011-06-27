<?php


/**
 * Description of Service
 *
 * @author mdoll
 */
class Core_Model_Acl_Service {
    
    
    private static $_resourceCache = null;
    private static $_modules = null;
    
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
    
    private function _getAcl(){
        
            if(self::$_acl == null){
               $acl = new Zend_Acl();
                self::$_acl = $acl;
            }
            
            return self::$_acl;
    }
    
    
    public function getUserAcl($user){
        
        
        
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
    
    public function getUserAcl($user,$module){
        //get cache 'u_'.$user.'_'.$module
    }
    
    public function getGroupAcl($group, $module){
        //get cache $group.'_'.$module
    }
    
    public function getGroupAclObject($user, $module,$controller,$object){
        //get cache $user.'_'.md5(serilize($objects))
    }
    
    public function getUserAclObject($user, $module,$controller,$object){
        //check local static object
        
        //
        
    }
    
    public function getGroupAclObjects($user, $module,$controller,$objects){
        //get cache $user.'_'.md5(serilize($objects))
    }
    
    public function getUserAclObjects($user, $module,$controller,$objects){
        //check local static object
        
        //
        
    }
    
    
    
}