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
class CMS_Form_Element_IniTextarea extends Zend_Form_Element_Textarea {

    /**
     * Use formTextarea view helper by default
     * @var string
     */
    public $helper = 'formIniTextarea';
   // protected $_isArray = true;


    
//    public function getFilters() {
//        parent::getFilters();
//    }

    public function getValue() {

        $value = parent::getValue();


            if (empty($value)) {
                $value = array();
            } if (is_string($value) ) { 
                $this->parsed = true;
                $values = parse_ini_string($value);
                $value = array();
                foreach($values as $key => $v){
                     $value = $this->_processKey($value, $key, $v);
                }

        }
        return $value;
    }
    
     /**
     * From Zend Framework
     *
     * @param  array  $config
     * @param  string $key
     * @param  string $value
     * @throws Zend_Config_Exception
     * @return array
     */
    protected function _processKey($config, $key, $value)
    {
        if (strpos($key, '.') !== false) {
            $pieces = explode('.', $key, 2);
            if (strlen($pieces[0]) && strlen($pieces[1])) {
                if (!isset($config[$pieces[0]])) {
                    if ($pieces[0] === '0' && !empty($config)) {
                        // convert the current values in $config into an array
                        $config = array($pieces[0] => $config);
                    } else {
                        $config[$pieces[0]] = array();
                    }
                } elseif (!is_array($config[$pieces[0]])) {
                    /**
                     * @see Zend_Config_Exception
                     */
                    require_once 'Zend/Config/Exception.php';
                    throw new Zend_Config_Exception("Cannot create sub-key for '{$pieces[0]}' as key already exists");
                }
                $config[$pieces[0]] = $this->_processKey($config[$pieces[0]], $pieces[1], $value);
            } else {
                /**
                 * @see Zend_Config_Exception
                 */
                require_once 'Zend/Config/Exception.php';
                throw new Zend_Config_Exception("Invalid key '$key'");
            }
        } else {
            $config[$key] = $value;
        }
        return $config;
    }

}
