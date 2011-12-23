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
class HomeNet_Form_Room extends CMS_Form
{
    private $regions;
    
    public function __construct($regions = array()) {
        $this->regions = $regions;
        parent::__construct();
    }
    
    public function init()
    {
        $service = new HomeNet_Model_House_Service();
       // $options = $service->getRegions($this->regions);
        
        
        
        
        $this->setMethod('post');

        $name = $this->createElement('text','name');
        $name->setLabel('Room Name: ');
        $name->setRequired('true');
        $name->addFilter('StripTags');
        $this->addElement($name);

        $description = $this->createElement('textarea','description');
        $description->setLabel('Room Description: ');
        $description->addFilter('StripTags');
        $description->setAttrib('rows','3');
        $description->setAttrib('cols','20'); 
        $this->addElement($description);


        $region = $this->createElement('select', 'region');
        $region->setLabel("Room is in:");
        
        $region->setMultiOptions($service->getRegions($this->regions));
        
        $region->setRequired('true');
        $this->addElement($region);
/*
        $other->setLabel("Do you also want to include");
        $other = $this->createElement('select', 'role');
        $other->addMultiOption('Basement', 'Basement');
        $other->addMultiOption('Attic', 'Attic');
        $house->addElement($other); */

        $this->addDisplayGroup($this->getElements(), 'room', array ('legend' => 'Room'));
    }

}