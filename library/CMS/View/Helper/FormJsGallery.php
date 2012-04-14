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
class CMS_View_Helper_FormJsGallery extends CMS_View_Helper_FormJsFileManager
{
    public function formJsGallery($name, $values, $attribs = null, $params = null)
    {
        extract($this->_prepareArgs($name, $values, $attribs, $params));
      //  $options = array('path','title','description','source','sourceUrl','copyright','owner','fullname', 'size', 'width', 'height', 'cropTop','cropLeft','cropWidth','cropHeight');
        
        if(!empty($values) && !is_array($values)){
            throw new Exception('invalid value');
        }
        

        
//        if(empty($params['folder'])){
//            $params['folder'] = '';
//        }
//        
//        $params['hash'] = securityHash($params['folder']);
        
        //add class;
        $class = 'cms-element-gallery';
        if(isset($attribs['class'])){
           $class = ' '.$attribs['class'];
        } 
        
        $attribs['class'] = $class;

        $params['name'] = $name;
        $params['layout'] = 'gallery';
        $params['types'] = 'images';
        
        $this->view->headScript()->appendFile('/js/mylibs/jquery.galleryelement.js');
        $this->view->headScript()->appendFile('/js/mylibs/jquery.imageelement.js');
        $this->view->headScript()->appendFile('/js/mylibs/jquery.imageeditor.js');
        
        $this->view->jquery()->addOnLoad("$('#" . $attribs['id'] . "').galleryelement();");
        
        $this->_attachScripts();
        
        return $this->_list($values, $attribs, $params);
    }
    
}
