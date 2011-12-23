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
 * @package HomeNet
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */

//based on http://weierophinney.net/matthew/archives/234-Module-Bootstraps-in-Zend-Framework-Dos-and-Donts.html
require_once 'Installer.php';

class HomeNet_Bootstrap extends Zend_Application_Module_Bootstrap
{
    protected function _initPlugins()
    {
        $bootstrap = $this->getApplication();
        $bootstrap->bootstrap('frontcontroller');
        $front = $bootstrap->getResource('frontcontroller');

       // $front->registerPlugin(new HomeNet_Plugin_Layout());
        $front->registerPlugin(new HomeNet_Plugin_Navigation());
        
    }
}


function flattenArray($array) {

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

 function unflattenArray($string) {
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
