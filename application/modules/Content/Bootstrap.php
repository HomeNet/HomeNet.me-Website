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
 * @package Content
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */

//based on http://weierophinney.net/matthew/archives/234-Module-Bootstraps-in-Zend-Framework-Dos-and-Donts.html

class Content_Bootstrap extends Zend_Application_Module_Bootstrap
{
    protected function _initRoute()
    {
       // die(debugArray(Zend_Controller_Front::getInstance()->getRequest()->getParams() ));

        
//        $bootstrap = $this->getApplication();
//        $bootstrap->bootstrap('frontcontroller');
//        $front = $bootstrap->getResource('frontcontroller');
//
//       // $front->registerPlugin(new HomeNet_Plugin_Layout());
//        
//        $front->registerPlugin(new HomeNet_Plugin_Navigation());
//        
    }
}



