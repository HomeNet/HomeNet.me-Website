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
class CMS_View_Helper_FormJsWysiwyg extends CMS_View_Helper_FormJsElement
{
    public function formJsWysiwyg($name, $value, $attribs = null, $params = null)
    {
        
        extract($this->_prepareArgs($name, $value, $attribs, $params));
      //  $options = array('path','title','description','source','sourceUrl','copyright','owner','fullname', 'size', 'width', 'height', 'cropTop','cropLeft','cropWidth','cropHeight');
        
//        if(empty($params['folder'])){
//            $params['folder'] = '';
//        }
//        
//        $params['hash'] = securityHash($params['folder']);
        
        //add class;
//        $class = 'cms-element-wysiwyg';
//        if(isset($attribs['class'])){
//           $class = ' '.$attribs['class'];
//        } 
        $attribs['name'] = $name;
       // $attribs['class'] = $class;

        $params['name'] = $name;
        //$params['layout'] = 'gallery';
       // $params['types'] = 'images';
        
        $this->view->headLink()->appendStylesheet('/css/jquery.wysiwyg.css');
        $this->view->headScript()->appendFile('/js/mylibs/jquery.imageeditor.js');
        $this->view->headScript()->appendFile('/js/libs/jquery.wrapselection.js');
        $this->view->headScript()->appendFile('/js/libs/jquery.fileupload.js');
        $this->view->headScript()->appendFile('/js/libs/jquery.fileupload-ui.js');
        $this->view->headScript()->appendFile('/js/mylibs/jquery.fileupload.js');
        $this->view->headScript()->appendFile('/js/libs/jquery.iframe-transport.js');
        $this->view->headLink()->appendStylesheet('/css/jquery.fileupload-ui.css');
        $this->view->headScript()->appendFile('/js/mylibs/jquery.filemanager.js');
        
        
        $this->view->headScript()->appendFile('/js/mylibs/jquery.wysiwyg.js');
        $this->view->headScript()->appendFile('/js/mylibs/jquery.wysiwyg.block.js');
        $this->view->headScript()->appendFile('/js/mylibs/jquery.galleryelement.js');
        $this->view->headScript()->appendFile('/js/mylibs/jquery.galleryblockhelper.js');
        
        $this->view->jquery()->addOnLoad("$('#" . $attribs['id'] . "').wysiwyg();");
        
      //  $this->_attachScripts();
        //$this->_htmlAttribs($attribs)
        
        return '<textarea '.$this->_htmlAttribs($attribs).$this->_htmlData($params).'>'.$value.'</textarea>';
    }
    
     protected function _htmlData($values) {
        $data = array();
        foreach ($values as $key => $value) {
            $data[] = 'data-' . $key . '="' . $value . '"';
        }

        return ' ' . implode(' ', $data);
    }
    
}
