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
class CMS_Form_Element_OptionTextarea extends Zend_Form_Element_Textarea
{
    /**
     * Use formTextarea view helper by default
     * @var string
     */
    public $helper = 'formTextarea';
    
    public function setValue($value){
        
        if(is_array($value)){
            $value = flattenArray($value);
        }
        parent::setValue($value);
}

public function getValue(){
        
        parent::getValue();
    
    if(is_array($value)){
            $value = unflattenArray($value);
        }
        return $value;
}
    
    
    private function flattenArray($array) {

        if(empty($array)){
            return '';
        }

        $strings = array();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $value = implode(',', $value).',';
            }
            $strings[] = $key . ':' . $value;
        }
        return implode("\n", $strings);
    }

 private function unflattenArray($string) {
        if(empty($string)){
            return '';
        }

        $strings = preg_split("`[\n\r]+`", $string);
        $array = array();
        foreach ($strings as $value) {
            $value = explode(':', $value);
            if (strpos($value[1], ',') !== false) {
                $value[1] = explode(',', $value[1]);
                $last = end($value[1]);
                if (empty($last)) {
                    array_pop($value[1]);
                }
            }
            $array[$value[0]] = $value[1];
        }

        return $array;
    }
    
    
}
