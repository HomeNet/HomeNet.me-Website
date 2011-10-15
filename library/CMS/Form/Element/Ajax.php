<?php

/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Element
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
/** Zend_Form_Element_Xhtml */
require_once 'Zend/Form/Element/Xhtml.php';

/**
 * Textarea form element
 *
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Element
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Textarea.php 23775 2011-03-01 17:25:24Z ralph $
 */
class CMS_Form_Element_Ajax extends Zend_Form_Element {
    
//    private $_options2 = array();
//
//    public function setOptions(array $options) {
//        $this->_options2 = $options;
//    }
//
//    public function getOptions() {
//        return $this->_options2;
//    }
//
//    public function setOption($key, $value) {
//        $this->_options2[$key] = $value;
//    }
//
//    public function getOption($key) {
//        return $this->options2[$key];
//    }

    private $_params = array();

    public function setParams(array $params) {
        $this->_params = $params;
    }

    public function getParams() {
        return $this->_params;
    }

    public function setParam($key, $value) {
        $this->_params[$key] = $value;
    }

    public function getParam($key) {
        return $this->params[$key];
    }
   
   
   /**
     * Load default decorators
     *
     * @return Zend_Form_Element
     */
    public function loadDefaultDecorators()
    {
        
        
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return $this;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $getId = create_function('$decorator',
                                     'return $decorator->getElement()->getId()
                                             . "-element";');
            $this->addDecorator('AjaxHelper')
                 ->addDecorator('Errors')
                 ->addDecorator('Description', array('tag' => 'p', 'class' => 'description'))
                 ->addDecorator('HtmlTag', array('tag' => 'dd',
                                                 'id'  => array('callback' => $getId)))
                 ->addDecorator('Label', array('tag' => 'dt'));
        }
        return $this;
    }
}
