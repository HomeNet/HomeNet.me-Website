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
class CMS_View_Helper_ImagePath extends Zend_View_Helper_HtmlElement
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
    public function imagePath($source, $width, $height, $type = null)
    {
        $source = strtolower($source);
        $config = Zend_Registry::get('config');
        $salt = $config->site->salt;
        if($type === null){
            $type = $config->site->image->defaultType;
        }

        $hash = imageHash($source, $width, $height, $type);
    
        $array = $config->resources->router->routes->toArray();
        $route = $array['core-image']['route'];
        $find = array(':source',':width',':height',':type',':hash');
        $replace = array($source,$width,$height,$type,$hash);
        $route = '/'.str_replace($find, $replace, $route);
        return $route;
        //return $this->view->url(array('source'=> $source,'width'=>$width, 'height'=>$height, 'type'=>$type, 'hash'=>$hash),'core-image',false,false);
    }
}