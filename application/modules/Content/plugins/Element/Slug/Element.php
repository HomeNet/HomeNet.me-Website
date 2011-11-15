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
class Content_Plugin_Element_Slug_Element extends Content_Model_Plugin_Element {

    
    /**
     * get any custom options for the setup of the field type
     * 
     * @return CMS_Sub_Form
     */
    function getSetupForm($options = array()){
        $form = parent::getSetupForm($options);
        $form->setLegend('Slug Options');
        
        $source = $form->createElement('select', 'source');
        $source->setLabel('Source: ');
        $source->setRequired('true');

        $options = array(
           // 'name' => 'Name',
            '#title' => 'Title',
        );

        $source->setMultiOptions($options);
        $form->addElement($source);
        
        $separator = $form->createElement('select', 'separator');
        $separator->setLabel('Separator: ');
        $separator->setRequired('true');

        $options = array(
            '-' => '-',
            '_' => '_',
            '.' => '.'
        );
        $separator->setMultiOptions($options);
        $form->addElement($separator);
        
        $length = $form->createElement('text', 'length');
        $length->setLabel('Max Length: ');
        $length->setRequired('true');
        $length->setValue(32);
        $length->addFilter('Int');
        $form->addElement($length);

        return $form;
    }
    
    /**
     * Get the form element to display
     * 
     * @param $config config of how object shoiuld be rendered
     * @return Zend
     */
    function getElement(array $config, $options = array()){
        
        $element = new CMS_Form_Element_JsSlug($config); 
        $element->setParams($options);
        return $element;
    }
}