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
class Content_Plugin_Element_DateTime_Element extends Content_Model_Plugin_Element  {

    
  /**
     * get any custom options for the setup of the field type
     * 
     * @return CMS_Sub_Form
     */
    function getSetupForm($options = array()){
        $form = parent::getSetupForm($options);
        $form->setLegend('DateTime Options');
        
        $type = $form->createElement('multiCheckbox', 'target');
        $type->setLabel('Show: ');
        $type->setRequired('true');

        $options = array(
            'year' => 'Year',
            'month' => 'Month',
            'day' => 'Day',
            'hour' => 'Hour',
            'minute' => 'Minute',
            'second' => 'Second',
        );
        $type->setMultiOptions($options);
        $form->addElement($type);
        
        $format = $form->createElement('select', 'format');
        $format->setLabel('Format: ');
        $format->setRequired('true');

        $options = array(
            'YYYY-MM-DD' => '2011-01-26',
        );
        $format->setMultiOptions($options);
        $form->addElement($format);
        
        return $form;
    }
    
    /**
     * Get the form element to display
     * 
     * @param $config config of how object shoiuld be rendered
     * @return Zend
     */
    function getElement(array $config, $options = array()){
        
        $element = new ZendX_JQuery_Form_Element_DatePicker();
        
       // $element = new Zend_Form_Element_Text($field->name); 
        return $element;
    }
}