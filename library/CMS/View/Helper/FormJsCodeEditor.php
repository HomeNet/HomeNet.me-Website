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
class CMS_View_Helper_FormJsCodeEditor extends CMS_View_Helper_FormJsElement
{
    public function formJsCodeEditor($name, $value, $attribs = null, $params = null)
    {
        $defaultParams = array('lineNumbers' => true,
                                'matchBrackets' => true,
                                'indentUnit' => 8,
                                'indentWithTabs' => true,
                                'enterMode' => "keep",
                                'tabMode' => "shift");
        
        
        $types = array('php');
        //linenumbers

//  die($value);
        extract($this->_prepareArgs($name, $value, $attribs, $params));
      //  $options = array('path','title','description','source','sourceUrl','copyright','owner','fullname', 'size', 'width', 'height', 'cropTop','cropLeft','cropWidth','cropHeight');
        
       
        $params = array_merge($defaultParams, $params);

        
        if(empty($params['type'])){
            $params['type'] = 'php';
        }
        $params['type'] = strtolower($params['type']);
        

        
        //add class;
//        $class = 'cms-element-wysiwyg';
//        if(isset($attribs['class'])){
//           $class = ' '.$attribs['class'];
//        } 
        $attribs['name'] = $name;
     //   $attribs['class'] = $class;


        //$params['layout'] = 'gallery';
       // $params['types'] = 'images';
        $this->view->headScript()->appendFile('/js/libs/jquery.base64.min.js');
        $this->view->headScript()->appendFile('/plugins/codemirror/lib/codemirror.js');
        $this->view->headLink()->appendStylesheet('/plugins/codemirror/lib/codemirror.css');
        $this->view->headLink()->appendStylesheet('/plugins/codemirror/theme/default.css');
        
        switch($params['type']){
            case "php":
                $params['mode'] = 'application/x-httpd-php';
                $this->view->headScript()->appendFile('/plugins/codemirror/mode/xml/xml.js');
                $this->view->headScript()->appendFile('/plugins/codemirror/mode/javascript/javascript.js');
                $this->view->headScript()->appendFile('/plugins/codemirror/mode/css/css.js');
                $this->view->headScript()->appendFile('/plugins/codemirror/mode/clike/clike.js');
                $this->view->headScript()->appendFile('/plugins/codemirror/mode/php/php.js');
                break;
                
            case "html":
                $params['mode'] = 'text/html';
                $params['htmlMode'] = true;
                $params['alignCDATA'] = true;


                $this->view->headScript()->appendFile('/plugins/codemirror/mode/xml/xml.js');
                $this->view->headScript()->appendFile('/plugins/codemirror/mode/javascript/javascript.js');
                $this->view->headScript()->appendFile('/plugins/codemirror/mode/css/css.js');
                break;
        }
        
        unset($params['type']);
           $this->view->jquery()->addOnLoad('var editor = CodeMirror.fromTextArea(document.getElementById("'.$attribs['id'].'"), '.str_replace('\/', '/', Zend_Json::encode($params,false,
    array('enableJsonExprFinder' => true))).");
                   
        var jEditor =  $('#$name');        
        jEditor.parents('form').submit(function(){
                 jEditor.val($.base64.encode(jEditor.val()));

      })


");    

        
        return '<textarea '.$this->_htmlAttribs($attribs).'>'.$value.'</textarea>';
    }
    
}
