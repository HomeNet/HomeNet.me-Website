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
 * @subpackage Template
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class Content_Form_Template extends CMS_Form
{

    public function init()
    {
//        $title = $this->createElement('text','title');
//        $title->setLabel('Title: ');
//        $title->setRequired('true');
//        $title->addFilter('StripTags');
//        $this->addElement($title);
        
        //This needs to be a convert from title special field
        $url = $this->createElement('text','url');
        $url->setLabel('Url: ');
        $url->setRequired('true');
        $url->addFilter('StripTags');
        $this->addElement($url);
        
        $content = $this->createElement('JsCodeEditor','content');
        $content->setLabel('Template: ');
        $content->setAttrib('rows','35');
        $content->setAttrib('cols','110');
        $this->addElement($content);
        
        $layout = $this->createElement('select','layout');
        $layout->setLabel('Layout: ');

        //$layout->setRequired('true');
      //  die('<pre>'.print_r($sets, 1));
        $options = array('' => 'Default');
        
        $reflection = new Core_Model_Reflection_Service();
        $layouts = $reflection->getLayouts();
        
        foreach($layouts as $value){
            $options[$value] = $value;
        }
        
        $layout->setMultiOptions($options);
        $this->addElement($layout);
        
//        $element = $this->createElement('MultiCheckbox','sdfsfsdfsf');
//        $options = array(1=>'test', 2=>'test2');
//        $element->setMultiOptions($options);
//       $element->setValue(array(2));
//        $this->addElement($element);

        $this->addDisplayGroup($this->getElements(), 'category', array('legend' => 'Template'));
    }
}