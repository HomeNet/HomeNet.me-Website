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
class CMS_View_Helper_FormJsFileManager extends CMS_View_Helper_FormJsElement {

    public function formJsFileManager($name, $value = null, $attribs = null, $params = null, $options = null) {
        extract($this->_prepareArgs($name, $value, $attribs, $params, $options));

        //  $options = array('path','title','description','source','sourceUrl','copyright','owner','fullname', 'size', 'width', 'height', 'cropTop','cropLeft','cropWidth','cropHeight');

        if (!empty($value) && !is_array($value)) {
            throw new Exception('invalid value');
        }
        
//        if(empty($params['folder'])){
//            $params['folder'] = '';
//        }

        //add class;
        $class = 'cms-element-filemanager';
        if (isset($attribs['class'])) {
            $class = ' ' . $attribs['class'];
        }
        $attribs['class'] = $class;

        $params['name'] = $name;
        $params['layout'] = 'gallery';
        $params['types'] = 'any';

        $this->_attachScripts();

        return $this->_list(array($values), $attribs, $params);
    }

    protected function _attachScripts() {
        $this->view->headLink()->appendStylesheet('/css/jquery.filemanager.css');
        
        $this->view->headLink()->appendStylesheet('/css/jquery.fileupload-ui.css');
        $this->view->headScript()->appendFile('/js/libs/jquery.iframe-transport.js');
        $this->view->headScript()->appendFile('/js/libs/jquery.fileupload.js');
        $this->view->headScript()->appendFile('/js/libs/jquery.fileupload-ui.js');
        $this->view->headScript()->appendFile('/js/mylibs/jquery.fileupload.js');
        $this->view->headScript()->appendFile('/js/mylibs/jquery.filemanager.js');
        
    }

    protected function _list($values, $attribs, $params) {
        $xhtml = '<div' . $this->_htmlAttribs($attribs) . $this->_htmlData($params) . '>';
        if (!is_null($values) && is_array($values)) {

            foreach ($values as $value) {
                $xhtml .= $this->_listItem($value);
            }
        }
        $xhtml .= '</div>';
        return $xhtml;
    }

    protected function _listItem($value) {

        if (empty($value['path'])) {
            return '';
        }

        //    if($value['type'] == 'image'){
        $value['thumbnail'] = $this->view->imagePath($value['path'], 100, 75);
        $value['preview'] = $this->view->imagePath($value['path'], 480, 320);
        //    }

        return '<div' . $this->_htmlData($value) . '></div>';
    }

    protected function _htmlData($values) {
        $data = array();
        foreach ($values as $key => $value) {
            $data[] = 'data-' . $key . '="' . $value . '"';
        }

        return ' ' . implode(' ', $data);
    }
    
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
        if(empty($params['folder'])){
            $params['folder'] = '';
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
