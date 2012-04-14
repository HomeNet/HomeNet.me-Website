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
class Content_Plugin_Element_FileManager_Element  extends Content_Model_Plugin_Element  {

    public $isArray = true;
    
    
    
    
    
    /**
     * get any custom options for the setup of the field type
     * 
     * @return CMS_Sub_Form
     */
    function getSetupForm($options = array()){
         $form = parent::getSetupForm($options);
        $form->setLegend('Image Options');
        $path = $form->createElement('text','folder');
        $path->setLabel('Folder: ');
        $path->setDescription('Path is Prefixed with: '.APPLICATION_ROOT);
        $path->setRequired('true');
        $path->addFilter('StripTags');//@todo filter chars
        $form->addElement($path);
        
        //@todo rename files?
        //@public/private?
        //@todo default sizes
        
        return $form;
    }
    
    /**
     * Get the form element to display
     * 
     * @param $config config of how object shoiuld be rendered
     * @return Zend
     */
    function getElement(array $config, $options = array()){
        
        $element = new Zend_Form_Element_Text($config); 
        return $element;
    }
    
    /**
     * Parse the Subform and return the value to be stored in the database
     * 
     * @param array $values 
     */
    function getValue($values = array()){
        
    }
}