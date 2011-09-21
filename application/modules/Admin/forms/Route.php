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
 * @subpackage Route
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class Admin_Form_Route extends CMS_Form {

    public function init() {
        //@todo change this to a form type that can show more details
        $type = $this->createElement('select', 'type');
        $type->setLabel('Type: ');
        $type->setRequired('true');

        $options = array(
            'Routes' => 'Typical Route',
            'Static' => 'Static Route',
            'Regex' => 'Regex Route'
        );

        //$template->addMultiOption('None','');
        $type->setMultiOptions($options);
        $this->addElement($type);

        $name = $this->createElement('text', 'name');
        $name->setLabel('Name: ');
        $name->setRequired('true');
        $name->addFilter('StringToLower');
        $this->addElement($name);

        $path = $this->createElement('text', 'path');
        $path->setLabel('Path: ');
        $path->setRequired('true');
        $path->addFilter('StripTags');
        $this->addElement($path);

        $module = $this->createElement('text', 'module');
        $module->setLabel('Module: ');
        //$module->setRequired('true');
        $module->addFilter('StripTags');
        $this->addElement($module);

        $controller = $this->createElement('text', 'controller');
        $controller->setLabel('Controller: ');
        //$controller->setRequired('true');
        $controller->addFilter('StripTags');
        $this->addElement($controller);

        $action = $this->createElement('text', 'action');
        $action->setLabel('Action: ');
        //$action->setRequired('true');
        $action->addFilter('StripTags');
        $this->addElement($action);

        $options = $this->createElement('IniTextarea', 'options');
        $options->setLabel('Options: ');
        $options->addFilter('StripTags');
        $options->setAttrib('rows', '3');
        $options->setAttrib('cols', '20');
        $this->addElement($options);

        $order = $this->createElement('text', 'order');
        $order->setLabel('Order: ');
        $order->setRequired('true');
        $order->addFilter('StripTags');
        $this->addElement($order);
        
        $active = $this->createElement('checkbox', 'active', array('uncheckedValue' => ""));
        $active->setLabel('Active: ');
        $this->addElement($active);
//  


        $this->addDisplayGroup($this->getElements(), 'field', array('legend' => 'Section'));

        $this->addElement('hash', 'hash', array('salt' => 'unique'));
    }

}

