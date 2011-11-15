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
class CMS_View_Helper_FormJsSlug extends CMS_View_Helper_FormJsElement {

    public function formJsSlug($name, $value = null, $attribs = array(), $params = array()) {

        extract($this->_prepareArgs($name, $value, $attribs, $params));       
        
        //add class;
        $class = 'cms-element-slug';
        if (isset($attribs['class'])) {
            $class = ' ' . $attribs['class'];
        }
        $attribs['class'] = $class;
        
        $this->view->headScript()->appendFile('/js/mylibs/jquery.slugelement.js');

        $this->view->jquery()->addOnLoad("$('#" . $attribs['id'] . "').slugelement();");

        $attribs = $this->_dataAttribs($params,$attribs);
        
        return $this->view->formText($name, $value, $attribs);
    }
}