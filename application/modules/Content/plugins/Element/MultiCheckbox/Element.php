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
abstract class Content_Plugin_Element_MultiCheckbox_Element {

    
    /**
     * get any custom options for the setup of the field type
     * 
     * @return CMS_Sub_Form
     */
    function getSetupForm($options = array()){
        $form = new CMS_Sub_Form();
        
        return $form;
    }
    
    /**
     * Get the form for Inserting data
     * 
     * @param Content_Model_Field $field
     * @return CMS_Form_SubForm 
     */
    function getField(Content_Model_Field $field){
        $form = new CMS_Form_SubForm();
        
        return $form;
    }
    
    /**
     * Parse the Subform and return the value to be stored in the database
     * 
     * @param array $values 
     */
    function getValue($values = array()){
        
    }
}