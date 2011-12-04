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

/**
 * Description of Widget
 *
 * @author Matthew Doll <mdoll at homenet.me>
 */
class Core_Model_Plugin_Widget {
    /**
     * @var array widget options
     */
    private $_options;
    
    /**
     * Zend View object to use with rendering
     * 
     * @var Zend_View
     */
    protected $view;
    
    public function __construct($config) {
        if (isset($config['options'])) {
            $this->_options = $config['options'];
        }

        if (isset($config['view'])) {
            if (!($config['view'] instanceof Zend_View)) {
                throw new InvalidArgumentException('Invalid Zend View Supplied');
            }

            $this->view = $config['view'];
        } else {
            $this->view = Zend_Registry::get('view');
        }
    }
    
    public function render(){
        return '';
    }
    
    
     public function renderPartial($template, $placeholders = array(),$path)
    {
        $view = $this->cloneView();
        
//        if ((null !== $module) && is_string($module)) {
//            require_once 'Zend/Controller/Front.php';
//            $moduleDir = Zend_Controller_Front::getInstance()->getControllerDirectory($module);
//            if (null === $moduleDir) {
//                require_once 'Zend/View/Helper/Partial/Exception.php';
//                $e = new Zend_View_Helper_Partial_Exception('Cannot render partial; module does not exist');
//                $e->setView($this->view);
//                throw $e;
//            }
//            $viewsDir = dirname($moduleDir) . '/views';
//            $view->addBasePath($viewsDir);
//        } elseif ((null == $model) && (null !== $module)
//            && (is_array($module) || is_object($module)))
//        {
//            $model = $module;
//        }
        
        
        
        
        if($path !== null){
            $view->setScriptPath($path);
        }


//        if (!empty($model)) {
//            if (is_array($model)) {
//                $view->assign($model);
//            } elseif (is_object($model)) {
//                if (null !== ($objectKey = $this->getObjectKey())) {
//                    $view->assign($objectKey, $model);
//                } elseif (method_exists($model, 'toArray')) {
//                    $view->assign($model->toArray());
//                } else {
//                    $view->assign(get_object_vars($model));
//                }
//            }
//        }
        
      //  die(debugArray($view->getScriptPaths())); 
         
        $view->assign($placeholders);

        return $view->render($template);
    }

    /**
     * Clone the current View
     *
     * @return Zend_View_Interface
     */
    public function cloneView()
    {
        $view = clone $this->view;
        $view->clearVars();
        return $view;
    }
    
    
}

?>
