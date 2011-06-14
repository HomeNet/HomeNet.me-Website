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
 * @subpackage House
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class HomeNet_Form_House extends CMS_Form
{

    public function init()
    {
        $this->setMethod('post');

        $name = $this->createElement('text','name');
        $name->setLabel('Home Name: ');
        $name->setRequired('true');
        $name->addFilter('StripTags');
        $this->addElement($name);

        $description = $this->createElement('textarea','description');
        $description->setLabel('Home Description: ');
        $description->addFilter('StripTags');
        $description->setAttrib('rows','3');
        $description->setAttrib('cols','20');
        $this->addElement($description);

        $location = $this->createElement('text','location');
        $location->setLabel('Location: ');
        $location->setRequired('true');
        $location->addFilter('StripTags');
        $this->addElement($location);

        $type = $this->createElement('select','type');
        $type->setMultiOptions(array('house' => 'House',
                                     'apartment' => 'Apartment',
                                     'condo' => 'Condo',
                                     'other' => 'Other',
                                     'na' => 'N/A'));
        $type->setLabel('Type: ');
        $type->setRequired('true');
        $type->addFilter('StripTags');
        $this->addElement($type);

        $regions = $this->createElement('MultiCheckbox', 'regions');
        $regions->setLabel("Create space for:");
        $regions->setRequired('true');
        $regions->setMultiOptions(array('1' => 'First Floor',
                                       '2' => 'Second Floor',
                                       '3' => 'Third Floor',
                                       '4' => 'Forth Floor',
                                       '5' => 'Fifth Floor',
                                       'B' => 'Basement',
                                       'A' => 'Attic',
                                       'O' => 'Outdoors'));

        //$regions->addDecorator('HtmlTag', array('tag' => 'div', 'style'=>'float:right; width:265px; text-align:left;'));

        $this->addElement($regions);
/*
        $other->setLabel("Do you also want to include");
        $other = $this->createElement('select', 'role');
        $other->addMultiOption('Basement', 'Basement');
        $other->addMultiOption('Attic', 'Attic');
        $house->addElement($other); */

        $this->addDisplayGroup($this->getElements(), 'house', array('legend' => 'House'));

        $this->addElement('hash', 'hash', array('salt' => 'unique'));


       // $this->addSubForm($sub, 'home');

        
    }

}

