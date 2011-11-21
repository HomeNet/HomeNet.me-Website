<?php

/*
 * Interface.php
 * 
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
 * along with HomeNet.  If not, see <http ://www.gnu.org/licenses/>.
 */

/**
 *
 * @author Matthew Doll <mdoll at homenet.me>
 */
class Content_Plugin_Element_Textarea_Element extends Content_Model_Plugin_Element {
   
    
    /**
     * get any custom options for the setup of the field type
     * 
     * @return CMS_Sub_Form
     */
    function getSetupForm($options = array()){
        $form = parent::getSetupForm($options);
        $form->setLegend('Textarea Options');
        
        $rows = $form->createElement('text','rows');
        $rows->setLabel('Rows: ');
      //  $rows->setDescription('This is the label that will show up in the form interface');
      //  $rows->setRequired('true');
        $rows->setValue(3);
        $rows->addFilter('Int');
        $form->addElement($rows);
        
//        $cols = $form->createElement('text','cols');
//        $cols->setLabel('Cols: ');
//        $cols->setValue(50);
//     //   $cols->setDescription('This is the label that will show up in the form interface');
//     //   $cols->setRequired('true');
//        $cols->addFilter('Int');
//        $form->addElement($cols);
                
        $path = $form->createElement('text','value');
        $path->setLabel('Starting Value: ');
        $path->setRequired('true');
        $path->addFilter('StripTags');//@todo filter chars
        $form->addElement($path);
        
        return $form;
    }
    
    /**
     * Get the form element to display
     * 
     * @param $config config of how object shoiuld be rendered
     * @return Zend
     */
    function getElement(array $config, $options = array()){
        
        $element = new Zend_Form_Element_Textarea($config); 
        if(isset($options['rows'])){
            $element->setAttrib('rows', $options['rows']);
        }
        $element->setAttrib('cols', '');
        $element->setAttrib('style', 'width:300px');
       
        return $element;
    }
}