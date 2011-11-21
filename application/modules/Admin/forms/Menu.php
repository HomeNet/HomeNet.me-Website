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
 * @subpackage Menu
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class Admin_Form_Menu extends CMS_Form
{

    public function init()
    {
        $this->setMethod('post');
        
        //id 	type 	package 	title 	visible
        
        $title = $this->createElement('text','title');
        $title->setLabel('Title: ');
        $title->setRequired('true');
        $title->addFilter('StripTags');
        $this->addElement($title);
        
        $active = $this->createElement('checkbox', 'visible', array('uncheckedValue' => ""));
        $active->setLabel('Visible: ');
        $this->addElement($active);
        
        $this->addDisplayGroup($this->getElements(), 'display_group', array('legend' => 'Menu'));

        $this->addElement('hash', 'hash', array('salt' => 'unique'));
    }


}

