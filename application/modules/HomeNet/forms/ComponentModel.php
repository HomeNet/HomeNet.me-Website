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
 * @subpackage Room
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class HomeNet_Form_ComponentModel extends CMS_Form {

    public function init() {

        $this->setMethod('post');

        $status = $this->createElement('select', 'status');
        $status->setLabel("Status: ");
        $status->setRequired('true');
        $status->addMultiOption('-1', 'Inactive');
        $status->addMultiOption('0', 'Test');
        $status->addMultiOption('1', 'Live');
        $this->addElement($status);

        $driver = $this->createElement('text', 'plugin');
        $driver->setLabel('plugin: ');
        $driver->setValue('HomeNet_Model_Component_Generic');
        $driver->setRequired('true');
        $driver->addFilter('StripTags');
        $this->addElement($driver);


        $name = $this->createElement('text', 'name');
        $name->setLabel('Name: ');
        $name->setRequired('true');
        $name->addFilter('StripTags');
        $this->addElement($name);

        $description = $this->createElement('textarea', 'description');
        $description->setLabel('Description: ');
        $description->addFilter('StripTags');
        $description->setAttrib('rows', '3');
        $description->setAttrib('cols', '20');
        $this->addElement($description);
/*
        $datatype = $this->createElement('select', 'datatype');
        $datatype->setLabel("Datatype: ");
        $datatype->setRequired('true');
        $datatype->addMultiOptions(array(
            0 => 'CUSTOM',
            1 => 'BYTE',
            2 => 'STRING',
            3 => 'INT',
            4 => 'FLOAT',
            5 => 'LONG',
            6 => 'BINARY',
            7 => 'BOOLEAN',
            10 => 'RAW'));

        $this->addElement($datatype);

        $units = $this->createElement('text', 'units');
        $units->setLabel('Units: ');
        $units->addFilter('StripTags');
        $this->addElement($units);
*/
        $settings = $this->createElement('textarea', 'settings');
        $settings->setLabel('Settings: ');
        $settings->addFilter('StripTags');
        $settings->setAttrib('rows', '10');
        $settings->setAttrib('cols', '20');
        $this->addElement($settings);

//        $color = $this->createElement('simpleColorPicker', 'color');
//        $color->setLabel('Color: ');
//        $color->setRequired('true');
//        $this->addElement($color);


        $this->addDisplayGroup($this->getElements(), 'room', array('legend' => 'Component Model'));
    }

}