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
 * @subpackage Category
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class Content_Form_Category extends CMS_Form
{

    public function init()
    {
   
    
    //controller will load parent items
    $parent = $this->createElement('select','parent');
    $parent->addMultiOption('','None');
//        $type->setMultiOptions(array('house' => 'House',
//                                     'apartment' => 'Apartment',
//                                     'condo' => 'Condo',
//                                     'other' => 'Other',
//                                     'na' => 'N/A'));
        $parent->setLabel('Parent: ');
       // $parent->setRequired('true');
        $this->addElement($parent);
    
        $title = $this->createElement('text','title');
        $title->setLabel('Title: ');
        $title->setRequired('true');
        $title->addFilter('StripTags');
        $this->addElement($title);
        
        //This needs to be a convert from title special field
        $url = $this->createElement('JsSlug','url');
        $url->setLabel('Url: ');
        $url->setRequired('true');
        $url->addFilter('StripTags');
        $url->setParam('source','#title');
        $url->setParam('separator','_');
        $this->addElement($url);
        
        $description = $this->createElement('textarea','description');
        $description->setLabel('Description: ');
        $description->addFilter('StripTags');
        $description->setAttrib('rows','3');
        $description->setAttrib('cols','20');
        $this->addElement($description);

        $this->addDisplayGroup($this->getElements(), 'category', array('legend' => 'Category'));  
    }
}