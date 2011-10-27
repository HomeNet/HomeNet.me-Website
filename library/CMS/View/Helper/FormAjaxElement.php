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
 * @subpackage View
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class CMS_View_Helper_FormAjaxElement extends Zend_View_Helper_FormElement {

    protected function _prepareArgs($name, $value = null, $attribs = null, $params = null, $options = null) {
        // the baseline info.  note that $name serves a dual purpose;
        // if an array, it's an element info array that will override
        // these baseline values.  as such, ignore it for the 'name'
        // if it's an array.
       
        
        if(!is_array($attribs)){
            $attribs = array();
        }
        if(!is_array($params)){
            $params = array();
        }
         if(!is_array($options)){
            $options = array();
        }
        

        // Set ID for element
        if (empty($attribs['id'])) {
            $attribs['id'] = trim(strtr($name, array('[' => '-', ']' => '')), '-');
        } 

        return array(
            'attribs' => $attribs,
            'params' => $params,
            'options' => $options,
        );
    }

}
