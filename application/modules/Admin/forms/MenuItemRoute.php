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
 * @package Admin
 * @subpackage Menu_Item
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class Admin_Form_MenuItemRoute extends CMS_Form_SubForm
{

    public function init()
    {
        $this->setLegend('Route');
        
        $title = $this->createElement('text','title');
        $title->setLabel('Title: ');
        $title->setRequired('true');
        $title->addFilter('StripTags');
        $this->addElement($title);

        //@todo ajax select onday this will pull more
        $route = $this->createElement('select', 'route');
        $route->setLabel('Route: ');
        $route->setRequired('true');
        
        $service = new Core_Model_Route_Service();
        $objects = $service->getObjects();
        $options = array();
        
        foreach($objects as $object){
            $options[$object->name] = $object->path;
        }

        //$template->addMultiOption('None','');
        $route->setMultiOptions($options);
        $this->addElement($route);
        
        $options = $this->createElement('IniTextarea','options');
        $options->setLabel('Options: ');
        $options->setAttrib('rows','3');
        $options->setAttrib('cols','20');
        $this->addElement($options);  
    }
}

