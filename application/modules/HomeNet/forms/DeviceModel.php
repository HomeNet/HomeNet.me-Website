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
class HomeNet_Form_DeviceModel extends CMS_Form
{

    public function init()
    {

        $this->setMethod('post');




        $status = $this->createElement('select', 'status');
        $status->setLabel("Status: ");
        $status->setRequired('true');
        $status->addMultiOption('-1', 'Inactive');
        $status->addMultiOption('0', 'Test');
        $status->addMultiOption('1', 'Live');
        $this->addElement($status);


        $category = $this->createElement('select', 'category');
        $category->setLabel("Category: ");
        $category->setRequired('true');
        
        $dmService = new HomeNet_Model_DeviceModel_Service();
        $category->addMultiOptions($dmService->getCategories());
        $this->addElement($category);






        $driver = $this->createElement('text','driver');
        $driver->setLabel('Driver: ');
        $driver->setValue('HomeNet_Model_Device_Generic');
        $driver->setRequired('true');
        $driver->addFilter('StripTags');
        $this->addElement($driver);


        $name = $this->createElement('text','name');
        $name->setLabel('Name: ');
        $name->setRequired('true');
        $name->addFilter('StripTags');
        $this->addElement($name);

        $description = $this->createElement('textarea','description');
        $description->setLabel('Description: ');
        $description->addFilter('StripTags');
        $description->setAttrib('rows','3');
        $description->setAttrib('cols','20'); 
        $this->addElement($description);

        $image = $this->createElement('text','image');
        $image->setLabel('Image: ');
        //$image->setRequired('true');
        $this->addElement($image);


        $settings = $this->createElement('textarea','settings');
        $settings->setLabel('Settings: ');
        $settings->addFilter('StripTags');
        $settings->setAttrib('rows','10');
        $settings->setAttrib('cols','20'); 
        $this->addElement($settings);

        $this->addDisplayGroup($this->getElements(), 'room', array ('legend' => 'Device Model'));

    }

}