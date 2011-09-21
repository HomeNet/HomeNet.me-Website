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
class CMS_View_Helper_Message extends Zend_View_Helper_HtmlElement
{
	/**
	 * Render a Color Picker in an FormText field.
	 *
	 * @link   http://docs.jquery.com/UI/ColorPicker
	 * @param  string $id
	 * @param  string $value
	 * @param  array  $params
	 * @param  array  $attribs
	 * @return string
	 */
    public function message($class, $title, $content = null, $attribs = null)
    {
        $validClasses = array('notice','warning','fatal');
        if(!in_array($class, $validClasses)){
            throw new CMS_Exception('Invalid Message Class: '.$class);
        }

        $uiClass = 'highlight';
        $icon = 'info';

        if($class != 'notice'){
            $uiClass = 'error';
            $icon = 'alert';
        }

        $xhtml = '<div class="ui-widget ui-state-'.$uiClass.' cms-message" >';
        $xhtml .= '<span class="ui-icon ui-icon-'.$icon.'" />';
        $xhtml .= '<strong>'.$title.'</strong>';
        if(!empty($content)){
            $xhtml .= $content;
        }     
        $xhtml .= '</div>';
        return $xhtml;
    }
}