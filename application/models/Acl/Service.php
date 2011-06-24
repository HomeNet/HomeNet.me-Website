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
    
    
    public function getUserAcl($user){
        
        
        
    }
    
    public function getResources(){
        
        $array = array();
        
        $modules = $this->_getModules();
        foreach($modules as $key => $value){
            $array[$key] = $this->getResourcesByModule($key);
        }
        return $array;
        
        
        
       
        
        
        //from stack overflow
        $front = $this->getFrontController();
        $acl = array();

        

        
        
        
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
        
        
        
        $front = $this->getFrontController();
        $acl = array();
        
        foreach ($front->getControllerDirectory() as $module => $path) {

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

                                $acl[$module][$controller] = $actions;
                        }
                }
        }
        
    }
    
    
    
}