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
class Admin_Form_Group extends CMS_Form {

    public function init() {
        
        $name = $this->createElement('text', 'title');
        $name->setLabel('Title: ');
        $name->setRequired('true');
        $this->addElement($name);

        $description = $this->createElement('textarea', 'description');
        $description->setLabel('Description: ');
        $description->addFilter('StripTags');
        $description->setAttrib('rows', '3');
        $description->setAttrib('cols', '20');
        $this->addElement($description);
        
        $visible = $this->createElement('checkbox', 'visible', array('uncheckedValue' => ""));
        $visible->setLabel('Visible on Site: ');
        $this->addElement($visible);
//  
        $this->addDisplayGroup($this->getElements(), 'field', array('legend' => 'Group'));

        $this->addElement('hash', 'hash', array('salt' => 'unique'));
    }

}

